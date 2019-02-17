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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const elem = document.getElementById('select_class');
            elem.addEventListener('change', () => {
                const id = elem.value;
                let data = {};
                let xhr = new XMLHttpRequest;

                xhr.onreadystatechange = () => {
                    switch ( xhr.readyState ) {
                        case 0:
                            // 未初期化状態.
                            console.log( 'uninitialized!' );
                            break;
                        case 1: // データ送信中.
                            console.log( 'loading...' );
                            break;
                        case 2: // 応答待ち.
                            console.log( 'loaded.' );
                            break;
                        case 3: // データ受信中.
                            console.log( 'interactive... '+xhr.responseText.length+' bytes.' );
                            break;
                        case 4: // データ受信完了.
                            if( xhr.status == 200 || xhr.status == 304 ) {
                                data = xhr.responseText; // responseXML もあり
                                console.log( 'COMPLETE! :'+data );
                            } else {
                                console.log( 'Failed. HttpStatus: '+xhr.statusText );
                            }
                        break;
                    }
                }
                xhr.open( 'GET', 'http://localhost/admin/class/'+id+'/students' , false );
                xhr.send();
                xhr.abort(); // 再利用する際にも abort() しないと再利用できないらしい.

                const parsed_data = JSON.parse(data);

                let tag = '<div>';
                parsed_data.foreach(v => {
                    tag += '<div>'+v.name+'</div>';
                })
                tag += '</div>';
            });
        });
    </script>
    <body>
        <div class="container">    
            <div class="row">
                <h1>register student</h1>
            </div>    
            <div class="row">
                <select name="class" id="select_class">
                    <option value="0">未選択</option>
                    @foreach($classes as $id => $class)
                    <option value="@php echo $id + 1; @endphp">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>    
            <div class="row">
                <form action="{{ action('AdminController@studentRegistration') }}" method="post">
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
                            <button type="submit">登録</button>
                        </div>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </body>
</html>
