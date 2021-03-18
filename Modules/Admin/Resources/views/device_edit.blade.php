@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('devices') }}">{{$title}}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_panel">
        <h2 class="text-center">{{$head}}</h2>
        {!! Form::open(['url' => route('deviceEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST','id'=>'new_form','files'=>'true']) !!}

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Тип устройства: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('type',$data['type'],['class' => 'form-control','placeholder'=>'Введите тип устройства','maxlength'=>'10','required'=>'required'])!!}
                {!! $errors->first('type', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Серийный №: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('snum',$data['snum'],['class' => 'form-control','placeholder'=>'Введите серийный номер устройства','maxlength'=>'10','disabled'=>'disabled'])!!}
                {!! $errors->first('snum', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('fware','Версия прошивки:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('fware',$data['fware'],['class' => 'form-control','placeholder'=>'Введите версию firmware','maxlength'=>'10'])!!}
                {!! $errors->first('fware', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('conn_fw','Версия conn-прошивки:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('conn_fw',$data['conn_fw'],['class' => 'form-control','placeholder'=>'Введите версию conn-firmware','maxlength'=>'10'])!!}
                {!! $errors->first('conn_fw', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Статус: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::select('is_active',['1' => 'Активный', '0' => 'Не активный'], $data['is_active'], ['class' => 'form-control','required'=>'required','id'=>'is_active']); !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Режим работы: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::select('mode',['0' => 'Норма', '1' => 'Блокировка', '2' => 'Свободный проход', '3' => 'Ожидание свободного прохода', '12' => 'Не установлен'], $data['mode'], ['class' => 'form-control','required'=>'required','id'=>'mode']); !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Временная зона: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::select('time_zone_id',$zonesel, $data['time_zone_id'], ['class' => 'form-control','required'=>'required','id'=>'time_zone_id']); !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                IP-адрес: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('address',$data['address'],['class' => 'form-control','placeholder'=>'Введите IP-адрес устройства','maxlength'=>'15','required'=>'required'])!!}
                {!! $errors->first('address', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('image', 'Изображение:',['class'=>'col-xs-3 control-label']) !!}
            <div class="col-xs-8">
                {!! Form::file('image', ['class' => 'form-control','data-buttonText'=>'Выберите файл изображения','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран"]) !!}
                {!! $errors->first('image', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('text','Описание:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('text',$data['text'],['class' => 'form-control','placeholder'=>'Введите описание устройства','maxlength'=>'254'])!!}
                {!! $errors->first('text', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-2 col-xs-10">
                {!! Form::button('Сохранить', ['class' => 'btn btn-primary','type'=>'submit','id'=>'save_btn']) !!}
            </div>
        </div>

        {!! Form::close() !!}

    </div>
@endsection

@section('user_script')
    <script>
        $('#save_btn').click(function(){
            let error=0;
            $("#new_form").find(":input").each(function() {// проверяем каждое поле ввода в форме
                if($(this).attr("required")=='required'){ //обязательное для заполнения поле формы?
                    if(!$(this).val()){// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error=1;// определяем индекс ошибки
                    }
                    else{
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if(error){
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
        });
    </script>
@endsection

