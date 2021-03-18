<?php

namespace App\Http\Controllers;

use App\Events\AddEventLog;
use App\Models\Event;
use App\Models\GuestCard;
use App\Models\Tracelog;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Admin\Entities\Card;
use Modules\Admin\Entities\Device;
use Modules\Admin\Entities\Task;

class DataController extends Controller
{
    public function index(Request $request)
    {
        //Получить JSON как строку
        $json_str = file_get_contents('php://input');
        //$json_str = '{"type":"Z5RWEB","sn":44374,"messages":[{ "id":112805732, "success":1},{"id":1120048829,"operation":"events","events":[{"flag": 0,"event": 4,"time": "2018-06-20 09:50:37","card": "00000029CF67"},{"flag": 0,"event": 16,"time": "2018-06-20 09:50:37","card": "00000029CF67"}]}]}';
        //$json_str = '{"type":"Z5RWEB","sn":44374,"messages":[{ "id":511702305, "success":1},{"id":2084420925,"operation":"events","events":[{"flag": 0,"event": 2,"time": "2018-06-18 16:24:49","card": "00000029780F"},{"flag": 0,"event": 2,"time": "2018-06-19 00:56:46","card": "000000297D13"},{"flag": 0,"event": 2,"time": "2018-06-19 00:56:47","card": "000000297D13"},{"flag": 0,"event": 2,"time": "2018-06-19 00:56:50","card": "000000297D13"},{"flag": 0,"event": 2,"time": "2018-06-19 08:15:11","card": "000000164A84"},{"flag": 0,"event": 2,"time": "2018-06-19 08:15:15","card": "000000164A84"},{"flag": 0,"event": 2,"time": "2018-06-19 08:25:54","card": "00000029697E"},{"flag": 0,"event": 2,"time": "2018-06-19 08:29:52","card": "00000029BD87"},{"flag": 0,"event": 4,"time": "2018-06-19 10:48:30","card": "00000014DEE7"},{"flag": 0,"event": 16,"time": "2018-06-19 10:48:30","card": "00000014DEE7"},{"flag": 0,"event": 5,"time": "2018-06-19 10:48:34","card": "00000014DEE7"},{"flag": 0,"event": 17,"time": "2018-06-19 10:48:34","card": "00000014DEE7"}]}]}';
        //$json_str = ' {"type":"Z5RWEB","sn":44374,"messages":[{"id":1869470124,"operation":"events","events":[{"flag": 0,"event": 4,"time": "2018-06-08 14:26:22","card": "00000067BDC3"}]}]}';
        //$json_str = '{"type":"Z5RWEB","sn":44374,"messages":[{"id":631704567,"operation":"power_on","fw":"3.23","conn_fw":"1.0.123","active":0,"mode":0,"controller_ip":"192.168.8.9"}]}';
        //$json_str = '{"type":"Z5RWEB","sn":44374,"messages":[{"id":1856669179,"operation":"ping","active":1,"mode":0}]}';
        //$json_str = '{"type":"Z5RWEB","sn":44374,"messages":[{"id":717885386,"operation":"events","events":[{"flag": 0,"event": 17,"time": "2018-09-21 12:25:34","card": "00000067DE1D"}]}]}';
        //$json_str = '{"type":"Z5RWEB","sn":44374,"messages":[{ "id":358931379, "success":1},{"id":663594565,"operation":"events","events":[{"flag": 264,"event": 37,"time": "2018-04-10 10:49:12","card": "000000000000"}]}]}';
        //Получить объект
        $json = json_decode($json_str);
        //проверяем успешность выполнения последнего задания
        $pos = strpos($json_str, '"success":1');
        $sn = $json->sn; //серийный номер контроллера Z5R-WEB
        $type = $json->type; //тип контроллера Z5R-WEB
        $device_id = Device::where(['type' => $type, 'snum' => $sn])->first()->id;
        if (!empty($pos)) {
            //удаляем активную команду контроллеру
            $cmd = Task::where(['device_id' => $device_id, 'status' => 1])->first();
            if (!empty($cmd)) $cmd->delete();
        }
        if (!empty($json)) {
            //запись в лог
            /*$trace = new Tracelog();
            $trace->type='request';
            $trace->msg = $json_str;
            $trace->save();*/
            $types = [4, 5, 16, 17, 40, 41]; //типы событий: входы и выходы
            foreach ($json->messages as $message) {
                if (isset($message->operation)) {
                    //активация контроллера
                    if ($message->operation == "power_on") {
                        $this->power_on($type, $sn, $message);
                        $msg = new \stdClass();
                        $msg->id = $message->id;
                        $msg->operation = "set_active";
                        $msg->active = 1;
                        $msg->online = 1;
                    }

                    //пинг
                    if ($message->operation == "ping") {
                        //проверяем нет ли сформированных команд контроллеру
                        $task = Task::where(['device_id' => $device_id, 'status' => 1])->first();
                        if (empty($task)) {
                            //а вообще задания ему есть?
                            $cmd = Task::where(['device_id' => $device_id, 'status' => 0])->first();
                            if (!empty($cmd)) {
                                $msg = json_decode($cmd->json);
                                $cmd->status = 1;
                                $cmd->update();
                            }
                        } else {
                            $msg = json_decode($task->json);
                        }
                    }
                    //events
                    if ($message->operation == "events") {
                        $event_success = 0;
                        $device_id = Device::where(['type' => $type, 'snum' => $sn])->first()->id;
                        //обрабатываем каждое событие
                        foreach ($message->events as $item) {
                            $model = new Event();
                            $model->device_id = $device_id;
                            $model->event_type = $item->event;
                            $model->card = hexdec($item->card);
                            if ($item->card != '000000000000') {
                                //смотрим привязку карты к посетителю
                                $visitor = Visitor::where(['card' => $model->card])->first();
                                if (!empty($visitor)) {
                                    $model->visitor_id = $visitor->id;
                                }
                                if (isset($item->flag))
                                    $model->flag = $item->flag;
                                $model->event_time = $item->time;
                                $model->created_at = date('Y-m-d H:i:s');
                                $model->save();
                                if (in_array($model->event_type, $types) && $model->visitor_id){
                                    $this->check_guest($model->event_type, $model->card, $model->visitor_id);
                                }
                            }
                            $event_success++;
                        }
                        $msg = new \stdClass();
                        $msg->id = $message->id;
                        $msg->operation = 'events';
                        $msg->events_success = $event_success;
                    }

                    //проверка доступа
                    if ($message->operation == "check_access") {
                        $card = hexdec($message->card);
                        //есть такая карта в базе?
                        $model = Card::where(['code' => $card])->first();
                        $msg = new \stdClass();
                        $msg->id = $message->id;
                        $msg->operation = 'check_access';
                        if (empty($model))
                            $msg->granted = 1; //проход запрещен
                        else
                            $msg->granted = $model->granted;

                    }
                }
            }
            //преобразование и отправка сообщения контроллеру
            $send = new \stdClass();
            $send->date = date('Y-m-d H:i:s');
            $send->interval = 10;
            if (isset($msg))
                $send->messages[0] = $msg;
            else
                $send->messages = array();
            $data = json_encode($send);
            return $data;
        }
    }

