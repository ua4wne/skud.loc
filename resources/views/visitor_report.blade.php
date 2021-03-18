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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-header text-center">{{ $head }}</h2>
            <div class="x_content">
                {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'new_form']) !!}

                <div class="form-group">
                    {!! Form::label('start', 'Начало периода:',['class'=>'col-xs-3 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('start', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-').'01'),['class' => 'form-control','id'=>'start','required'=>'required']) }}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('finish', 'Конец периода:',['class'=>'col-xs-3 control-label']) !!}
                    <div class="col-xs-8">
                        {{ Form::date('finish', \Carbon\Carbon::createFromFormat('Y-m-d', date('Y-m-d')),['class' => 'form-control','id'=>'finish','required'=>'required']) }}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('renter_id', 'Арендатор:',['class'=>'col-xs-3 control-label']) !!}
                    <div class="col-xs-8">
                        {!! Form::select('renter_id', $rentsel, old('renter_id[]'),['class' => 'select2 form-control','id'=>'renter_id']); !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-offset-2 col-xs-8">
                        {!! Form::button('<span class="fa  fa-bar-chart-o"></span> Сформировать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'report','value' => 'report','id'=>'report']) !!}
{{--                        {!! Form::button('<span class="fa  fa-file-excel-o"></span> Скачать', ['class' => 'btn btn-primary','type'=>'submit','name' => 'export','value' => 'export','id'=>'export']) !!}--}}
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
            <a href="#"
               onclick="$('.x_content').show(); $('#result').hide(); $('.fa-plus-square-o').hide(); return false;"><i
                    class="fa fa-plus-square-o fa-lg" aria-hidden="true"></i></a>
            <div class="x_panel" id="result">
                <div id="table-data"></div>
            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/select2.min.js"></script>
    <script>

        $('#result').hide();
        $('.fa-plus-square-o').hide();
        $('.select2').css('width', '100%').select2({allowClear: false});
        $("#renter_id").prepend($('<option value="0">Выберите организацию</option>'));
        $("#renter_id :first").attr("selected", "selected");
        $("#renter_id :first").attr("disabled", "disabled");

        $('#report').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#new_form").find(":input").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).attr("required") == 'required') { //обязательное для заполнения поле формы?
                    if (!$(this).val()) {// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error = 1;// определяем индекс ошибки
                    } else {
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if (error) {
                alert("Необходимо заполнять все обязательные поля!");
                return false;
            }
            let start = $('#start').val();
            let finish = $('#finish').val();
            let renter_id = $('#renter_id').val();
            $.ajax({
                url: '{{ route('visitorsReport') }}',
                type: 'POST',
                data: {'start': start, 'finish': finish, 'renter_id': renter_id},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    $('#result').show();
                    //alert("Сервер вернул вот что: " + res);
                    $("#table-data").html(res);
                    $(".text-header").html('<h4>Посетители за период с ' + start + ' по ' + finish + '</h4>');
                    $('.x_content').hide();
                    $('.fa-plus-square-o').show();
                },
                error: function (xhr, response) {
                    alert('Error! ' + xhr.responseText);
                }
            });
        });

        $('#export').click(function (e) {
            e.preventDefault();
            let error = 0;
            $("#new_form").find(":input").each(function () {// проверяем каждое поле ввода в форме
                if ($(this).attr("required") == 'required') { //обязательное для заполнения поле формы?
                    if (!$(this).val()) {// если поле пустое
                        $(this).css('border', '1px solid red');// устанавливаем рамку красного цвета
                        error = 1;// определяем индекс ошибки
                    } else {
                        $(this).css('border', '1px solid green');// устанавливаем рамку зеленого цвета
                    }

                }
            })
            if (error) {
                alert("Необходимо заполнять все обязательные поля!");
                return false;
            }
            let start = $('#start').val();
            let finish = $('#finish').val();
            let renter_id = $('#renter_id').val();
            alert('Export');
        });

    </script>
@endsection
