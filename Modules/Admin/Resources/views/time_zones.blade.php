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
                <a href="{{route('tzoneAdd')}}">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green" aria-hidden="true"></i> Новая запись</button>
                </a>
                </div>
            <div class="x_panel">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Временная зона</th>
                        <th>Время начала</th>
                        <th>Время окончания</th>
                        <th>Дни недели</th>
                        <th>Описание</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($rows as $row)

                        <tr>
                            <td>{{ $row->zone }}</td>
                            <td>{{ $row->begin }}</td>
                            <td>{{ $row->end }}</td>
                            <td>{{ $row->days }}</td>
                            <td>{{ $row->text }}</td>
                            <td style="width:110px;">
                                {!! Form::open(['url'=>route('tzoneEdit',['id'=>$row->id]), 'class'=>'form-horizontal','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                {{ method_field('DELETE') }}
                                <div class="form-group" role="group">
                                    <a href="{{ route('tzoneEdit',['id'=>$row->id]) }}" class="btn btn-success btn-sm" type="button" title = "Редактировать запись"><i class="fa fa-edit fa-lg" aria-hidden="true"></i></a>
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
@endsection