    private function power_on($type, $sn, $msg)
    {
        //проверяем наличие контроллера в базе
        $device = Device::where(['type' => $type, 'snum' => $sn])->first();
        if (empty($device)) { //новая запись
            $model = new Device();
            $model->type = $type;
            $model->snum = $sn;
            $model->fware = $msg->fw;
            $model->conn_fw = $msg->conn_fw;
            $model->is_active = $msg->active;
            $model->mode = $msg->mode;
            $model->address = $msg->controller_ip;
            $model->image = '/images/noimage.jpg';
            $model->zone_id = 1;
            $model->created_at = date('Y-m-d H:i:s');
            $model->updated_at = date('Y-m-d H:i:s');
            $model->save(); //отключаем валидацию
            $msg = 'Новый контроллер ' . $model->type . ' (sn ' . $model->snum . ') добавлен в систему';
            //вызываем event
            $ip = $model->address;
            event(new AddEventLog('info', Auth::id(), $msg, $ip));
        } else {
            $device->fware = $msg->fw;
            $device->conn_fw = $msg->conn_fw;
            $device->is_active = $msg->active;
            $device->mode = $msg->mode;
            $device->address = $msg->controller_ip;
            $device->update();
        }
        return true;
    }

    private function check_guest($type, $card, $visitor_id)
    {
        //карта гостевая?
        $c = Card::where(['code' => $card])->first();
        if(empty($c)){
            //вызываем event
            $msg = 'Карта '.$card. ' не обнаружена в системе!';
            event(new AddEventLog('error', 1, $msg, null));
            return;
        }
        if (!$c->share) return;
        //запоминаем время прохода
        if ($type == 4 || $type == 16 || $type == 40) { // вход
            $model = new GuestCard();
            $model->visitor_id = $visitor_id;
            $model->card = $card;
            $model->passed = date('Y-m-d H:i:s');
            $model->save();
            return;
        } else { //выход
            $guest = GuestCard::where(['visitor_id' => $visitor_id])->first();
            if (!empty($guest)) {
                //$guest->card = null;
                //$guest->passed = null;
                $guest->issued = date('Y-m-d H:i:s');
                $guest->update();
            }
            //отвязываем карту от посетителя
            $visitor = Visitor::find($visitor_id);
            $visitor->card = null;
            $visitor->update();
        }
        return;
    }
}
