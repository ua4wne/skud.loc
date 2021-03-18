@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="{{ route('main') }}">Рабочий стол</a></li>
    <li><a href="{{ route('renters') }}">{{ $title }}</a></li>
    <li class="active">{{ $head }}</li>
</ul>
<!-- END BREADCRUMB -->
<!-- page content -->

<div class="x_panel">
    <h2 class="text-center">{{ $head }}</h2>
    {!! Form::open(['url' => route('renterEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST','id'=>'new_form']) !!}

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Наименование: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::text('title',$data['title'],['class' => 'form-control','placeholder'=>'Введите наименование','maxlength'=>'100','required'=>'required'])!!}
            {!! $errors->first('title', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Локация: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::text('area',$data['area'],['class' => 'form-control','placeholder'=>'Укажите локацию','maxlength'=>'50','required'=>'required'])!!}
            {!! $errors->first('area', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Контактное лицо: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::text('agent',$data['agent'],['class' => 'form-control','placeholder'=>'Укажите контактное лицо','maxlength'=>'70','required'=>'required'])!!}
            {!! $errors->first('agent', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('phone','Телефон:',['class' => 'col-xs-3 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('phone',$data['phone'],['class' => 'form-control','placeholder'=>'Укажите телефон','maxlength'=>'15','data-inputmask'=>'\'mask\' : \'8(999)999-99-99\''])!!}
            {!! $errors->first('phone', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('email','Телефон:',['class' => 'col-xs-3 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::email('email',$data['email'],['class' => 'form-control','placeholder'=>'Укажите E-Mail','maxlength'=>'70'])!!}
            {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Статус: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::select('status',['1' => 'Действующая', '0' => 'Не действующая'], $data['status'], ['class' => 'form-control','required'=>'required']); !!}
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
