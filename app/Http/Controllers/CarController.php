<?php

namespace App\Http\Controllers;

use App\Events\AddEventLog;
use App\Models\Car;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class CarController extends Controller
{
    public function index()
    {
        if (!Role::granted('view_refs')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('cars')){
            $rows = Car::all();
            $data = [
                'title' => 'Марки ТС',
                'head' => 'Марки машин',
                'rows' => $rows,
            ];
            return view('cars',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if (!Role::granted('edit_refs')) {//вызываем event
            $msg = 'Попытка создания новой записи в справочнике cars!';
            event(new AddEventLog('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на создание записей в справочниках!');
        }
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'unique' => 'Значение поля должно быть уникальным!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'text' => 'required|string|max:50|unique:cars',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('carAdd')->withErrors($validator)->withInput();
            }

            $car = new Car();
            $car->fill($input);
            if($car->save()){
                $msg = 'Марка ТС '. $input['text'] .' была успешно добавлена!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('cars')->with('status',$msg);
            }
        }
        if(view()->exists('car_add')){
            $data = [
                'title' => 'Марки ТС',
                'head' => 'Новая запись',
            ];
            return view('car_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request)
    {
        $model = Car::find($id);
        $name = $model->text;
        if($request->isMethod('delete')){
            if (!Role::granted('delete_refs')) {
                $msg = 'Попытка удаления марки ТС ' . $name . ' из справочника.';
                event(new AddEventLog('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Марка ТС '. $name .' была удалена!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('cars')->with('status',$msg);
        }
        if (!Role::granted('edit_refs')) {
            $msg = 'Попытка редактирования марки ТС ' . $name . ' в справочнике.';
            //вызываем event
            event(new AddEventLog('access', Auth::id(), $msg));
            abort(503, 'У Вас нет прав на редактирование записи!');
        }
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'unique' => 'Значение поля должно быть уникальным!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'text' => 'required|string|max:50|unique:doc_types',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('carEdit',['id'=>$id])->withErrors($validator)->withInput();
            }

            $model->fill($input);
            $model->update();
            $msg = 'Марка ТС ' . $name . ' была обновлена!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('cars')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('car_edit')) {
            $data = [
                'title' => 'Марки машин',
                'head' => $model->text,
                'data' => $old,
            ];
            return view('car_edit', $data);
        }
        abort(404);
    }
}
