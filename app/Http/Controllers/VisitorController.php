<?php

namespace App\Http\Controllers;

use App\Events\AddEventLog;
use App\Models\Car;
use App\Models\DocType;
use App\Models\Renter;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;
use Validator;

class VisitorController extends Controller
{
    public function index()
    {
        if(view()->exists('visitors')){
            $rows = Visitor::where(['employee'=>1])->get();
            $data = [
                'title' => 'Посетители',
                'head' => 'Список посетителей',
                'rows' => $rows,
            ];
            return view('visitors',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if($request->isMethod('post')){
            $input = $request->except('_token'); //параметр _token нам не нужен

            $messages = [
                'required' => 'Поле обязательно к заполнению!',
                'max' => 'Значение поля должно быть не более :max символов!',
                'integer' => 'Значение поля должно быть числом!',
                'string' => 'Значение поля должно быть строкой!',
            ];
            $validator = Validator::make($input,[
                'fname' => 'required|string|max:50',
                'mname' => 'nullable|string|max:50',
                'lname' => 'required|string|max:50',
                'card' => 'nullable|string|max:20',
                'renter_id' => 'required|integer',
                'car_id' => 'required|integer',
                'car_num' => 'nullable|string|max:10',
                'doc_type_id' => 'required|integer',
                'doc_series' => 'nullable|string|max:7',
                'doc_num' => 'nullable|string|max:10',
                'phone' => 'nullable|string|max:15',
                'employee' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('visitorAdd')->withErrors($validator)->withInput();
            }
            $input['image'] = '/images/male.png';
            if ($request->hasFile('image')) {
                $file = $request->file('image'); //загружаем файл
                $name = $file->getClientOriginalName();
                $filename = substr(md5(microtime() . rand(0, 9999)), 0, 20);
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $input['image'] = '/images/gallery/'.$filename.'.'.$extension;
                $file->move(public_path() . '/images/gallery/', $input['image']);
            }
            $visitor = new Visitor();
            $visitor->fill($input);
            if($visitor->save()){
                $msg = 'Посетитель '. $input['lname'].' '. $input['fname'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('visitors')->with('status',$msg);
            }
        }
        if(view()->exists('visitor_add')){
            $orgs = Renter::where(['status'=>1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val['id']] = $val['title']; //массив для заполнения данных в select формы
            }
            $dtypes = DocType::all();
            $docsel = array();
            foreach ($dtypes as $val) {
                $docsel[$val['id']] = $val['text']; //массив для заполнения данных в select формы
            }
            $cars = Car::all();
            $carsel = array();
            foreach ($cars as $val) {
                $carsel[$val['id']] = $val['text']; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Организации',
                'head' => 'Новая запись',
                'orgsel' => $orgsel,
                'docsel' => $docsel,
                'carsel' => $carsel,
            ];
            return view('visitor_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request)
    {
        $model = Visitor::find($id);
        if($request->isMethod('delete')){
            if (!Role::granted('delete_refs')) {
                $msg = 'Попытка удаления посетителя ' . $model->full_name . ' из справочника.';
                event(new AddEventLog('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Посетитель '. $model->full_name .' был удален из системы!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('visitors')->with('status',$msg);
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
                'fname' => 'required|string|max:50',
                'mname' => 'nullable|string|max:50',
                'lname' => 'required|string|max:50',
                'card' => 'nullable|string|max:20',
                'renter_id' => 'required|integer',
                'car_id' => 'required|integer',
                'car_num' => 'nullable|string|max:10',
                'doc_type_id' => 'required|integer',
                'doc_series' => 'nullable|string|max:7',
                'doc_num' => 'nullable|string|max:10',
                'phone' => 'nullable|string|max:15',
                'employee' => 'required|integer',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('visitorEdit',['id'=>$id])->withErrors($validator)->withInput();
            }
            if ($request->hasFile('image')) {
                //сначала удаляем старый файл, если он не по-умолчанию
                if($model->image != '/images/male.png' && $model->image != '/images/female.png')
                    unlink(public_path() . $model->image); //удаляем старый файл
                $file = $request->file('image'); //загружаем файл
                $name = $file->getClientOriginalName();
                $filename = substr(md5(microtime() . rand(0, 9999)), 0, 20);
                $extension = pathinfo($name, PATHINFO_EXTENSION);
                $input['image'] = '/images/gallery/'.$filename.'.'.$extension;
                $file->move(public_path() . '/images/gallery', $input['image']);
            }
            if(empty($input['image']))
                $input['image'] = $model->image;
            $model->fill($input);
            $model->update();
            $msg = 'Данные посетителя ' . $model->full_name . ' были обновлены!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('visitors')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('visitor_edit')) {
            $orgs = Renter::where(['status'=>1])->get();
            $orgsel = array();
            foreach ($orgs as $val) {
                $orgsel[$val['id']] = $val['title']; //массив для заполнения данных в select формы
            }
            $dtypes = DocType::all();
            $docsel = array();
            foreach ($dtypes as $val) {
                $docsel[$val['id']] = $val['text']; //массив для заполнения данных в select формы
            }
            $cars = Car::all();
            $carsel = array();
            foreach ($cars as $val) {
                $carsel[$val['id']] = $val['text']; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Посетители',
                'head' => $model->full_name,
                'data' => $old,
                'orgsel' => $orgsel,
                'docsel' => $docsel,
                'carsel' => $carsel,
            ];
            return view('visitor_edit', $data);
        }
        abort(404);
    }
}
