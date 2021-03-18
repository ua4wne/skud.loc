<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="favicon.ico" type="image/ico"/>

    <title>{{ $title ?? '' }}</title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="/css/custom.min.css" rel="stylesheet">
    <link href="/css/select2.min.css" rel="stylesheet">
    @section('user_css')

    @show
</head>

<body class="nav-md">
<div class="container body">
    <div class="main_container">
        @section('left_menu')
            <div class="col-md-3 left_col">
                <div class="left_col scroll-view">
                    <div class="navbar nav_title" style="border: 0;">
                        <a href="{{ route('main') }}" class="site_title"><i class="fa fa-home" aria-hidden="true"></i> <span>СКУД Рубеж</span></a>
                    </div>

                    <div class="clearfix"></div>

                    <!-- menu profile quick info -->
                    <div class="profile clearfix">
                        <div class="profile_pic">
                            @if(Auth::user()->image)
                                <img src="{{ Auth::user()->image }}" alt="..." class="img-circle profile_img">
                            @else
                                <img src="/images/male.png" alt="..." class="img-circle profile_img">
                            @endif
                        </div>
                        <div class="profile_info">
                            <span>Здравствуйте,</span>
                            <h2>{{ Auth::user()->name }}</h2>
                        </div>
                    </div>
                    <!-- /menu profile quick info -->

                    <br/>

                    <!-- sidebar menu -->
                    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                        <div class="menu_section">
                            <ul class="nav side-menu">
                                <li><a href="{{ route('main') }}"><i class="fa fa-tachometer" aria-hidden="true"></i>Рабочий
                                        стол </a></li>
                                <li><a href="{{ route('renters') }}"><i class="fa fa-users" aria-hidden="true"></i>Организации</a></li>
                                <li><a href="{{ route('visitors') }}"><i class="fa fa-user" aria-hidden="true"></i>Сотрудники</a></li>
                                <li><a><i class="fa fa-address-book-o" aria-hidden="true"></i> Справочники <span
                                            class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('cars') }}">Марки автотранспорта</a></li>
                                        <li><a href="{{ route('doc-types') }}">Типы документов</a></li>
                                    </ul>
                                </li>
                                <li><a><i class="fa fa-bar-chart-o"></i> Отчеты <span class="fa fa-chevron-down"></span></a>
                                    <ul class="nav child_menu">
                                        <li><a href="{{ route('traffic_flow') }}">Поток ТС</a></li>
                                        <li><a href="{{ route('visitorsReport') }}">Посетители</a></li>
                                    </ul>
                                </li>
                                @if(\App\User::hasRole('admin'))
                                    <li><a href="{{ route('tasks') }}"><i class="fa fa-tasks" aria-hidden="true"></i>Очередь задач</a></li>
                                    <li><a href="{{ route('eventlogs') }}"><i class="fa fa-bell-o" aria-hidden="true"></i>Системный лог</a></li>
                                    <li><a><i class="fa fa-cogs" aria-hidden="true"></i> Настройки <span
                                                class="fa fa-chevron-down"></span></a>
                                        <ul class="nav child_menu">
                                            <li><a href="{{ route('users') }}">Пользователи</a></li>
                                            <li><a href="{{ route('roles') }}">Роли</a></li>
                                            <li><a href="{{ route('actions') }}">Разрешения</a></li>
                                            <li><a href="{{ route('cards') }}">Карты доступа</a></li>
                                            <li><a href="{{ route('time-zones') }}">Временные зоны</a></li>
                                            <li><a href="{{ route('event-types') }}">Виды событий</a></li>
                                            <li><a href="{{ route('devices') }}">Контроллеры СКУД</a></li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <!-- /sidebar menu -->

                    <!-- /menu footer buttons -->
                    <div class="sidebar-footer hidden-small">
                        @if(\App\User::hasRole('admin') || \App\User::hasRole('control'))
                        <a data-toggle="tooltip" data-placement="top" title="Карты доступа" href="{{ route('cards') }}">
                            <span class="glyphicon glyphicon-credit-card" aria-hidden="true"></span>
                        </a>
                        @endif
                        @if(\App\User::hasRole('admin'))
                        <a data-toggle="tooltip" data-placement="top" title="Очередь задач" href="{{ route('tasks') }}">
                            <span class="glyphicon glyphicon-tasks" aria-hidden="true"></span>
                        </a>
                        @endif
                        @if(\App\User::hasRole('admin') || \App\User::hasRole('control'))
                        <a data-toggle="tooltip" data-placement="top" title="Журнал событий" href="#">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                        </a>
                        @endif
                        <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('logout') }}">
                            <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                        </a>
                    </div>
                    <!-- /menu footer buttons -->
                </div>
            </div>
        @show

        @section('top_nav')
        <!-- top navigation -->
            <div class="top_nav">
                <div class="nav_menu">
                    <nav>
                        <div class="nav toggle">
                            <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>

                        <ul class="nav navbar-nav navbar-right">
                            <li class="">
                                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                                   aria-expanded="false">
                                    @if(Auth::user()->image)
                                        <img src="{{ Auth::user()->image }}" alt="...">
                                    @else
                                        <img src="/images/male.png" alt="...">
                                    @endif
                                    {{ Auth::user()->login }}
                                    <span class=" fa fa-angle-down"></span>
                                </a>
                                <ul class="dropdown-menu dropdown-usermenu pull-right">
                                    <li><a href="#"><i class="fa fa-cog pull-right"></i> Профиль</a></li>
                                    <li><a href="{{ route('logout') }}"><i class="fa fa-sign-out pull-right"></i> Log
                                            Out</a></li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <!-- /top navigation -->
        @show
        <div class="right_col" role="main">
        @section('tile_widget')
            <!-- top tiles -->
                <!-- /top tiles -->
        @endsection
        @yield('tile_widget')

        @yield('content')
        </div>
        @section('footer')
            <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        Разработано для выставки домов "Малоэтажная Страна". 2014 - 2020
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->
        </div>
    </div>

    <!-- jQuery -->
    <script src="/js/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <script src="/js/fastclick.js"></script>
    <!-- NProgress -->
    <script src="/js/nprogress.js"></script>
    <!-- iCheck -->
{{--<script src="/js/icheck.min.js"></script>--}}


<!-- Custom Theme Scripts -->
    <script src="/js/custom.min.js"></script>

@show

@section('user_script')

@show

</body>
</html>
