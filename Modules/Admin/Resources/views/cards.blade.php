@extends('layouts.main')
@section('user_css')
    <link href="/css/DT_bootstrap.css" rel="stylesheet" media="screen">
@endsection

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
    <div id="loader"></div> <!--  идентификатор загрузки (анимация) - ожидания выполнения-->
    <div class="row">
        <!-- MODAL -->
        <div class="modal fade" id="taskCard" tabindex="-1" role="dialog" aria-labelledby="taskCard" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times-circle fa-lg" aria-hidden="true"></i>
                        </button>
                        <h4 class="modal-title" id="role-title">Выбор контроллера СКУД</h4>
                    </div>
                    <div class="modal-body">
                        {!! Form::open(['url' => '#','id'=>'new_form','class'=>'form-horizontal','method'=>'POST']) !!}

                        <div class="form-group">
                            <div class="col-xs-8">
                                {!! Form::hidden('task','',['class' => 'form-control','required'=>'required','id'=>'task']) !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-xs-4 control-label">
                                Контроллеры СКУД: <span class="symbol required red" aria-required="true">*</span>
                            </label>
                            <div class="col-xs-8">
                                {!! Form::select('device_id',$devsel, old('device_id'), ['class' => 'form-control','required'=>'required','id'=>'device_id']); !!}
                            </div>
                        </div>

                        {!! Form::close() !!}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                        <button type="button" class="btn btn-primary" id="add_task">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- MODAL -->
        <h2 class="text-center">{{ $head }}</h2>
        @if($rows)
            <div class="x_content">
                <a href="{{route('cardAdd')}}">
                    <button type="button" class="btn btn-default btn-sm"><i class="fa fa-plus green"
                                                                            aria-hidden="true"></i> Новая запись
                    </button>
                </a>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#taskCard" id="card-load"><i class="fa fa-cloud-upload"
                                                                        aria-hidden="true"></i> Записать все карты
                </button>
                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#taskCard" id="del-card"><i class="fa fa-cloud-upload"
                                                                     aria-hidden="true"></i> Стереть все карты
                </button>
            </div>
            <div class="x_panel">
                <table id="mytable" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Идентификатор</th>
                        <th>Тип карты</th>
                        <th>Статус</th>
                        <th>Флаг</th>
                        <th>Временная зона</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($rows as $row)

                        <tr id="{{ $row->id }}">
                            <td>{{ $row->code }}</td>
                            <td>
                                @if($row->share)
                                    <span role="button" class="label label-info">Гостевая карта</span>
                                @else
                                    <span role="button" class="label label-success">Индивидуальная</span>
                                @endif
                            </td>
                            <td>
                                @if($row->granted)
                                    <span role="button" class="label label-success">Действующая</span>
                                @else
                                    <span role="button" class="label label-danger">Заблокирована</span>
                                @endif
                            </td>
                            <td>{{ $row->flags }}</td>
                            <td>{{ $row->time_zone->zone .' : '. $row->time_zone->text }}</td>

                            <td style="width:140px;">
                                {!! Form::open(['url'=>route('cardEdit',['id'=>$row->id]), 'class'=>'form-horizontal','method' => 'POST', 'onsubmit' => 'return confirmDelete()']) !!}
                                {{ method_field('DELETE') }}
                                <div class="form-group" role="group">
                                    <a href="{{ route('cardEdit',['id'=>$row->id]) }}" class="btn btn-success btn-sm"
                                       type="button" title="Редактировать запись"><i class="fa fa-edit fa-lg"
                                                                                     aria-hidden="true"></i></a>
                                    @if($row->granted)
                                    <a href="{{ route('cardLoad',['id'=>$row->id]) }}" class="btn btn-info btn-sm"
                                       type="button" title="Записать в контроллеры"><i class="fa fa-upload fa-lg"
                                                                                       aria-hidden="true"></i></a>
                                    @else
                                        <a href="{{ route('cardLoad',['id'=>$row->id]) }}" class="btn btn-info btn-sm"
                                           type="button" title="Записать в контроллеры" disabled><i class="fa fa-upload fa-lg"
                                                                                           aria-hidden="true"></i></a>
                                    @endif
                                    {!! Form::button('<i class="fa fa-trash-o fa-lg>" aria-hidden="true"></i>',['class'=>'btn btn-danger btn-sm','type'=>'submit','title'=>'Удалить запись']) !!}
                                </div>
                                {!! Form::close() !!}
                            </td>
                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
    @include('confirm')
    <script src="/js/jquery.dataTables.min.js"></script>
    <script>
        $('#mytable').DataTable({
            "aoColumnDefs": [{
                "aTargets": [0]
            }],
            "language": {
                "processing": "Подождите...",
                "search": "Поиск: ",
                "lengthMenu": "Показать _MENU_ записей",
                "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                "infoEmpty": "Записи с 0 до 0 из 0 записей",
                "infoFiltered": "(отфильтровано из _MAX_ записей)",
                "infoPostFix": "",
                "loadingRecords": "Загрузка записей...",
                "zeroRecords": "Записи отсутствуют.",
                "emptyTable": "В таблице отсутствуют данные",
                "paginate": {
                    "first": "Первая",
                    "previous": "Предыдущая",
                    "next": "Следующая",
                    "last": "Последняя"
                },
                "aria": {
                    "sortAscending": ": активировать для сортировки столбца по возрастанию",
                    "sortDescending": ": активировать для сортировки столбца по убыванию"
                },
                "select": {
                    "rows": {
                        "_": "Выбрано записей: %d",
                        "0": "Кликните по записи для выбора",
                        "1": "Выбрана одна запись"
                    }
                }
            },
            //"aaSorting" : [[1, 'asc']],
            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "Все"] // change per page values here
            ],
            // set the initial value
            "iDisplayLength": 10,
        });

        $('#card-load').click(function(){
            $('#task').val('load');
        });

        $('#del-card').click(function(){
            $('#task').val('del');
        });

        $('#add_task').click(function(e){
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
                alert("Необходимо заполнять все доступные поля!");
                return false;
            }
            else{
                $.ajax({
                    type: 'POST',
                    url: '{{ route('cardTask') }}',
                    data: $('#new_form').serialize(),
                    success: function(res){
                        //alert(res);
                        if(res=='OK'){
                            $(".modal").modal("hide");
                            alert('Задания созданы!');
                        }
                        else
                            alert('Ошибка выполнения.');
                    }
                });
            }
        });

    </script>
@endsection
