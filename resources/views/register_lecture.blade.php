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

                if(id === 0){
                    return false;
                }

                location.href = `{{ url('/admin/classes/${id}/lecture/all') }}`;
            });


            const ss = document.getElementsByClassName('select_subject');
            const class_id = {{ $class_id }}
            Array.prototype.forEach.call(ss, v => {
                v.addEventListener('change', () => {
                    const subject_id = v.value;
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
                    xhr.open( 'GET', 'http://localhost/admin/classes/'+class_id+'/timetable/subject/'+subject_id+'/teachers' , false );
                    xhr.send();
                    xhr.abort();

                    let html = '<option value="0">未選択</option>';
                    const parsed_data = JSON.parse(data);
                    parsed_data.forEach(o => {
                        html += `<option value="${o.id}">${o.name}</option>`;
                        
                    })
                    document.getElementById(v.id+'-t').innerHTML = html;
                });
            });
        });
    </script>
    <body>
        <div class="container">    
            <div class="row">
                <h1>register lecture and teacher</h1>
            </div>    
            <p>クラスごとの科目と、科目担当を設定する</p>
            <div class="row">
                <div>
                    <select name="class" id="select_class" class="form-control">
                        <option value="0">未選択</option>
                        @foreach($classes as $id => $class)
                        <option value="@php echo $id + 1; @endphp" @if($class_id == $id + 1) selected @endif>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if(isset($lectures))
            <div class="row">
                <form action="{{ url('/admin/classes/'.$class_id.'/lecture/registration') }}" method="post">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>科目名称</th>
                                <th>担当教員</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($subjects as $key => $subject)
                            <tr>
                                <td>{{ $subject->subject_name }}</td>
                                <td>
                                    <div>
                                        <select name="{{ $subject->subject_name }}" id="" class="form-control">
                                            <option value="0">未選択</option>
                                            @foreach($teachers as $key => $teacher)
                                                @if($key == $subject->id)
                                                    @foreach($teacher as $v)
                                                        @foreach($lectures as $lecture)
                                                            @if ($lecture->subject_id == $subject->id)
                                                                <option value="{{ $v->id }}" @if($lecture->teacher_id == $v->id) selected @endif>{{ $v->name }}</option>                                       
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        <button type="submit">登録</button>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
            @endif
        </div>
    </body>
</html>