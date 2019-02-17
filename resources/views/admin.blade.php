<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-reboot.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-grid.min.css') }}">
    </head>
    <body>
        <h1>sign in</h1>
        <form action="{{ action('AdminController@authnication') }}" method="post">
            <input type="text" name="name" id=""><br>
            <input type="password" name="password" id="">
            {{ csrf_field() }}
            <button type="submit">ログイン</button>
        </form>

        @if(isset($error))
            <p>{{ $error }}</p>
        @endif

        <a href="{{ action('AdminController@signup') }}">登録はこちら</a>
    </body>
</html>
