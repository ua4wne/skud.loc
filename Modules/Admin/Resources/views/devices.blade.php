@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li class="active">{{ $title }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->
    @if (session('status'))
        <div class="row">
            <div class="alert alert-success panel-remove">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('status') }}
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center">{{ $head }}</h2>
            @if($rows)
                <div class="x_content">
                <a href="{{route('deviceAdd')}}">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                </a>
                </div>
            <div class="x_panel">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Фото</th>
                        <th>Тип</th>
                        <th>Серийный №</th>
                        <th>Firmware</th>
                        <th>Conn Fw</th>
                        <th>Описание</th>
                        <th>IP адрес</th>
                        <th>Статус</th>
                        <th>Режим работы</th>
                        <th>Временная зона</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($rows as $row)

                        <tr id="{{ $row->id }}">
                            <td><img src="{{ $row->image }}" alt="..."></td>
                            <td>{{ $row->type }}</td>
                            <td>{{ $row->snum }}</td>
                            <td>{{ $row->fware }}</td>
                            <td>{{ $row->conn_fw }}</td>
                            <td>{{ $row->text }}</td>
                            <td>{{ $row->address }}</td>
                            <td>
                                @if($row->is_active)
                                    <span role="button" class="label label-success">В работе</span>
                                @else
                                    <span role="button" class="label label-danger">Отключен</span>
                                @endif
                            </td>
                            @if($row->mode==0)
                            <td>Нормальный</td>
                                @elseif($row->mode==1)
                                <td>Блокировка</td>
                            @elseif($row->mode==2)
                                <td>Свободный проход</td>
                            @elseif($row->mode==3)
                                <td>Ожидание свободного прохода</td>
                            @else
                                <td>Не известно</td>
                            @endif
                            <td>{{ $row->time_zone->zone .' : '.$row->time_zone->text }}</td>
                            <td style="width:160px;">
                                {!! Form::open(['url'=>route('deviceEdit',['id'=>$row->id]), 'class'=>'form-horizontal','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                {{ method_field('DELETE') }}
                                <div class="form-group" role="group">
                                    <a href="{{ route('deviceEdit',['id'=>$row->id]) }}" class="btn btn-success btn-sm" type="button" title = "Редактировать запись"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a>
                                    <a href="{{ route('clearCard',['id'=>$row->id]) }}" class="btn btn-danger btn-sm btn_erase" type="button" title = "Удалить все карты"><i class="fa fa-eraser fa-lg" aria-hidden="true"></i></a>
                                    {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn-sm','type'=>'submit','title'=>'Удалить запись']) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
                {{ $rows->links() }}
            </div>
            @endif
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
    <script>
        $(document).ready(function(){
            let options = {
                'backdrop' : 'true',
                'keyboard' : 'true'
            }
            $('#basicModal').modal(options);
        });

        $('.btn_erase').click(function(e){
            var x = confirm("Из выбранного контроллера будут удалены все карты. Продолжить (Да/Нет)?");
            if (x) {
                return true;
            }
            else {

                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection
