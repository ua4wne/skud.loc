<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Device;
use Modules\Admin\Entities\Task;
use Modules\Admin\Entities\TimeZone;
use Validator;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if(view()->exists('admin::devices')){
            $rows = Device::paginate(env('PAGINATION_SIZE')); //all();
            $zones = TimeZone::all();
            $zonesel = array();
            foreach ($zones as $val) {
                $zonesel[$val['id']] = 'Зона №' . $val['zone'].' ('.$val['text'].')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Контроллеры',
                'head' => 'Контроллеры СКУД',
                'rows' => $rows,
                'zonesel' => $zonesel,
            ];
            return view('admin::devices',$data);
        }
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        if(!User::hasRole('admin')){
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
                'mimes' => 'К загрузке разрешены только графические файлы с расширением jpeg,bmp,png!'
            ];
            $validator = Validator::make($input,[
                'type' => 'required|string|max:10',
                'snum' => 'required|string|max:10|unique:devices',
                'fware' => 'nullable|string|max:10',
                'conn_fw' => 'nullable|string|max:10',
                'image' => 'nullable|max:1024|file|mimes:jpeg,bmp,png',
                'text' => 'nullable|string|max:255',
                'is_active' => 'required|integer',
                'mode' => 'required|integer',
                'time_zone_id' => 'required|integer',
                'address' => 'required|ip',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('deviceAdd')->withErrors($validator)->withInput();
            }
            $input['image'] = '/images/noimage.jpg';
            if ($request->hasFile('image')) {
                $file = $request->file('image'); //загружаем файл
                $name = $file->getClientOriginalName();
                $filename = substr(md5(microtime() . rand(0, 9999)), 0, 20);
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $input['image'] = '/images/'.$filename.'.'.$extension;
                $file->move(public_path() . '/images', $input['image']);
            }
            $device = new Device();
            $device->fill($input);
            $device->created_at = date('Y-m-d H:i:s');
            if($device->save()){
                $this->setMode($device->id,$device->mode); //задание на установку режима работы
                $this->setTimeZone($device->id,$device->time_zone_id); //задание на установку временной зоны
                $msg = 'Контроллер СКУД '. $input['type'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('devices')->with('status',$msg);
            }
        }
        if(view()->exists('admin::device_add')){
            $zones = TimeZone::all();
            $zonesel = array();
            foreach ($zones as $val) {
                $zonesel[$val['id']] = 'Зона №' . $val['zone'].' ('.$val['text'].')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Контроллеры СКУД',
                'head' => 'Новая запись',
                'zonesel' => $zonesel,
            ];
            return view('admin::device_add', $data);
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
        if(!User::hasRole('admin')){
            abort(503);
        }
        $model = Device::find($id);
        if($request->isMethod('delete')){
            $model->delete();
            $msg = 'Контроллер СКУД '. $model->type .' был удален из системы!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('devices')->with('status',$msg);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
                'mimes' => 'К загрузке разрешены только графические файлы с расширением jpeg,bmp,png!'
            ];
            $validator = Validator::make($input,[
                'type' => 'required|string|max:10',
                'fware' => 'nullable|string|max:10',
                'conn_fw' => 'nullable|string|max:10',
                'image' => 'nullable|max:1024|file|mimes:jpeg,bmp,png',
                'text' => 'nullable|string|max:255',
                'is_active' => 'required|integer',
                'mode' => 'required|integer',
                'time_zone_id' => 'required|integer',
                'address' => 'required|ip',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('deviceEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            if ($request->hasFile('image')) {
                //сначала удаляем старый файл, если он не по-умолчанию
                if($model->image != '/images/noimage.jpg')
                    unlink(public_path() . $model->image); //удаляем старый файл
                $file = $request->file('image'); //загружаем файл
                $name = $file->getClientOriginalName();
                $filename = substr(md5(microtime() . rand(0, 9999)), 0, 20);
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $input['image'] = '/images/'.$filename.'.'.$extension;
                $file->move(public_path() . '/images', $input['image']);
            }
            if(empty($input['image']))
                $input['image'] = $model->image;
            $model->fill($input);
            $model->update();
            $this->setMode($model->id,$model->mode); //задание на установку режима работы
            $this->setTimeZone($model->id,$model->time_zone_id); //задание на установку временной зоны
            $msg = 'Данные контроллера СКУД ' . $model->type . ' были обновлены!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('devices')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('admin::device_edit')) {
            $zones = TimeZone::all();
            $zonesel = array();
            foreach ($zones as $val) {
                $zonesel[$val['id']] = 'Зона №' . $val['zone'].' ('.$val['text'].')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Контроллеры СКУД',
                'head' => 'Контроллер СКУД '.$model->type,
                'data' => $old,
                'zonesel' => $zonesel,
            ];
            return view('admin::device_edit', $data);
        }
        abort(404);
    }

    public function clearCard($id){
        $msg = new \stdClass();
        $msg->id = rand();
        $msg->operation = 'clear_cards';
        $task = new Task();
        $task->device_id = $id;
        $task->json = json_encode($msg);
        $task->status = 0;
        $task->created_at = date('Y-m-d H:m:s');
        if($task->save()){
            $msg = 'Задание на удаление карт из контроллера СКУД ' . $task->device->type . ' созданы!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('devices')->with('status', $msg);
        }
    }

    private function setMode($id,$mode){
        $msg = new \stdClass();
        $msg->id = rand();
        $msg->operation = 'set_mode';
        $msg->mode = (int)$mode;
        $task = new Task();
        $task->device_id = $id;
        $task->json = json_encode($msg);
        $task->status = 0;
        $task->created_at = date('Y-m-d H:m:s');
        $task->save();
    }

    private function setTimeZone($id,$zone_id){
        $tzone = TimeZone::find($zone_id);
        $msg = new \stdClass();
        $msg->id = rand();
        $msg->operation = 'set_timezone';
        $msg->zone = $tzone->zone;
        $msg->begin = $tzone->begin;
        $msg->end = $tzone->end;
        $msg->days = $tzone->days;
        $task = new Task();
        $task->device_id = $id;
        $task->json = json_encode($msg);
        $task->status = 0;
        $task->created_at = date('Y-m-d H:m:s');
        $task->save();
    }
}
