<?php

namespace App\Http\Controllers;

use App\Events\AddEventLog;
use App\Models\EventLog;
use App\Models\Tracelog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventLogController extends Controller
{
    public function index()
    {
        if(!User::hasRole('admin')){
            abort(503);
        }
        if(view()->exists('eventlogs')){
            $title='Системный лог';
            $rows = EventLog::orderBy('created_at', 'desc')->limit(100)->get();
            $data = [
                'title' => $title,
                'head' => 'События в системе',
                'rows' => $rows,
            ];
            return view('eventlogs',$data);
        }
        abort(404);
    }

    public function delOne($id){
        $model = EventLog::find($id);
        if(!empty($model))
            $model->delete();
        return redirect()->route('eventlogs');
    }

    public function delLog(Request $request){
        $rows = EventLog::all();
        foreach ($rows as $row){
            $row->delete();
        }
        $msg = 'Журнал системных логов успешно очищен!';
        //вызываем event
        $ip = $request->getClientIp();
        event(new AddEventLog('info',Auth::id(),$msg,$ip));
        return redirect()->route('eventlogs')->with('status',$msg);
    }

    public function trace(){
        if(!User::hasRole('admin')){
            abort(503);
        }
        if(view()->exists('tracelogs')){
            $title='Лог трассировки';
            $rows = Tracelog::orderBy('created_at', 'desc')->limit(100)->get();
            $data = [
                'title' => $title,
                'head' => 'Лог сообщений с контроллера',
                'rows' => $rows,
            ];
            return view('tracelogs',$data);
        }
        abort(404);
    }

    public function traceDelOne($id){
        $model = Tracelog::find($id);
        if(!empty($model))
            $model->delete();
        return redirect()->route('tracelogs');
    }

    public function traceDelLog(Request $request){
        $rows = Tracelog::all();
        foreach ($rows as $row){
            $row->delete();
        }
        $msg = 'Журнал логов трассировки очищен!';
        //вызываем event
        $ip = $request->getClientIp();
        event(new AddEventLog('info',Auth::id(),$msg,$ip));
        return redirect()->route('tracelogs')->with('status',$msg);
    }
}
