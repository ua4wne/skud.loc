@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="{{ route('main') }}">Рабочий стол</a></li>
    <li><a href="{{ route('visitors') }}">{{ $title }}</a></li>
    <li class="active">{{ $head }}</li>
</ul>
<!-- END BREADCRUMB -->
<!-- page content -->

<div class="x_panel">
    <h2 class="text-center">{{ $head }}</h2>
    {!! Form::open(['url' => route('visitorEdit',['id'=>$data['id']]),'class'=>'form-horizontal','method'=>'POST','id'=>'new_form','files'=>'true']) !!}

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Фамилия: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::text('lname',$data['lname'],['class' => 'form-control','placeholder'=>'Укажите фамилию','maxlength'=>'50','required'=>'required'])!!}
            {!! $errors->first('lname', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Имя: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::text('fname',$data['fname'],['class' => 'form-control','placeholder'=>'Укажите имя','maxlength'=>'50','required'=>'required'])!!}
            {!! $errors->first('fname', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('mname','Отчество:',['class' => 'col-xs-3 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('mname',$data['mname'],['class' => 'form-control','placeholder'=>'Укажите отчество','maxlength'=>'50'])!!}
            {!! $errors->first('mname', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Организация: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::select('renter_id',$orgsel, $data['renter_id'], ['class' => 'form-control','required'=>'required','id'=>'renter_id']); !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Является сотрудником: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::select('employee',['1' => 'Да', '0' => 'Нет'], $data['employee'], ['class' => 'form-control','required'=>'required']); !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Карта доступа: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::text('card',$data['card'],['class' => 'form-control','placeholder'=>'Укажите карту доступа','maxlength'=>'20','required'=>'required'])!!}
            {!! $errors->first('card', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Документ: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::select('doc_type_id',$docsel, $data['doc_type_id'], ['class' => 'form-control','required'=>'required']); !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('doc_series','Серия документа:',['class' => 'col-xs-3 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('doc_series',$data['doc_series'],['class' => 'form-control','placeholder'=>'Укажите серию документа','maxlength'=>'7'])!!}
            {!! $errors->first('doc_series', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('doc_num','Номер документа:',['class' => 'col-xs-3 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('doc_num',$data['doc_num'],['class' => 'form-control','placeholder'=>'Укажите номер документа','maxlength'=>'10'])!!}
            {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
        </div>
    </div>

    <div class="form-group">
        <label class="col-xs-3 control-label">
            Транспортное средство: <span class="symbol required red" aria-required="true">*</span>
        </label>
        <div class="col-xs-8">
            {!! Form::select('car_id',$carsel, $data['car_id'], ['class' => 'form-control','required'=>'required','id'=>'car_id']); !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('car_num','Регистрационный номер:',['class' => 'col-xs-3 control-label'])   !!}
        <div class="col-xs-8">
            {!! Form::text('car_num',$data['car_num'],['class' => 'form-control','placeholder'=>'Укажите номер ТС','maxlength'=>'10'])!!}
            {!! $errors->first('car_num', '<p class="text-danger">:message</p>') !!}
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
        {!! Form::label('image', 'Изображение:',['class'=>'col-xs-3 control-label']) !!}
        <div class="col-xs-8">
            {!! Form::file('image', ['class' => 'form-control','data-buttonText'=>'Выберите файл изображения','data-buttonName'=>"btn-primary",'data-placeholder'=>"Файл не выбран"]) !!}
            {!! $errors->first('image', '<p class="text-danger">:message</p>') !!}
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
