<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\EventType;
use Validator;

class EventTypeController extends Controller
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
        if(view()->exists('admin::event_types')){
            $rows = EventType::all(); //all();
            $data = [
                'title' => 'Виды событий',
                'head' => 'Виды событий',
                'rows' => $rows,
            ];
            return view('admin::event_types',$data);
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
            ];
            $validator = Validator::make($input,[
                'code' => 'required|integer|unique:event_types',
                'text' => 'required|string|max:70',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('evtypeAdd')->withErrors($validator)->withInput();
            }

            $type = new EventType();
            $type->fill($input);
            $type->created_at = date('Y-m-d H:i:s');
            if($type->save()){
                $msg = 'Тип события с кодом '. $input['code'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('event-types')->with('status',$msg);
            }
        }
        if(view()->exists('admin::event_type_add')){
            $data = [
                'title' => 'Виды событий',
                'head' => 'Новая запись',
            ];
            return view('admin::event_type_add', $data);
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
        $model = EventType::find($id);
        if($request->isMethod('delete')){
            $model->delete();
            $msg = 'Вид события с кодом '. $model->code .' был удален!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('event-types')->with('status',$msg);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'text' => 'required|string|max:70',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('evtypeEdit',['id'=>$id])->withErrors($validator)->withInput();
            }

            $model->fill($input);
            $model->update();
            $msg = 'Вид события с кодом ' . $model->code . ' был обновлен!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('event-types')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('admin::event_type_edit')) {
            $data = [
                'title' => 'Виды событий',
                'head' => 'Код события '.$model->code,
                'data' => $old,
            ];
            return view('admin::event_type_edit', $data);
        }
        abort(404);
    }
}
