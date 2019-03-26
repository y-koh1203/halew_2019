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
    <!-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('c2').addEventListener('change', () => {
                document.getElementById('c1');
            })
        })
    </script> -->
    <body>
        <div class="container">    
            <div class="row">
                <h1>sign up</h1>
            </div>    
            <div class="row">
                <form action="{{ action('AdminController@registration') }}" method="post">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="text" name="name">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <input type="password" name="password">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <input type="text" name="login_id">
                            </div>
                        </div>

                        <div class="row">
                            <select name="charge1" id="c1">
                            @foreach($subjects as $key => $subject)
                                <option value="@php echo $key + 1; @endphp">{{ $subject->subject_name }}</option>
                            @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <select name="charge2" id="c2">
                                <option value="0">未選択</option>
                            @foreach($subjects as $key => $subject)
                                <option value="@php echo $key + 1; @endphp">{{ $subject->subject_name }}</option>
                            @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <button type="submit">登録</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </body>
</html>
