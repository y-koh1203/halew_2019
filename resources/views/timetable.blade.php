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

                location.href = `{{ url('/admin/classes/${id}/timetable') }}`;
            });
        });
    </script>
    <style>
        td, th{
            empty-cells: show;
            text-align: center;
        }
    </style>
    <body>
        <div class="container">    
            <div class="row">
                <h1>clsss</h1>
            </div>    
            <div class="row">  
                <select name="class" id="select_class">
                    @if(isset($class_id))
                        <option value="0">未選択</option>
                        @foreach($classes as $id => $class)
                            <option value="@php echo $id + 1; @endphp" @if($class->id == $class_id) selected @endif>{{ $class->class_name }}</option>
                        @endforeach
                    @else
                        <option value="0" selected>未選択</option>
                        @foreach($classes as $id => $class)
                            <option value="@php echo $id + 1; @endphp">{{ $class->class_name }}</option>
                        @endforeach
                    @endif
                </select> 
            </div>  
            @if(count($timetable, COUNT_RECURSIVE) > 6)
            <div class="row">
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
                                @if($timetable[$i][$j] == 0)
                                    <p>授業なし</p>
                                @else
                                    @foreach($lectures as $lecture)
                                        @if($lecture->id == $timetable[$i][$j])
                                        <p>{{ $lecture->subject_name }}</p>
                                        <p>{{ $lecture->name }}</p>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                            @endfor
                        </tr>                       
                    @endfor
                    </tbody>
                </table>
            </div>
            @else
            <p>このクラスの時間割が未登録です。</p>
            <a href="{{ url('/admin/classes/'.$class_id.'/timetable/register') }}">時間割を登録する</a>
            @endif
        </div>
    </body>
</html>
