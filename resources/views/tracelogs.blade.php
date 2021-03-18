@extends('layouts.main')
@section('user_css')
    <!-- dataTables -->
    <link href="/css/DT_bootstrap.css" rel="stylesheet" media="screen">
@endsection

@section('tile_widget')

@endsection

@section('content')
    <!-- START BREADCRUMB -->
    <ul class="breadcrumb">
        <li><a href="{{ route('main') }}">Рабочий стол</a></li>
        <li><a href="{{ route('eventlogs') }}">Системный лог</a></li>
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
                    <a href="{{route('tracelogDel')}}">
                        <button type="button" class="btn btn-default btn-sm" id="clear_btn"><i class="fa fa-trash-o red"
                                                                                aria-hidden="true"></i> Очистить лог
                        </button>
                    </a>
                </div>
                <div class="x_panel">
                    <table id="mytable" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Тип события</th>
                            <th>Описание события</th>
                            <th>Дата события</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($rows as $row)

                            <tr id="{{ $row->id }}">
                                <td>{{ $row->type }}</td>
                                <td>{{ $row->msg }}</td>
                                <td>{{ $row->created_at }}</td>

                                <td style="width:70px;">
                                    {!! Form::open(['url'=>route('tracelogDelOne',['id'=>$row->id]), 'class'=>'form-horizontal','method' => 'GET']) !!}
                                    <div class="form-group" role="group">
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
    </div>
    <!-- /page content -->
@endsection

@section('user_script')
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

        $('#clear_btn').click(function(e){
            let x = confirm("Журнал логов будет очищен!. Продолжить (Да/Нет)?");
            if (x) {
                return true;
            }
            else {

                e.preventDefault();
                return false;
            }
        });
    </script>
@endsection
