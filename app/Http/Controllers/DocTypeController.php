<?php

namespace App\Http\Controllers;

use App\Events\AddEventLog;
use App\Models\DocType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Role;
use Validator;

class DocTypeController extends Controller
{
    public function index()
    {
        if (!Role::granted('view_refs')) {//вызываем event
            abort(503, 'У Вас нет прав на просмотр справочников!');
        }
        if(view()->exists('doc_types')){
            $rows = DocType::paginate(env('PAGINATION_SIZE'));
            $data = [
                'title' => 'Виды документов',
                'head' => 'Виды документов',
                'rows' => $rows,
            ];
            return view('doc_types',$data);
        }
        abort(404);
    }

    public function create(Request $request){
        if (!Role::granted('edit_refs')) {//вызываем event
            $msg = 'Попытка создания нового вида документа!';
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
                'text' => 'required|string|max:50|unique:doc_types',
            ],$messages);
            if($validator->fails()){
                return redirect()->route('dtypeAdd')->withErrors($validator)->withInput();
            }

            $tdoc = new DocType();
            $tdoc->fill($input);
            if($tdoc->save()){
                $msg = 'Тип документа '. $input['text'] .' был успешно добавлен!';
                $ip = $request->getClientIp();
                //вызываем event
                event(new AddEventLog('info',Auth::id(),$msg,$ip));
                return redirect()->route('doc-types')->with('status',$msg);
            }
        }
        if(view()->exists('doc_type_add')){
            $data = [
                'title' => 'Виды документов',
                'head' => 'Новая запись',
            ];
            return view('doc_type_add', $data);
        }
        abort(404);
    }

    public function edit($id,Request $request)
    {
        $model = DocType::find($id);
        $name = $model->text;
        if($request->isMethod('delete')){
            if (!Role::granted('delete_refs')) {
                $msg = 'Попытка удаления вида документа ' . $name . ' из справочника.';
                event(new AddEventLog('access', Auth::id(), $msg));
                abort(503, 'У Вас нет прав на удаление записи!');
            }
            $model->delete();
            $msg = 'Вид документа '. $name .' был удален!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
            return redirect()->route('doc-types')->with('status',$msg);
        }
        if (!Role::granted('edit_refs')) {
            $msg = 'Попытка редактирования вида документа ' . $name . ' в справочнике.';
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
                return redirect()->route('dtypeEdit',['id'=>$id])->withErrors($validator)->withInput();
            }

            $model->fill($input);
            $model->update();
            $msg = 'Вид документа ' . $name . ' был обновлен!';
            //вызываем event
            event(new AddEventLog('info', Auth::id(), $msg));
            return redirect()->route('doc-types')->with('status', $msg);
        }
        $old = $model->toArray(); //сохраняем в массиве предыдущие значения полей модели
        if (view()->exists('doc_type_edit')) {
            $data = [
                'title' => 'Виды документов',
                'head' => $model->text,
                'data' => $old,
            ];
            return view('doc_type_edit', $data);
        }
        abort(404);
    }
}
