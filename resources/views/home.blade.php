@extends('base')

@section('title')
    <title>教員HOME</title>
@endsection

@section('teacher_name')
    @if(isset($tn))
        {{ $tn }}
    @endif
@endsection

@section('body')
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

    hr {
        width: 100%;
        height: 2px;
        margin: 1% 0 2% 0;
    }

    .heading{
        margin-top: 3%;
    }
</style>     
<div class="container">
    <div class="container">
        <div class="row">
            <h2 class="heading">今週の時間割</h2> 
        </div>
        <div class="row"><p>{{ $tn }}先生の時間割です</p></div>
        <div class="row">
            <hr>
        </div>
        <div class="row">
            <table class="table table-striped table-bordered">
                <thead>
                    <th class="cell-none"></th>
                    <th class="cell-th">@if(isset($date)) {{ $date[0]['month'] }}/{{ $date[0]['day'] }}(日) @endif</th>
                    <th class="cell-th">@if(isset($date)) {{ $date[1]['month'] }}/{{ $date[1]['day'] }}(月) @endif</th>
                    <th class="cell-th">@if(isset($date)) {{ $date[2]['month'] }}/{{ $date[2]['day'] }}(火) @endif</th>
                    <th class="cell-th">@if(isset($date)) {{ $date[3]['month'] }}/{{ $date[3]['day'] }}(水) @endif</th>
                    <th class="cell-th">@if(isset($date)) {{ $date[4]['month'] }}/{{ $date[4]['day'] }}(木) @endif</th>
                    <th class="cell-th">@if(isset($date)) {{ $date[5]['month'] }}/{{ $date[5]['day'] }}(金) @endif</th>
                    <th class="cell-th">@if(isset($date)) {{ $date[6]['month'] }}/{{ $date[6]['day'] }}(土) @endif</th>
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
@endsection
