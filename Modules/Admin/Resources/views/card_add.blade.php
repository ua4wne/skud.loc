@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('devices') }}">{{$title}}</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_panel">
        <h2 class="text-center">{{$head}}</h2>
        {!! Form::open(['url' => route('cardAdd'),'class'=>'form-horizontal','method'=>'POST','id'=>'new_form']) !!}

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Идентификатор: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('code',old('code'),['class' => 'form-control','placeholder'=>'Введите код карты','maxlength'=>'20','required'=>'required'])!!}
                {!! $errors->first('code', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Тип карты: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::select('share',['0' => 'Гостевая', '1' => 'Индивидуальная'], old('share'), ['class' => 'form-control','required'=>'required','id'=>'share']); !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Статус: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::select('granted',['1' => 'Активная', '0' => 'Заблокирована'], old('granted'), ['class' => 'form-control','required'=>'required','id'=>'granted']); !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Временная зона: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::select('time_zone_id',$zonesel, old('time_zone_id'), ['class' => 'form-control','required'=>'required','id'=>'time_zone_id']); !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('flags','Флаг:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::number('flags',old('flags'),['class' => 'form-control','placeholder'=>'Введите флаги'])!!}
                {!! $errors->first('flags', '<p class="text-danger">:message</p>') !!}
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

