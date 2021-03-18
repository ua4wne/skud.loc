<?php

namespace App\Http\Controllers;

use App\Models\GuestCard;
use App\Models\Renter;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Role;

class ReportController extends Controller
{
    public function trafficFlow(Request $request)
    {
        if (view()->exists('traffic_flow')) {
            $renters = Renter::where(['status' => 1])->get();
            $rentsel = array();
            foreach ($renters as $row) {
                $rentsel[$row->id] = $row->title . ' (' . $row->area . ')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Транспортный поток',
                'head' => 'Задайте условия отбора',
                'rentsel' => $rentsel,
            ];
            return view('traffic_flow', $data);
        }
        abort(404);
    }

    public function trafficBar(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $from = $input['start'];
            $to = $input['finish'];
            $renter_id = $input['renter_id'];

            $data = array();
            $visitors = Visitor::where(['renter_id' => $renter_id])->get();
            if (!empty($from) && !empty($to) && !empty($visitors)) {
                $f = Carbon::parse($from);
                $t = Carbon::parse($to);
                $tt = $t->diffInDays($f);
                $in = '';
                foreach ($visitors as $row) {
                    if(!empty($row->car_id) && !empty($row->car_num) && $row->employee==0)
                        $in .= $row->id . ',';
                }
                $in = substr($in, 0, -1);
                if ($from == $to){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,12,2) AS period FROM `events` WHERE visitor_id IN ($in)
                                AND event_type=4 and event_time LIKE '$from%'
                                GROUP BY substring(event_time,1,13)
                                ORDER BY substring(event_time,1,13) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period . ' час.';
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    return json_encode($data);
                }
                elseif($tt <= 31){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,9,2) AS period FROM `events` WHERE visitor_id IN ($in)
                                AND event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,10)
                                ORDER BY substring(event_time,1,10) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    return json_encode($data);
                }
                else
                    $query = "SELECT COUNT(card) AS kolvo, substring(event_time,1,7) AS period FROM `events` WHERE visitor_id IN ($in)
                                AND event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,7)
                                ORDER BY substring(event_time,1,7) ASC";
                $logs = DB::select($query);
                foreach ($logs as $log) {
                    $tmp = array();
                    $tmp['y'] = $log->period;
                    $tmp['a'] = $log->kolvo;
                    array_push($data, $tmp);
                }
            }
            return json_encode($data);
        }
    }

    public function trafficTbl(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $from = $input['start'];
            $to = $input['finish'];
            $renter_id = $input['renter_id'];
            $data = array();
            $visitors = Visitor::where(['renter_id' => $renter_id])->get();
            if (!empty($from) && !empty($to) && !empty($visitors)) {
                $f = Carbon::parse($from);
                $t = Carbon::parse($to);
                $tt = $t->diffInDays($f);
                $in = '';
                foreach ($visitors as $row) {
                     if(!empty($row->car_id) && !empty($row->car_num) && !$row->employee)
                        $in .= $row->id . ',';
                }
                $in = substr($in, 0, -1);
                if ($from == $to){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,12,2) AS period FROM `events` WHERE visitor_id IN ($in)
                                AND event_type=4 and event_time LIKE '$from%'
                                GROUP BY substring(event_time,1,13)
                                ORDER BY substring(event_time,1,13) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    $content='<br/><table class="table table-hover">
                            <tr><th>Часы</th>';
                }
                elseif($tt <= 31){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,9,2) AS period FROM `events` WHERE visitor_id IN ($in)
                                AND event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,10)
                                ORDER BY substring(event_time,1,10) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    $content='<br/><table class="table table-hover">
                            <tr><th>Дата</th>';
                }
                else{
                    $query = "SELECT COUNT(card) AS kolvo, substring(event_time,1,7) AS period FROM `events` WHERE visitor_id IN ($in)
                                AND event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,7)
                                ORDER BY substring(event_time,1,7) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    $content='<br/><table class="table table-hover">
                            <tr><th>Период</th>';
                }
                foreach ($data as $row){
                    $content .= '<td>'.$row['y'].'</td>';
                }
                $content .= '</tr>';
                $content .= '<tr><th>Кол-во ТС</th>';
                foreach ($data as $row){
                    $content .= '<td>'.$row['a'].'</td>';
                }
                $content .= '</tr>';
            }
            return $content;
        }
    }

    public function analizeBar(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $from = $input['start'];
            $to = $input['finish'];

            $data = array();
            if (!empty($from) && !empty($to)) {
                $f = Carbon::parse($from);
                $t = Carbon::parse($to);
                $tt = $t->diffInDays($f);

                if ($from == $to){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,12,2) AS period FROM `events`
                                WHERE event_type=4 and event_time LIKE '$from%'
                                GROUP BY substring(event_time,1,13)
                                ORDER BY substring(event_time,1,13) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period . ' час.';
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    return json_encode($data);
                }
                elseif($tt <= 31){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,9,2) AS period FROM `events`
                                WHERE event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,10)
                                ORDER BY substring(event_time,1,10) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    return json_encode($data);
                }
                else
                    $query = "SELECT COUNT(card) AS kolvo, substring(event_time,1,7) AS period FROM `events`
                                WHERE event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,7)
                                ORDER BY substring(event_time,1,7) ASC";
                $logs = DB::select($query);
                foreach ($logs as $log) {
                    $tmp = array();
                    $tmp['y'] = $log->period;
                    $tmp['a'] = $log->kolvo;
                    array_push($data, $tmp);
                }
            }
            return json_encode($data);
        }
    }

    public function analizeTbl(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $from = $input['start'];
            $to = $input['finish'];
            $data = array();

            if (!empty($from) && !empty($to)) {
                $f = Carbon::parse($from);
                $t = Carbon::parse($to);
                $tt = $t->diffInDays($f);

                if ($from == $to){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,12,2) AS period FROM `events`
                                WHERE event_type=4 and event_time LIKE '$from%'
                                GROUP BY substring(event_time,1,13)
                                ORDER BY substring(event_time,1,13) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    $content='<br/><table class="table table-hover">
                            <tr><th>Часы</th>';
                }
                elseif($tt <= 31){
                    $query = "SELECT COUNT(card) AS kolvo, SUBSTRING(event_time,9,2) AS period FROM `events`
                                WHERE event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,10)
                                ORDER BY substring(event_time,1,10) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    $content='<br/><table class="table table-hover">
                            <tr><th>Дата</th>';
                }
                else{
                    $query = "SELECT COUNT(card) AS kolvo, substring(event_time,1,7) AS period FROM `events`
                                WHERE event_type=4 and event_time between '$from' AND '$to'
                                GROUP BY substring(event_time,1,7)
                                ORDER BY substring(event_time,1,7) ASC";
                    $logs = DB::select($query);
                    foreach ($logs as $log) {
                        $tmp = array();
                        $tmp['y'] = $log->period;
                        $tmp['a'] = $log->kolvo;
                        array_push($data, $tmp);
                    }
                    $content='<br/><table class="table table-hover">
                            <tr><th>Период</th>';
                }
                foreach ($data as $row){
                    $content .= '<td>'.$row['y'].'</td>';
                }
                $content .= '</tr>';
                $content .= '<tr><th>Кол-во ТС</th>';
                foreach ($data as $row){
                    $content .= '<td>'.$row['a'].'</td>';
                }
                $content .= '</tr>';
            }
            return $content;
        }
    }

    public function visitors(Request $request)
    {
        if ($request->isMethod('post')) {
            $input = $request->except('_token'); //параметр _token нам не нужен
            $from = Carbon::parse($input['start'] . ' 00:00:00');
            $to = Carbon::parse(($input['finish'] . ' 23:59:59'));

            $renter_id = $input['renter_id'];
            if(!empty($renter_id)){
                $visitors = Visitor::where(['renter_id'=>$renter_id,'employee'=>0])->whereBetween('created_at', [$from, $to])->get();
            }
            else {
                $visitors = Visitor::where('employee',0)->whereBetween('created_at', [$from, $to])->get();
            }
            $content='<table class="table table-hover">
                            <tr><th>ФИО</th><th>Арендатор</th><th>Модель ТС</th><th>Рег. номер</th><th>Документ</th>
                            <th>Серия</th><th>Номер</th><th>Пропуск</th><th>Время входа</th><th>Время выхода</th></tr>';
            if(!empty($visitors)){
                foreach ($visitors as $visitor){
                    $content .= '<tr><td>' . $visitor->full_name .'</td><td>' . $visitor->renter->title .'</td>
                    <td>' . $visitor->car->text .'</td><td>' . $visitor->car_num .'</td><td>' . $visitor->doc_type->text .'</td>
                    <td>' . $visitor->doc_series .'</td><td>' . $visitor->doc_num .'</td>';
                    $time = GuestCard::where('visitor_id',$visitor->id)->whereBetween('passed', [$from, $to])->orderBy('passed','desc')->first();
                    $out = GuestCard::where('visitor_id',$visitor->id)->whereBetween('issued', [$from, $to])->orderBy('issued','desc')->first();
                    if(!empty($time->card)){
                        $content .= '<td>' . $time->card .'</td>';
                    }
                    else{
                        $content .= '<td></td>';
                    }
                    if(!empty($time->passed)){
                        $content .= '<td>' . $time->passed .'</td>';
                    }
                    else{
                        $content .= '<td></td>';
                    }
                    if(!empty($out->issued)){
                        $content .= '<td>' . $out->issued .'</td></tr>';
                    }
                    else{
                        $content .= '<td></td></tr>';
                    }
                }
            }
            $content .= '</table>';
            return $content;
        }
        if (view()->exists('visitor_report')) {
            $renters = Renter::where(['status' => 1])->get();
            $rentsel = array();
            foreach ($renters as $row) {
                $rentsel[$row->id] = $row->title . ' (' . $row->area . ')'; //массив для заполнения данных в select формы
            }
            $data = [
                'title' => 'Отчет по посетителям',
                'head' => 'Задайте условия отбора',
                'rentsel' => $rentsel,
            ];
            return view('visitor_report', $data);
        }
        abort(404);
    }
}
