<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Scheduller TOP</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-reboot.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
    </head>
    <style>
        .heading {
            width: 100%;
            padding: 1% 0;
            margin-bottom: 5%;
            background: #37465e;
            text-align: center;
            color: #fff;
        }

        .main{
            width: 50%;
            margin: 1% auto;
            padding: 3% 0;
            text-align: center;
            background: #e9ebee;
        }

        .error-box {
            width: 50%;
            margin: 1% auto;
            border-radius: 3px;
            border: 2px solid red;
        }

        .fa-exclamation-triangle{
            background-color: red;
            padding: 1%;
            /* border-radius: 3px 0 0 3px; */
            border: 2px solid red;
            color: white;
        }

        .form-group{
            width: 60%;
            margin: 3% auto;
        }

    </style>
    <body>
        <h1 class="heading">Scheduller v1</h1>

        @if(isset($error))
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="error-text">{{ $error }}</span>
        </div>
        @endif

        <div class="main">

            <div>Schedullerへログイン</div>

            <form action="{{ action('AdminController@authnication') }}" method="post">
                <div class="form-group">
                    <input type="text" class="form-control" name="login_id" placeholder="教員ログインID">
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="パスワード">
                </div>

                {{ csrf_field() }}
                <button type="submit" class="btn btn-primary">ログイン</button>
                
            </form>

            <!-- <a href="{{ action('AdminController@signup') }}">登録はこちら</a> -->
        </div>
    </body>
</html>
