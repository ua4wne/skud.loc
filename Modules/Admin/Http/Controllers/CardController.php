<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Card;
use Modules\Admin\Entities\Device;
use Modules\Admin\Entities\Task;
use Modules\Admin\Entities\TimeZone;
use Validator;

class CardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin') && !User::hasRole('control')){
            abort(503);
        }
        if(view()->exists('admin::cards')){
            $rows = Card::all();
            $devices = Device::all();
            $devsel = array();
            foreach ($devices as $val) {
                $devsel[$val['id']] = $val['full_type']; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Карты доступа',
                'head' => 'Карты доступа',
                'rows' => $rows,
                'devsel' => $devsel,
            ];
            return view('admin::cards',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!User::hasRole('admin') && !User::hasRole('control')){
            abort(503);
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'unique' => 'Значение поля должно быть уникальным!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'code' => 'required|string|max:20|unique:cards',
                'granted' => 'required|integer',
                'flags' => 'nullable|integer',
                'share' => 'required|integer',
                'time_zone_id' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('cardAdd')->withErrors($validator)->withInput();
            }
            if(empty($input['flags']))
                $input['flags'] = 0;

            $card = new Card();
            $card->fill($input);
            $card->created_at = date('Y-m-d H:i:s');
            if($card->save()){
                $this->addCard($card); //задание на загрузку в контроллеры
                $msg = 'Карта доступа '. $input['code'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('cards')->with('status',$msg);
            }
        }
        if(view()->exists('admin::card_add')){
            $zones = TimeZone::all();
            $zonesel = array();
            foreach ($zones as $val) {
                $zonesel[$val['id']] = 'Зона №' . $val['zone'].' ('.$val['text'].')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Карты доступа',
                'head' => 'Новая запись',
                'zonesel' => $zonesel,
            ];
            return view('admin::card_add', $data);
        }
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id,Request $request)
    {
        if(!User::hasRole('admin') && !User::hasRole('control')){
            abort(503);
        }
        $model = Card::find($id);
        if($request->isMethod('delete')){
            $code = $model->code;
            $model->delete();
            //удаляем карту из всех контроллеров
            $this->delCard($code);
            $msg = 'Карта доступа '. $code .' была удалена из системы!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('cards')->with('status',$msg);
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input, [
                'granted' => 'required|integer',
                'flags' => 'nullable|integer',
                'share' => 'required|integer',
                'time_zone_id' => 'required|integer',
            ], $messages);
            if ($validator->fails()) {
                return redirect()->route('cardEdit', ['id' => $id])->withErrors($validator)->withInput();
            }
            if (empty($input['flags']))
                $input['flags'] = 0;
            $model->fill($input);
            $model->update();
            $msg = 'Данные карты доступа ' . $model->code . ' были обновлены!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('cards')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('admin::card_edit')) {
            $zones = TimeZone::all();
            $zonesel = array();
            foreach ($zones as $val) {
                $zonesel[$val['id']] = 'Зона №' . $val['zone'].' ('.$val['text'].')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Карты доступа',
                'head' => 'Карта доступа '.$model->code,
                'data' => $old,
                'zonesel' => $zonesel,
            ];
            return view('admin::card_edit', $data);
        }
        abort(404);
    }

    public function load($id){
        $card = Card::find($id);
        $this->addCard($card);
        $msg = 'Задания на загрузку карты доступа ' . $card->code . ' в контроллеры были созданы!';
        return redirect()->route('cards')->with('status', $msg);
    }

    public function tasks(Request $request){
        if(!User::hasRole('admin') && !User::hasRole('control')){
            return 'ERR';
        }
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            if($input['task']=='load'){
                $cards = Card::where(['granted'=>1])->get();
                foreach ($cards as $card){
                    //формируем команду для записи карты в контроллер
                    $msg = new \stdClass();
                    $msg->id = rand();
                    $msg->operation = 'add_cards';
                    $code = dechex($card->code);
                    while(strlen($code)<12){
                        $code = '0'.$code;
                    }
                    if(empty($card->flags))
                        $flags = 0;
                    else
                        $flags = $card->flags;
                    $tz = $card->zone;
                    $msg->cards = array();
                    $msg->cards[0] = ['card'=>strtoupper($code), 'flags'=>$flags,'tz'=>$tz];
                    $task = new Task();
                    $task->device_id = $input['device_id'];
                    $task->json = json_encode($msg);
                    $task->status = 0;
                    $task->created_at = date('Y-m-d H:m:s');
                    ($task->save());
                }
                $msg = 'Задания на добавление карт доступа в контроллер СКУД ' . $task->device->type . ' созданы!';
                //вызываем event
                event(new AddEventLog('info', Auth::id(), $msg));
                return 'OK';
            }
            if($input['task']=='del'){
                //формируем команду для записи карты в контроллер
                $msg = new \stdClass();
                $msg->id = rand();
                $msg->operation = 'clear_cards';
                $task = new Task();
                $task->device_id = $input['device_id'];
                $task->json = json_encode($msg);
                $task->status = 0;
                $task->created_at = date('Y-m-d H:m:s');
                $task->save();
                $msg = 'Задание на удаление карт доступа из контроллера СКУД ' . $task->device->type . ' создано!';
                //вызываем event
                event(new AddEventLog('info', Auth::id(), $msg));
                return 'OK';
            }
            return 'ERR';
        }
    }

    private function delCard($code){
        //создаем задание на удаление карты из всех контроллеров
        $devices = Device::all();
        foreach ($devices as $row){
            $msg = new \stdClass();
            $msg->id = rand();
            $msg->operation = 'del_cards';
            $msg->cards = array();
            $card = dechex($code);
            while(strlen($card)<12){
                $card = '0'.$card;
            }
            $msg->cards[0] = strtoupper($card); //символы в верхний регистр
            $task = new Task();
            $task->device_id = $row->id;
            $task->json = json_encode($msg);
            $task->status = 0;
            $task->created_at = date('Y-m-d H:m:s');
            if($task->save()){
                $msg = 'Задание на удаление карты доступа '.$code.' из контроллера СКУД ' . $task->device->type . ' создано!';
                //вызываем event
                event(new AddEventLog('info', Auth::id(), $msg));
            }
        }
    }

    private function addCard(Card $card){
        if($card->granted==0) return;
        //создаем задание на добавление активной карты во все контроллеры
        $devices = Device::all();
        foreach ($devices as $row){
            $msg = new \stdClass();
            $msg->id = rand();
            $msg->operation = 'add_cards';
            $code = dechex($card->code);
            while(strlen($code)<12){
                $code = '0'.$code;
            }
            $flags = $card->flags;
            $tz = $card->time_zone->zone;
            $msg->cards = array();
            $msg->cards[0] = ['card'=>strtoupper($code), 'flags'=>$flags,'tz'=>$tz];
            $task = new Task();
            $task->device_id = $row->id;
            $task->json = json_encode($msg);
            $task->status = 0;
            $task->created_at = date('Y-m-d H:m:s');
            if($task->save()){
                $msg = 'Задание на добавление карты доступа '.$card->code.' в контроллер СКУД ' . $task->device->type . ' создано!';
                //вызываем event
                event(new AddEventLog('info', Auth::id(), $msg));
            }
        }
    }
}
