<?php

namespace Modules\Admin\Http\Controllers;

use App\Events\AddEventLog;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Task;
use Validator;

class TaskController extends Controller
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
        if(view()->exists('admin::tasks')){
            $rows = Task::all(); //all();
            $data = [
                'title' => 'Очередь задач',
                'head' => 'Очередь задач',
                'rows' => $rows,
            ];
            return view('admin::tasks',$data);
        }
        abort(404);
    }


    /**
     * Delete all resource in storage.
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        $tasks = Task::all();
        if(!empty($tasks)){
            foreach ($tasks as $task){
                $task->delete();
            }
            $msg = 'Очередь заданий контроллеру была очищена!';
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
        }
        return redirect()->route('tasks')->with('status',$msg);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id,Request $request)
    {
        $task = Task::find($id);
        if(!empty($task)){
            $task->delete();
            $msg = "Задание $task->json было удалено!";
            $ip = $request->getClientIp();
            //вызываем event
            event(new AddEventLog('info',Auth::id(),$msg,$ip));
        }
        return redirect()->route('tasks')->with('status',$msg);
    }
}
