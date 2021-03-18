<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\TimeZone;
use Validator;

class TimeZoneController extends Controller
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
        if(view()->exists('admin::time_zones')){
            $rows = TimeZone::paginate(env('PAGINATION_SIZE')); //all();
            $data = [
                'title' => 'Временные зоны',
                'head' => 'Временные зоны',
                'rows' => $rows,
            ];
            return view('admin::time_zones',$data);
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
                'zone' => 'required|integer|unique:time_zones',
                'begin' => 'required|string|max:8',
                'end' => 'required|string|max:8',
                'days' => 'required|string|max:8',
                'text' => 'nullable|string|max:255',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('tzoneAdd')->withErrors($validator)->withInput();
            }

            $zone = new TimeZone();
            $zone->fill($input);
            $zone->created_at = date('Y-m-d H:i:s');
            if($zone->save()){
                $msg = 'Временная зона '. $input['zone'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('time-zones')->with('status',$msg);
            }
        }
        if(view()->exists('admin::time_zone_add')){
            $data = [
                'title' => 'Временные зоны',
                'head' => 'Новая запись',
            ];
            return view('admin::time_zone_add', $data);
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
        $model = TimeZone::find($id);
        if($request->isMethod('delete')){
            $model->delete();
            $msg = 'Временная зона '. $model->zone .' была удалена!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('time-zones')->with('status',$msg);
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'begin' => 'required|string|max:8',
                'end' => 'required|string|max:8',
                'days' => 'required|string|max:8',
                'text' => 'nullable|string|max:255',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('tzoneEdit',['id'=>$id])->withErrors($validator)->withInput();
            }

            $model->fill($input);
            $model->update();
            $msg = 'Временная зона ' . $model->zone . ' обновлена!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('time-zones')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('admin::time_zone_edit')) {
            $data = [
                'title' => 'Временные зоны',
                'head' => 'Зона №'.$model->zone,
                'data' => $old,
            ];
            return view('admin::time_zone_edit', $data);
        }
        abort(404);
    }
}
