@extends('layouts.main')

@section('content')
    <!-- page content -->
    <div class="row top_tiles">
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-users"></i></div>
                <div class="count">?</div>
                <h3>На территории</h3>
                <p>Число посетителей на территории.</p>
            </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-car"></i></div>
                <div class="count">{{ $ts }}</div>
                <h3>Автотранспорт</h3>
                <p>Количество ТС на территории.</p>
            </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-arrow-down"></i></div>
                <div class="count">{{ $in }}</div>
                <h3>Вход</h3>
                <p>Вошло за день.</p>
            </div>
        </div>
        <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-arrow-up"></i></div>
                <div class="count">{{ $out }}</div>
                <h3>Выход</h3>
                <p>Вышло за день.</p>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newTS" tabindex="-1" role="dialog" aria-labelledby="newTS" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                    </button>
                    <h4 class="modal-title" id="role-title">Транспортное средство</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '#','id'=>'new_car','class'=>'form-horizontal','method'=>'POST']) !!}

                    <div class="form-group">

                        <div class="form-group">
                            <label class="col-xs-3 control-label">
                                Марка ТС: <span class="symbol required red" aria-required="true">*</span>
                            </label>
                            <div class="col-xs-8">
                                {!! Form::text('tsname',old('tsname'),['class' => 'form-control','placeholder'=>'Укажите марку транспортного средства','maxlength'=>'50','required'=>'required','id'=>'tsname'])!!}
                                {!! $errors->first('tsname', '<p class="text-danger">:message</p>') !!}
                            </div>
                        </div>

                    </div>

                    {!! Form::close() !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="add_car">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="col-xs-9">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane active" id="control">
                                <div class="row">
                                    <div class="col-md-4"><img src="" id="photo"></div>
                                    <div class="col-md-8">
                                        <table class="table table-striped table-bordered">
                                            <tbody>
                                            <tr>
                                                <th>ФИО</th>
                                                <td id="vfio"></td>
                                            </tr>
                                            <tr>
                                                <th>Организация</th>
                                                <td id="vfirm"></td>
                                            </tr>
                                            <tr>
                                                <th>Точка прохода</th>
                                                <td id="vdevice"></td>
                                            </tr>
                                            <tr>
                                                <th>Карта доступа</th>
                                                <td id="vcard"></td>
                                            </tr>
                                            <tr>
                                                <th>Событие</th>
                                                <td id="event"></td>
                                            </tr>
                                            <tr>
                                                <th>Время события</th>
                                                <td id="vtime"></td>
                                            </tr>
                                            <tr>
                                                <th>Марка ТС</th>
                                                <td id="vcar"></td>
                                            </tr>
                                            <tr>
                                                <th>Регистрационный №</th>
                                                <td id="vnum"></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @if(\App\User::hasRole('admin') || \App\User::hasRole('guard'))
                            <div class="tab-pane" id="new_card">
                                <h2 class="text-center">{{$head}}</h2>
                                {!! Form::open(['url' => '#','class'=>'form-horizontal','method'=>'POST','id'=>'new_form']) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label">
                                                Фамилия: <span class="symbol required red" aria-required="true">*</span>
                                            </label>
                                            <div class="col-xs-8">
                                                {!! Form::text('lname',old('lname'),['class' => 'form-control','placeholder'=>'Укажите фамилию посетителя','maxlength'=>'50','required'=>'required','id'=>'lname'])!!}
                                                {!! $errors->first('lname', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label">
                                                Имя: <span class="symbol required red" aria-required="true">*</span>
                                            </label>
                                            <div class="col-xs-8">
                                                {!! Form::text('fname',old('fname'),['class' => 'form-control','placeholder'=>'Укажите имя посетителя','maxlength'=>'50','required'=>'required','id'=>'fname'])!!}
                                                {!! $errors->first('fname', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('mname','Отчество:',['class' => 'col-xs-3 control-label'])   !!}
                                            <div class="col-xs-8">
                                                {!! Form::text('mname',old('text'),['class' => 'form-control','placeholder'=>'Укажите отчество посетителя','maxlength'=>'50','id'=>'mname'])!!}
                                                {!! $errors->first('mname', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label">
                                                Организация: <span class="symbol required red"
                                                                   aria-required="true">*</span>
                                            </label>
                                            <div class="col-xs-8">
                                                {!! Form::select('renter_id',$orgsel, old('renter_id'), ['class' => 'select2 form-control','required'=>'required','id'=>'renter_id']); !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label">
                                                Карта доступа: <span class="symbol required red"
                                                                     aria-required="true">*</span>
                                            </label>
                                            <div class="col-xs-8">
                                                {!! Form::text('card',old('card'),['class' => 'form-control','placeholder'=>'Укажите карту доступа','maxlength'=>'20','required'=>'required','id'=>'card'])!!}
                                                {!! $errors->first('card', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label">
                                                Документ: <span class="symbol required red"
                                                                aria-required="true">*</span>
                                            </label>
                                            <div class="col-xs-8">
                                                {!! Form::select('doc_type_id',$docsel, old('doc_type_id'), ['class' => 'form-control','required'=>'required','id'=>'doc_type_id']); !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('doc_series','Серия документа:',['class' => 'col-xs-3 control-label'])   !!}
                                            <div class="col-xs-8">
                                                {!! Form::text('doc_series',old('doc_series'),['class' => 'form-control','placeholder'=>'Укажите серию документа','maxlength'=>'7','required'=>'required','id'=>'doc_series'])!!}
                                                {!! $errors->first('doc_series', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('doc_num','Номер документа:',['class' => 'col-xs-3 control-label'])   !!}
                                            <div class="col-xs-8">
                                                {!! Form::text('doc_num',old('doc_num'),['class' => 'form-control','placeholder'=>'Укажите номер документа','maxlength'=>'10','required'=>'required','id'=>'doc_num'])!!}
                                                {!! $errors->first('doc_num', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label">
                                                Автомобиль: <span class="symbol required red"
                                                                  aria-required="true">*</span>
                                            </label>
                                            <div class="col-xs-7 cartype">
                                                {!! Form::select('car_id',$carsel, old('car_id'), ['class' => 'select2 form-control','required'=>'required','id'=>'car_id']); !!}
                                            </div>
                                            <div class="col-xs-1">
                                                <button class="btn btn-default" type="button" data-toggle="modal"
                                                        data-target="#newTS" title="Добавить новую марку ТС"><i
                                                        class="fa fa-truck fa-lg green" aria-hidden="true"></i></button>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('car_num','Рег. номер:',['class' => 'col-xs-3 control-label'])   !!}
                                            <div class="col-xs-8">
                                                {!! Form::text('car_num',old('car_num'),['class' => 'form-control','placeholder'=>'Укажите номер ТС','maxlength'=>'10','id'=>'car_num'])!!}
                                                {!! $errors->first('car_num', '<p class="text-danger">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-xs-offset-2 col-xs-10">
                                        {!! Form::button('Сохранить', ['class' => 'btn btn-primary pull-right','type'=>'submit','id'=>'save_btn']) !!}
                                    </div>
                                </div>

                                {!! Form::close() !!}
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <!-- required for floating -->
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs tabs-right">
                            <li class="active"><a href="#control" data-toggle="tab" aria-expanded="true">Контроль</a>
                            </li>
                            @if(\App\User::hasRole('admin') || \App\User::hasRole('guard'))
                            <li class=""><a href="#new_card" data-toggle="tab" aria-expanded="false">Регистрация</a>
                            </li>
                            @endif
                        </ul>
                        <div class="x_content">
                            <a href="{{route('main')}}" class="btn btn-app pull-right">
                                <i class="fa fa-repeat green"></i> Обновить
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h2 class="text-center">Последние события</h2>
                    @if($events)
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>Имя</th>
                                <th>Фамилия</th>
                                <th>Карта №</th>
                                <th>Организация</th>
                                <th>Марка ТС</th>
                                <th>Рег. №</th>
                                <th>Событие</th>
                                <th>Дата события</th>
                            </tr>
                            </thead>
                            <tbody id="t_body">

                            @foreach($events as $row)

                                <tr>
                                    <td>{{ $row->fname }}</td>
                                    <td>{{ $row->lname }}</td>
                                    <td></td>
                                    <td>{{ $row->card }}</td>
                                    <td>{{ $row->ts }}</td>
                                    <td>{{ $row->car_num }}</td>
                                    <td>{{ $row->text }}</td>
                                    <td>{{ $row->event_time }}</td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    <script src="/js/jquery.inputmask.bundle.min.js"></script>
    <script src="/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            //select2
            $('.select2').css('width', '90%').select2({allowClear: false})
            $("#renter_id").prepend($('<option value="0">Выберите организацию</option>'));
            $("#renter_id :first").attr("selected", "selected");
            $("#renter_id :first").attr("disabled", "disabled");
            $("#car_id").prepend($('<option value="0">Выберите марку ТС</option>'));
            $("#car_id :first").attr("selected", "selected");
            $("#car_id :first").attr("disabled", "disabled");
            $("#doc_type_id").prepend($('<option value="0">Выберите документ</option>'));
            $("#doc_type_id :first").attr("selected", "selected");
            $("#doc_type_id :first").attr("disabled", "disabled");
            let options = {
                'backdrop': 'true',
                'keyboard': 'true'
            }
            $('#basicModal').modal(options);
        });

        $('#add_car').click(function(e){
            e.preventDefault();
            if(!$('#tsname').val()){
                alert('Марка машины не заполнена!');
                $('#tsname').css('border', '1px solid red');// устанавливаем рамку красного цвета
                return false;
            }
            var data = $('#new_car').serialize();
            $.ajax({
                url: '{{ route('add_truck') }}',
                type: 'POST',
                data: data,
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    if(res=='ERR'){
                        alert('Попытка ввода дублирующего значения. Такое ТС уже есть в справочнике.');
                        $('#new_car')[0].reset();
                    }
                    else if(res=='NOT'){
                        alert('Ошибка БД!');
                        $('#new_car')[0].reset();
                    }
                    else{
                        $('.cartype').html(res);
                        $('.select2').css('width','100%').select2({allowClear:false});
                        $("#newTS").modal('hide');
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status+' '+thrownError);
                }
            });
            return false;
        });

        $('#save_btn').click(function(e){
            e.preventDefault();
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
                alert("Необходимо заполнять все обязательные поля!");
                return false;
            }
            else{
                $.ajax({
                    type: 'POST',
                    url: '{{ route('add_visitor') }}',
                    data: $('#new_form').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK'){
                            alert('Запись добавлена!')
                            $('#new_form')[0].reset();
                        }
                        if(res=='NO CARD'){
                            alert('Карта '+$('#card').val()+' не обнаружена в системе! Для авторизации карты обратитесь к начальнику охраны.');
                            $('#card').focus();
                        }
                        if(res=='NO SHARE'){
                            alert('Карта '+$('#card').val()+' не является гостевой! Выберите другую карту, а эту сдайте начальнику охраны.');
                            $('#card').focus();
                        }
                        if(res=='STOP'){
                            alert('Проход по карте '+$('#card').val()+' запрещен! Для авторизации карты обратитесь к начальнику охраны.');
                            $('#card').focus();
                        }
                        if(res=='ERR')
                            alert('Ошибка обновления данных.');
                        if(res=='NO')
                            alert('Выполнение операции запрещено!');
                    }
                });
            }
        });

        function findVisitor(){
            let doc_id = $('#doc_type_id').val();
            let series = $('#doc_series').val();
            let doc_num = $('#doc_num').val();
            $.ajax({
                url: '{{ route('find_visitor') }}',
                type: 'POST',
                data: {'doc_type_id':doc_id,'doc_series':series,'doc_num':doc_num},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    //alert("Сервер вернул вот что: " + res);
                    let obj = JSON.parse(res);
                    if (typeof obj === 'object') {
                        $('#lname').val(obj.lname);
                        $('#fname').val(obj.fname);
                        $('#mname').val(obj.mname);
                        $('#card').val(obj.card);
                        $('#renter_id').val(obj.renter_id);
                        //$('.select2').css('width','100%').select2({allowClear:false});
                        $("#renter_id option[value='"+obj.renter_id+"']").attr("selected", "selected");
                        $('#car_id').val(obj.car_id);
                        $('.select2').css('width','100%').select2({allowClear:false});
                        $("#car_id option[value='"+obj.car_id+"']").attr("selected", "selected");
                        $('#car_num').val(obj.car_num);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status+' '+thrownError);
                }
            });
        }

        function check_fields(){
            if($('#doc_type_id').val().length === 0 || $('#doc_series').val().length === 0 || $('#doc_num').val().length === 0)
                return false;
            else
                return true;
        }

        $( "#doc_type_id" ).blur(function() {
            if(check_fields())
                findVisitor();
        });

        $( "#doc_series" ).blur(function() {
            if(check_fields())
                findVisitor();
        });

        $( "#doc_num" ).blur(function() {
            if(check_fields())
                findVisitor();
        });

        function show() {
            $.ajax({
                url: '{{ route('show_main') }}',
                type: 'POST',
                data: {'data': 'check'},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                success: function (res) {
                    //alert("Сервер вернул вот что: " + res);
                    let obj = jQuery.parseJSON(res);
                    if (typeof obj === 'object') {
                        $('#photo').attr('src', obj.photo)
                        $('#vfio').text(obj.fio);
                        $('#vfirm').text(obj.firm);
                        $('#vdevice').text(obj.device);
                        $('#vcard').text(obj.card);
                        $('#event').text(obj.event);
                        $('#vtime').text(obj.time);
                        $('#vcar').text(obj.car);
                        $('#vnum').text(obj.num);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + ' ' + thrownError);
                }
            });

            $.ajax({
                url: '{{ route('show_last') }}',
                type: 'POST',
                data: {'data': 'check'},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                success: function (res) {
                    //alert("Сервер вернул вот что: " + res);
                    $('#t_body').html(res);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + ' ' + thrownError);
                }
            });
        }

        window.setInterval(function () {
            show();
        }, 3000);
    </script>

@endsection
