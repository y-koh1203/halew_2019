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
#error {
    color: red;
}

td, th {
    empty-cells: show;
    text-align: center;
}

td{
    height: 110px;
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

.cell-td-flex > select {
    width: 100%;
    height: 50%;
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

.button-wrap{
    text-align: center;
}

#select_class{
    width: 20%;
}

</style>
<div class="container">    
<div class="row">
        <h2 class="heading">時間割設定</h2>     
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
    <p id="error"></p> 
    <div class="row">
        <form action="{{ url('/admin/classes/'.$class_id.'/timetable/registration') }}" method="post" id="tt-form">
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
                                {{ $i }}
                            </div>
                        </th>
                        @for($j = 1; $j <= 7; $j++)
                        <td>
                            <div class="cell-td-flex">
                                <select name="{{$j}}-{{$i}}" id="{{$j}}-{{$i}}" class="form-control select_subject">
                                    <option value="0">授業無し</option>
                                @foreach($lectures as $lecture)
                                    <option value="{{ $lecture->id }}" 
                                        @if($timetable[$i][$j] ==  $lecture->id)
                                            selected
                                        @endif
                                    >{{ $lecture->subject_name }}:{{ $lecture->name }}</option>
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
            <div class="button-wrap">
                <button type="submit" class="btn btn-lg btn-primary" id="btn-submit">登録する</button>
            </div>      
        </Form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let count = 0;
        const ss = document.getElementsByClassName('select_subject');
        const id = {{ $class_id }};

        const elem = document.getElementById('select_class');
        elem.addEventListener('change', () => {
            const id = elem.value;

            if(id == 0){
                return false;
            }

            location.href = `{{ url('/admin/timetable/class/${id}/register') }}`;
        });
 
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
@endsection
