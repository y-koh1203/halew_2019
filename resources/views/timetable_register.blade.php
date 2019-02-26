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
            let count = 0;
            const ss = document.getElementsByClassName('select_subject');
            const id = {{ $class_id }};
            Array.prototype.forEach.call(ss, v => {
                v.addEventListener('change', () => {
                    const lecture_id = v.value;
                    if(lecture_id == 0){
                        if(v.style.border === '2px solid red'){
                            count--;
                        }
                        v.style.border = '2px solid green';

                    if(count > 0){
                        document.getElementById('error').innerHTML = '<span>他クラスの授業と重複している教員があります。<span>'; 
                        document.getElementById('btn-submit').disabled = true;
                    }else{
                        document.getElementById('error').innerHTML = '<span></span>';
                        document.getElementById('btn-submit').disabled = false;
                    }
                        
                        return false;
                    }
                    const splited_code = v.name.split('-');
                    
                    const day = splited_code[0];
                    const day_num = splited_code[1];
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

                    xhr.open( 'GET', 'http://localhost/admin/check/'+day+'/'+day_num+'/'+lecture_id , false );
                    xhr.send();
                    xhr.abort(); // 再利用する際にも abort() しないと再利用できないらしい.

                    if(JSON.parse(data).result === true){
                        if(v.style.border === '2px solid red'){
                            count--;
                        }
                        v.style.border = '2px solid green';
                    }else{
                        v.style.border = '2px solid red';
                        count++;
                    }

                    if(count > 0){
                        document.getElementById('error').innerHTML = '<span>他クラスの授業と重複している教員があります。<span>'; 
                        document.getElementById('btn-submit').disabled = true;
                    }else{
                        document.getElementById('error').innerHTML = '<span></span>';
                        document.getElementById('btn-submit').disabled = false;
                    }
                });
            });
        });
    </script>
    <style>
        td, th{
            empty-cells: show;
            text-align: center;
        }

        #error{
            color: red;
        }
    </style>
    <body>
        <div class="container">    
            <div class="row">
                <h1>clsss</h1>
            </div>   
            <p id="error"></p> 
            <div class="row">
                <Form action="{{ url('/admin/classes/'.$class_id.'/timetable/registration') }}" method="post" id="tt-form">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th></th>
                            <th>日曜日</th>
                            <th>月曜日</th>
                            <th>火曜日</th>
                            <th>水曜日</th>
                            <th>木曜日</th>
                            <th>金曜日</th>
                            <th>土曜日</th>
                        </thead>
                        <tbody>
                        @for($i = 1; $i <= 6; $i++)
                            <tr>
                                <th>{{ $i }}</th>
                                @for($j = 1; $j <= 7; $j++)
                                <td>
                                    <div>
                                        <select name="{{$j}}-{{$i}}" id="{{$j}}-{{$i}}" class="form-control select_subject">
                                            <option value="0">授業無し</option>
                                        @foreach($lectures as $lecture)
                                            <option value="{{ $lecture->id }}">{{ $lecture->subject_name }}:{{ $lecture->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </td>
                                @endfor
                            </tr>                       
                        @endfor
                        </tbody>
                    </table>
                    {{ csrf_field() }}
                    <div>
                        <button type="submit" class="btn btn-lg btn-primary" id="btn-submit">登録する</button>
                    </div>      
                </Form>
            </div>
        </div>
    </body>
</html>
