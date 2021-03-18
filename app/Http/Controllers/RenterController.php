<?php

namespace App\Http\Controllers;

use App\Events\AddEventLog;
use App\Models\Renter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class RenterController extends Controller
{
    public function index()
    {
        if (!Role::granted('view_refs')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('renters')){
            $rows = Renter::all();
            $data = [
                'title' => 'Организации',
                'head' => 'Организации',
                'rows' => $rows,
            ];
            return view('renters',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if (!Role::granted('edit_refs')) {//вызываем event
            $msg = 'Попытка создания новой организации!';
            event(new AddEventLog('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание организаций!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'title' => 'required|string|max:100',
                'area' => 'required|string|max:50',
                'agent' => 'required|string|max:70',
                'status' => 'required|integer',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|string|max:70',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('renterAdd')->withErrors($validator)->withInput();
            }

            $renter = new Renter();
            $renter->fill($input);
            if($renter->save()){
                $msg = 'Организация '. $input['title'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('renters')->with('status',$msg);
            }
        }
        if(view()->exists('renter_add')){
            $data = [
                'title' => 'Организации',
                'head' => 'Новая запись',
            ];
            return view('renter_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request)
    {
        $model = Renter::find($id);
        $name = $model->title;
        if($request->isMethod('delete')){
            if (!Role::granted('delete_refs')) {
                $msg = 'Попытка удаления организации ' . $name . ' из справочника.';
                event(new AddEventLog('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Организация '. $name .' была удалена!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('renters')->with('status',$msg);
        }
        if (!Role::granted('edit_refs')) {
            $msg = 'Попытка редактирования организации ' . $name . ' в справочнике.';
            //вызываем event
            event(new AddEventLog('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование записи!');
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
                'title' => 'required|string|max:100',
                'area' => 'required|string|max:50',
                'agent' => 'required|string|max:70',
                'status' => 'required|integer',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|string|max:70',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('renterEdit',['id'=>$id])->withErrors($validator)->withInput();
            }

            $model->fill($input);
            $model->update();
            $msg = 'Организация ' . $name . ' была обновлена!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('renters')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('renter_edit')) {
            $data = [
                'title' => 'Организации',
                'head' => $name,
                'data' => $old,
            ];
            return view('renter_edit', $data);
        }
        abort(404);
    }
}
