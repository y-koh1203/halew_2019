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
                            @if($class->id === $class_id)
                            <option value="@php echo $id + 1; @endphp" selected="selected">{{ $class->class_name }}</option>
                            @else
                            <option value="@php echo $id + 1; @endphp">{{ $class->class_name }}</option>
                            @endif
                        @endforeach
                    @else
                        <option value="0" selected>未選択</option>
                        @foreach($classes as $id => $class)
                            <option value="@php echo $id + 1; @endphp">{{ $class->class_name }}</option>
                        @endforeach
                    @endif
                </select> 
            </div>  
            @if(isset($students))
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
                            <tr>
                                <th>1</th>
                                <!-- foreach -->
                                <td>
                                    <p>aaa</p>
                                    <div>
                                        <button type="submit">aaa</button>
                                    </div>
                                </td>
                                <td>b</td>
                                <td>c</td>
                                <td>d</td>
                                <td>e</td>
                            </tr>
                            <tr>
                                <th>2</th>
                                <!-- foreach -->
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </body>
</html>
