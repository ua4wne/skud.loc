@extends('layouts.main')

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('roles') }}">Роли</a></li>
        <li class="active">Новая запись</li>
    </ul>
    <!-- END BREADCRUMB -->
    <!-- page content -->

    <div class="x_panel">
        <h2 class="text-center">Новая роль</h2>
        {!! Form::open(['url' => route('roleAdd'),'class'=>'form-horizontal','method'=>'POST','id'=>'new_form']) !!}

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Символьный код: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('code',old('code'),['class' => 'form-control','placeholder'=>'Введите символьный код','required'=>'required'])!!}
                {!! $errors->first('code', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">
                Наименование: <span class="symbol required red" aria-required="true">*</span>
            </label>
            <div class="col-xs-8">
                {!! Form::text('name',old('name'),['class' => 'form-control','placeholder'=>'Введите наименование роли','required'=>'required'])!!}
                {!! $errors->first('name', '<p class="text-danger">:message</p>') !!}
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
