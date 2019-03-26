<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @yield('title')

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="{{ asset('css/bootstrap-reboot.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
    </head>
    <style>
        .common { 
            width: 100%;
        }

        .nav-wrap {
            position: sticky;
            top: 0;
            left: 0;
        }

        .sidebar {
            width: 16%;
            background: #37465e;
            height: 100vh;
            position: fixed;
            left: 0;
        }

        .sidebar-wrap {
            width: 100%;
            margin-top: 3%;
            background: lightcoral;
        }

        .sidebar-wrap .sidebar-cell {
            width: 100%;
            font-size: 1.2rem;
            padding: 4% 0;
            background: #37465e;
            position: relative;
            z-index: 2; /* 必要であればリンク要素の重なりのベース順序指定 */
            text-align: center;
        }

        .sidebar-cell a {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            text-indent: -999px;
            z-index: 3; /* 必要であればリンク要素の重なりのベース順序指定 */
        }

        a:link{
            color: #fff;
            text-decoration: none;
        }

        a:visited{
            color: #fff;
        }

        .sidebar-cell:hover {
            background-color: gray;
        }

        .dropdown{
            position: sticky;
            z-index: 99;
        }

        .dropdown-menu{
            /* background-color: #37465e; */
            background: rgba(45,45,45, 1);
            z-index: 99;
        }

        .main {
            width: 84%;
            height: 90%;
            position: fixed;
            right: 0;
            overflow-y: scroll;
            transform: translateZ(0);
            z-index: 0;
        }

        .main-flex {
            width: 100%;
            display: flex;
            flex-direction: row;
            overflow: scroll;
        }

        hr {
            width: 100%;
            height: 2px;
            margin: 1% 0 2% 0;
        }

        .heading{
            margin-top: 3%;
        }

        #logout {
            color: gray;
        }
    </style>
    <body>
        <!-- 共通レイアウト -->
        <div class="common">
            <div class="nav-wrap">
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <a class="navbar-brand" href="/admin/home">Scheduller v1</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mr-auto">
                        </ul>
    
                        <span class="navbar-text">@yield('teacher_name')</span>
                        <ul class="navbar-nav">  
                            <!-- <li>@yield('teacher_name')</li> -->
                            <li class="nav-item">
                               <a href="{{ url('/logout') }}" id="logout" class="dropdown-item">ログアウト</a>    
                            </li>
                        </ul>
                    </div>    
                </nav>
            </div>
           
            <div class="main-flex">
                <div class="sidebar">
                    <div class="sidebar-wrap">
                        <a href="{{ url('/admin/home') }}">
                            <div class="sidebar-cell">
                                TOP
                            </div>
                        </a>
    
                        <a href="{{ url('/admin/lecture') }}">
                            <div class="sidebar-cell">
                                教員管理
                            </div>  
                        </a>
    
                        <a href="{{ url('/admin/timetable') }}">
                            <div class="sidebar-cell">
                                時間割管理
                            </div>
                        </a>

                        <a href="{{ url('/admin/timetable/spot') }}">
                            <div class="sidebar-cell">
                                スポット時間割管理
                            </div>
                        </a>

                        <a href="#">
                            <div class="sidebar-cell">
                                クラス管理
                            </div>
                        </a>

                        <a href="#">
                            <div class="sidebar-cell">
                                授業
                            </div>
                        </a>
                    </div>
                </div>
    
                <div class="main">
                    @yield('body')
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('logout').addEventListener('click', () => {
                    console.log(1);
                    location.href = '/logout';
                })
            })
        </script>
    </body>
</html>