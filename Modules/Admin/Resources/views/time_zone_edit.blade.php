@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('time-zones') }}">{{$title}}</a></li>
        <li class="active">{{ $head }}</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_panel">
        <h2 class="text-center">{{$head}}</h2>
        {!! Form::open(['url' => route('tzoneEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST','id'=>'new_form']) !!}

        <div class="form-group">
            {!! Form::label('zone','Временная зона №:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::number('zone',$data['zone'],['class' => 'form-control','placeholder'=>'Введите число','disabled'=>'disabled'])!!}
                {!! $errors->first('zone', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Время начала: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('begin',$data['begin'],['class' => 'form-control','placeholder'=>'Введите время начала действия','maxlength'=>'8','required'=>'required','data-inputmask'=>'\'mask\' : \'99:99:99\''])!!}
                {!! $errors->first('begin', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Время окончания: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('end',$data['end'],['class' => 'form-control','placeholder'=>'Введите время окончания действия','maxlength'=>'8','required'=>'required','data-inputmask'=>'\'mask\' : \'99:99:99\''])!!}
                {!! $errors->first('end', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Дни недели: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('days',$data['days'],['class' => 'form-control','placeholder'=>'Введите строку активности дней недели','maxlength'=>'8','required'=>'required'])!!}
                {!! $errors->first('days', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('text','Описание:',['class' => 'col-xs-3 control-label'])   !!}
            <div class="col-xs-8">
                {!! Form::text('text',$data['text'],['class' => 'form-control','placeholder'=>'Введите описание зоны','maxlength'=>'254'])!!}
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
    <script src="/js/jquery.inputmask.bundle.min.js"></script>
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

