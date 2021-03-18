<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\DocType;
use App\Models\Event;
use App\Models\GuestCard;
use App\Models\Renter;
use App\Models\Visit;
use App\Models\Visitor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Card;
use Modules\Admin\Entities\Device;
use Modules\Energy\Entities\MainLog;
use Modules\Marketing\Entities\Form;
use Modules\Marketing\Entities\Visitorlog;

class MainController extends Controller
{
    public function index(){
        $in = DB::select("SELECT COUNT(*) AS i FROM events WHERE event_type = 4 and event_time LIKE '" . date('Y-m-d'). "%'");
        $out = DB::select("SELECT COUNT(*) AS o FROM events WHERE event_type = 5 and event_time LIKE '" . date('Y-m-d'). "%'");
        $ts = DB::select("SELECT COUNT(*) AS t FROM visitors WHERE id IN (SELECT visitor_id FROM guest_cards WHERE passed LIKE '" . date('Y-m-d'). "%' AND issued IS NULL) AND car_id != 3");
        $all = DB::select("SELECT COUNT(*) AS p FROM `events` e
                                    JOIN visitors v ON v.id = e.visitor_id
                                    WHERE event_time LIKE '" . date('Y-m-d'). "%' AND event_type IN (4,5) AND v.car_id = 3");
        $pout = DB::select("SELECT COUNT(*) AS p FROM `events` e
                                    JOIN visitors v ON v.id = e.visitor_id
                                    WHERE event_time LIKE '" . date('Y-m-d'). "%' AND event_type = 5 AND v.car_id = 3");
        $people = $all[0]->p - $pout[0]->p;
        $events = DB::select("SELECT e.visitor_id,v.fname,v.lname,e.card,c.text AS ts,v.car_num, t.text, e.event_time FROM events e
                                    INNER JOIN visitors v ON v.id = e.visitor_id
                                    INNER JOIN event_types t ON t.code = e.event_type
                                    INNER JOIN cars c ON c.id = v.car_id
                                    WHERE e.event_type IN (2,3,4,5,6,7,54,55) AND event_time LIKE '".date('Y-m-d')."%'
                                    ORDER BY e.event_time DESC LIMIT 10;");
        $orgs = Renter::where(['status'=>1])->get();
        $orgsel = array();
        $cars = Car::all();
        $carsel = array();
        foreach ($cars as $val) {
            $carsel[$val['id']] = $val['text']; //массив для заполнения данных в select формы
        }
        foreach ($orgs as $val) {
            $orgsel[$val['id']] = $val['title']; //массив для заполнения данных в select формы
        }
        $dtypes = DocType::all();
        $docsel = array();
        foreach ($dtypes as $val) {
            $docsel[$val['id']] = $val['text']; //массив для заполнения данных в select формы
        }
        $data = [
            'title' => 'Главная панель',
            'head' => 'Регистрация посетителя',
            'in' => $in[0]->i,
            'out' => $out[0]->o,
            'ts' => $ts[0]->t,
            'people' => $people,
            'events' => $events,
            'orgsel' => $orgsel,
            'carsel' => $carsel,
            'docsel' => $docsel,
        ];

        if(view()->exists('main_index')){
            return view('main_index',$data);
        }
        abort(404);
    }

    public function show(Request $request){
        if($request->isMethod('post')) {
            //$input = $request->except('_token'); //параметр _token нам не нужен
            $last = DB::select("SELECT v.fname,v.lname,v.image AS image,e.card,r.title AS renter, c.text AS ts,v.car_num, t.text, e.event_time,d.text AS device FROM events e
                                    INNER JOIN visitors v ON v.id = e.visitor_id
                                    INNER JOIN renters r ON r.id = v.renter_id
                                    INNER JOIN event_types t ON t.code = e.event_type
                                    INNER JOIN cars c ON c.id = v.car_id
                                    INNER JOIN devices d ON d.id = e.device_id
                                    WHERE e.event_type IN (2,3,4,5,6,7,54,55)
                                    ORDER BY e.event_time DESC LIMIT 1;");
            foreach ($last as $row){
                $name = $row->fname.' '.$row->lname;
                $result = ['fio' => $name, 'firm' => $row->renter,'device'=>$row->device,
                    'card'=>$row->card,'photo'=>$row->image,'event'=>$row->text,'time'=>$row->event_time,
                    'car'=>$row->ts,'num'=>$row->car_num];
            }
            return json_encode($result);
        }
    }

    public function last(Request $request){
        if($request->isMethod('post')) {
            //$input = $request->except('_token'); //параметр _token нам не нужен
            $events = DB::select("SELECT e.visitor_id,v.fname,v.lname,e.card,c.text AS ts,v.car_num, t.text, e.event_time FROM events e
                                    INNER JOIN visitors v ON v.id = e.visitor_id
                                    INNER JOIN event_types t ON t.code = e.event_type
                                    INNER JOIN cars c ON c.id = v.car_id
                                    WHERE e.event_type IN (2,3,4,5,6,7,54,55) AND event_time LIKE '".date('Y-m-d')."%'
                                    ORDER BY e.event_time DESC LIMIT 10;");
            if(!empty($events)){
                $content = '';
                foreach ($events as $row){
                    $content.='<tr>
                                    <td>'. $row->fname .'</td>
                                    <td>'. $row->lname .'</td>
                                    <td>'.Visitor::find($row->visitor_id)->renter->title.'</td>
                                    <td>'. $row->card .'</td>
                                    <td>'. $row->ts .'</td>
                                    <td>'. $row->car_num .'</td>
                                    <td>'. $row->text .'</td>
                                    <td>'. $row->event_time .'</td>
                                </tr>';
                }
                return $content;
            }
            return '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
        }
    }

    public function newTruck(Request $request){
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $name = $input['tsname'];
            $dbl = Car::where(['text'=>$name])->first();
            if(!empty($dbl))
                return 'ERR';
            else{
                $car = new Car();
                $car->text = $name;
                $car->created_at = date('Y-m-d H:i:s');
                if($car->save()){
                    $id = $car->id;
                    $html = '<select id="car_id" class="select2 form-control" name="car_id" required>';
                    $cars = Car::all();
                    foreach ($cars as $row){
                        if($row->id==$id){
                            $html .= '<option selected value="'.$row->id.'">'.$row->text.'</option>';
                        }
                        else{
                            $html .= '<option value="'.$row->id.'">'.$row->text.'</option>';
                        }
                    }
                    $html .= '</select>';
                    return $html;
                }
            }
            return 'NOT';
        }
    }

    public function newVisitor(Request $request){
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            //проверка что карта есть и она гостевая и проход по ней разрешен
            $card = Card::where(['code'=>$input['card']])->first();
            if(empty($card))
                return 'NO CARD';
            if(!$card->share)
                return 'NO SHARE';
            if(!$card->granted)
                return 'STOP';
            if($card->share && $card->granted) {
                //проверяем что карта не привязана к другому человеку, если привязана - отвязываем
                $busy = Visitor::where(['card' => $input['card']])->get();
                if(!empty($busy)){
                    foreach ($busy as $row){
                        $row->card = null;
                        $row->update();
                    }
                }
                //Все нормально, можно выдавать. Проверяем не был ли ранее зарегистрирован данный чел
                $visitor = Visitor::where(['doc_type_id'=>$input['doc_type_id'],'doc_series'=>$input['doc_series'],'doc_num'=>$input['doc_num']])->first();
                if(empty($visitor)){ //новый
                    $model = new Visitor();
                    $model->fill($input);
                    if(empty($input['car_num'])){
                        $model->image = '/images/man.png';
                    }
                    else{
                        $model->image = '/images/truck.png';
                    }
                    $model->created_at = date('Y-m-d H:i:s');
                    if($model->save()){
                        //запоминаем данные о выданной карте
                        $gcard = new GuestCard();
                        $gcard->visitor_id = $model->id;
                        $gcard->card = $model->card;
                        $gcard->passed = date('Y-m-d H:i:s');
                        $gcard->save();
                    }
                }
                else{//был уже
                    $visitor->fill($input);
                    $visitor->update();
                    //запоминаем данные о выданной карте
                    $gcard = new GuestCard();
                    $gcard->visitor_id = $visitor->id;
                    $gcard->card = $visitor->card;
                    $gcard->passed = date('Y-m-d H:i:s');
                    $gcard->save();
                }
                //проверяем, что выбрано ТС, это сделано из-за того, что водители не могут с машины при заезде прикладывать карту
                $car = Car::find($input['car_id'])->text;
                if($car != 'Без ТС'){
                    $device_id = Device::where(['type'=>'Z5RWEB'])->first()->id;
                    //смотрим привязку карты к посетителю
                    $visitor_id = Visitor::where(['card'=>$input['card']])->first()->id;
                    $event = new Event();
                    $event->device_id = $device_id;
                    $event->event_type = '4';
                    $event->card = $input['card'];
                    $event->event_time = date('Y-m-d H:i:s');
                    $event->visitor_id = $visitor_id;
                    $event->created_at = date('Y-m-d H:i:s');
                    $event->save();
                }
                return 'OK';
            }
            return 'ERR';
        }
        return 'NO';
    }

    public function findVisitor(Request $request){
        if($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $doc_type_id = $input['doc_type_id'];
            $doc_series = $input['doc_series'];
            $doc_num = $input['doc_num'];
            if(!empty($doc_type_id) && !empty($doc_series) && !empty($doc_num)){
                $visitor = Visitor::where(['doc_type_id'=>$doc_type_id, 'doc_series'=>$doc_series, 'doc_num'=>$doc_num])->first();
                if(!empty($visitor))
                    return json_encode($visitor->toArray());
            }
        }
    }
}
