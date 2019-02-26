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
    <style>
        td, th{
            empty-cells: show;
            text-align: center;
        }

        td{
            height: 100px;
        }

        .cell-td-flex{
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
        }

        .cell-td-flex > p{
            margin: 0;
            padding: 0;
        }

        /* under */

        .cell-th{
            width: 300px;
        }

        .cell-none{
            width: 60px;
        }

        .cell-th-row{
            height: 100px;
        }

        .cell-th-flex{
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-around;
        }

        .cell-th-flex > p{
            margin: 0;
            padding: 0;
        }
    </style>
    <body>        
        <div class="container">
            <div class="container">
                <div class="row">
                    <h1>今週の時間割</h1>
                </div>
                <div class="row">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <th class="cell-none"></th>
                            <th class="cell-th">日曜日</th>
                            <th class="cell-th">月曜日</th>
                            <th class="cell-th">火曜日</th>
                            <th class="cell-th">水曜日</th>
                            <th class="cell-th">木曜日</th>
                            <th class="cell-th">金曜日</th>
                            <th class="cell-th">土曜日</th>
                        </thead>
                        <tbody>
                        @for($i = 1; $i <= 6; $i++)
                            <tr>
                                <th class="cell-th-row">
                                    <div class="cell-th-flex">
                                        <p>{{ $i }}</p>
                                    </div>
                                </th>
                                @for($j = 1; $j <= 7; $j++)   
                                <td>
                                    <div class="cell-td-flex">
                                        @if($timetable[$i][$j] == 0)
                                            <p>授業なし</p>
                                        @else
                                            @foreach($lectures as $lecture)
                                                @if($lecture->teacher_id == 0)
                                                    @continue
                                                @else
                                                    @if($lecture->id == $timetable[$i][$j])
                                                    <p class="aaa">{{ $lecture->subject_name }}</p>
                                                    <p>{{ $lecture->class_name }}</p>
                                                    @endif
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                                @endfor
                            </tr>                       
                        @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>
