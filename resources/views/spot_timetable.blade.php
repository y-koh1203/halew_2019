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
    margin: 1% 0;
}

.spot-list{
    margin-top: 4%;
}

.result-box{
    width: 20%;
    margin-top: 1%;
    margin-bottom: 2%;
    border-radius: 3px;
    border: 2px solid lightgreen;
}

.fa-check-circle{
    background-color: lightgreen;
    padding: 1%;
    /* border-radius: 3px 0 0 3px; */
    border: 2px solid lightgreen;
    color: white;
}

</style>
<div class="container">    
<div class="row">
        <h2 class="heading">スポット時間割設定</h2>     
    </div>
    <div class="row"><p>特定の日時に、カスタムした時間割を設定できます</p></div>
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
    @if(isset($success))
        @if($success)
        <div class="row">
            <div class="result-box">
                <i class="fas fa-check-circle"></i>
                <span>更新が完了しました</span>
            </div>
        </div>
        @endif
    @endif
    <div class="row">
        @if(isset($lectures))
        <form action="{{ url('/admin/timetable/spot/class/'.$class_id.'/registration') }}" method="post">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="cell-th">1</th>
                        <th class="cell-th">2</th>
                        <th class="cell-th">3</th>
                        <th class="cell-th">4</th>
                        <th class="cell-th">5</th>
                        <th class="cell-th">6</th>
                        <th class="cell-th">日付</th>      
                    </tr>
                </thead>
                <tbody>
                    
                    <tr>
                        @for($i = 1;$i < 7;$i++)
                        <td class="cell-td">
                            <div class="cell-td-flex">
                                <select name="spot-{{ $i }}" class="form-control">
                                    <option value="0">未選択</option>
                                    @foreach($lectures as $k => $lecture)
                                    <option value="{{ $lecture->id }}">{{ $lecture->subject_name }}:{{ $lecture->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        @endfor
                        <td class="cell-td">
                            <div class="cell-td-flex">
                                <input class="form-control" name="spot-date" type="date" data-date-format="YYYY-MM-DD" id="example-date-input" required>
                            </div>
                        </td>
                    </tr>   
                </tbody>
            </table>
            {{ csrf_field() }}
            <div class="button-wrap">
                <button type="submit" class="btn btn-lg btn-primary" id="btn-submit">登録する</button>
            </div>      
        </form>
        @endif
    </div>
    @if(isset($registed))
        <div class="row spot-list">
            <h3>登録済みのスポット時間割</h3>   
            <hr> 
        </div>
        @if(count($registed) > 0)
        <div>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="cell-th">1</th>
                        <th class="cell-th">2</th>
                        <th class="cell-th">3</th>
                        <th class="cell-th">4</th>
                        <th class="cell-th">5</th>
                        <th class="cell-th">6</th>
                        <th class="cell-th">日付</th>      
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registed as $datum)
                    <tr>
                        @for($i = 1;$i < 7;$i++)
                        <td class="cell-td">
                            <div class="cell-td-flex">
                                @if($datum->$i == 0)
                                    授業なし
                                @else
                                    @foreach($lectures as $lecture)
                                        @if($lecture->id == $datum->$i)
                                            {{ $lecture->subject_name }}:{{ $lecture->name }}
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        @endfor
                        <td class="cell-td">
                            <div class="cell-td-flex">
                                {{$datum->date}}
                            </div>
                        </td>
                        <td class="cell-td">
                            <div class="cell-td-flex">
                                <form action="{{ url('/admin/timetable/spot/delete') }}" method="POST" id="del-form">
                                    <input type="hidden" name="del-id" value="{{ $datum->id }}">
                                    <input type="hidden" name="class_id" value="{{ $class_id }}">
                                    <button type="submit" class="btn btn-warning">削除</button>
                                </form>
                            </div>
                        </td>
                    </tr> 
                    @endforeach  
                </tbody>
            </table>
        </div>
        @else
        <div>
            <p>現在未登録です</p>
        </div>
        @endif
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let count = 0;
        const ss = document.getElementsByClassName('select_subject');
        @if(isset($class_id))
        const id = {{ $class_id }};
        @endif

        const elem = document.getElementById('select_class');
        elem.addEventListener('change', () => {
            const id = elem.value;

            if(id == 0){
                return false;
            }

            location.href = `{{ url('/admin/timetable/spot/class/${id}/register') }}`;
        });

        const form = document.getElementById('del-form')
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (window.confirm("削除を実行しますか？")) {
                form.submit();
            }else{
                return false;

            }
        })

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
