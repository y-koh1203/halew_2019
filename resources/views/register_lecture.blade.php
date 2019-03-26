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
    .select-wrap{
        width: 100%;
    }

    #select_class {
        width: 20%;
    }

    .cell-th{
        width: 150px;
        height: 60px;
        text-align: center;
    }

    .cell-td{
        width: 200px;
        height: 60px;
        text-align: center;
    }

    .cell-flex{
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    .content{
        margin-top: 3%;
    }

    .button-wrap{
        margin-top: 1%;
        text-align: center;
    }

    .result-box{
        width: 20%;
        margin-top: 2%;
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
        <h2 class="heading">教員管理</h2>     
    </div>
    <div class="row"><p>各クラスの授業に、担当教員を設定します</p></div>
    <div class="row">
        <hr>
    </div>
    <div class="row">
        <h3>クラスを選択</h3>    
    </div>
    <div class="row">
        <div class="select-wrap">
            <select name="class" id="select_class" class="form-control">
                <option value="0">未選択</option>
                @foreach($classes as $id => $class)
                <option value="@php echo $id + 1; @endphp" 
                @if(isset($class_id))
                    @if($class_id == $id + 1) 
                        selected 
                    @endif
                @endif>{{ $class->class_name }}</option>
                @endforeach
            </select>
        </div>
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
    @if(isset($lectures))
    <div class="row content">
        <form action="{{ url('/admin/classes/'.$class_id.'/lecture/registration') }}" method="post">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="cell-th">
                            <div class="cell-flex">
                                科目名称
                            </div>
                        </th>
                        <th class="cell-td">
                            <div class="cell-flex">
                                担当教員
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($subjects as $subject)
                    <tr>
                        <td class="cell-th">
                            <div class="cell-flex">
                                {{ $subject->subject_name }}
                            </div>
                        </td>
                        <td class="cell-td">
                            <div class="cell-flex">
                                <select name="{{ $subject->subject_name }}" id="" class="form-control">
                                    <option value="0">未選択</option>
                                    <!-- teachersのkeyがsubject_idになっている -->
                                    @foreach($teachers as $key => $teacher)
                                        <!-- 配列がからでなければ -->
                                        @if($subject->id == $key)
                                            @foreach($teacher as $data)                                            
                                                <option value="{{$data->id}}" 
                                                    @foreach($lectures as $lecture) 
                                                        @if($lecture->teacher_id == $data->id && $lecture->subject_id == $key) 
                                                            selected
                                                        @endif
                                                    @endforeach
                                                >
                                                    {{ $data->name }}
                                                </option>
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
            <div class="button-wrap">
                <button type="submit" class="btn btn-lg btn-primary" id="btn-submit">登録する</button>
            </div>      
            {{ csrf_field() }}
        </form>
    </div>
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

            location.href = `{{ url('/admin/lecture/class/${id}/set') }}`;
        });


        const ss = document.getElementsByClassName('select_subject');
        @if(isset($class_id))
        const class_id = {{ $class_id }}
        @endif
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
@endsection