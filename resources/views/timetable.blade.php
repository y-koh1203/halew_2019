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

    #select_class{
        width: 20%;
    }

    .table-wrap{
        margin-top: 3%;
    }

    .not-exsist{
        margin: 1% 0;
        /* text-align: center; */
        width: 100%;
    }

    a:link {
        color: white;
    }

    .button-wrap{
        width: 28%;
        margin: 1% 0;
        text-align: center;
    }

    .custom-btn {
        margin-top: 2%;
        margin-bottom: 2%;
        padding-left: 0;
    }
</style>
<div class="container">    
    <div class="row">
        <h2 class="heading">時間割管理</h2>     
    </div>
    <div class="row"><p>各クラスの時間割の設定・管理を行えます</p></div>
    <div class="row">
        <hr>
    </div>
    <div class="row">
        <h3>クラスを選択</h3>    
    </div>
    <div class="row">
        <select name="class" id="select_class" class="custom-select">
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

    @if(isset($timetable))
    @if(count($timetable, COUNT_RECURSIVE) > 6)
        @if(isset($class_id))
            <div class="row custom-btn">
                <a href="{{ url('/admin/timetable/class/'.$class_id.'/register') }}" class="btn btn-primary btn-md" role="button" aria-disabled="true">時間割を編集する</a>
            </div>
            <!-- <div class="row custom-btn">
            </div> -->
        @endif
    <div class="row table-wrap">
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
                            {{ $i }}
                        </div>
                    </th>
                    @for($j = 1; $j <= 7; $j++)
                    <td>
                        <div class="cell-td-flex">
                        @if($additional[$i][$j] != null)
                            @if($additional[$i][$j] == 0)
                                <p>授業なし</p>
                            @else
                                @foreach($lectures as $lecture)
                                    @if($lecture->teacher_id == 0)
                                        <p>授業なし</p>
                                        @continue
                                    @else
                                        @if($lecture->id == $additional[$i][$j])
                                        <p>{{ $lecture->subject_name }}</p>
                                        <p>{{ $lecture->name }}</p>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @else
                            @if($timetable[$i][$j] == 0)
                                <p>授業なし</p>
                            @else
                                @foreach($lectures as $lecture)
                                    @if($lecture->teacher_id == 0)
                                        <p>授業なし</p>
                                        @continue
                                    @else
                                        @if($lecture->id == $timetable[$i][$j])
                                        <p>{{ $lecture->subject_name }}</p>
                                        <p>{{ $lecture->name }}</p>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endif
                        </div>
                    </td>
                    @endfor
                </tr>                       
            @endfor
            </tbody>
        </table>
    </div>
    @else
    <div class="not-exsist">
        <div class="error-box">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="error-text">選択されたクラスの時間割が未設定です</span>
        </div>
        <div class="button-wrap">
            <a href="{{ url('/admin/timetable/class/'.$class_id.'/register') }}" class="btn btn-primary btn-md" role="button" aria-disabled="true">時間割を登録する</a>
        </div>
    </div>
    @endif
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const elem = document.getElementById('select_class');
        elem.addEventListener('change', () => {
            const id = elem.value;

            if(id == 0){
                return false;
            }

            location.href = `{{ url('/admin/timetable/class/${id}') }}`;
        });
    });
</script>
@endsection
