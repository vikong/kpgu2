<html>
    <head>
    <title>Процесс {{$process->title}}</title>
        <script>
            async function showCoord(process_id) {
                let ctype_id = document.getElementById("ctype").value;
                let el = document.getElementById("coord");
                let response = await fetch(`http://127.0.0.1:8000/coordate?pid=${process_id}&cid=${ctype_id}`);
                if (response.ok) { 
                    el.innerHTML = await response.text();
                } else {
                    alert("Ошибка HTTP: " + response.status);
                }
            }            
        </script>
    </head>
 <body>
 <h2>События Процесса</h2>
 <table>
    <tr>
        <td>
        <table class="note bordered">
    <tr>
        <td>Документ</td>
        <td><strong>{{$process->doc->title}}</strong><div class="ref">{{$process->doc->document_reference}}</div></td>
    </tr>   
    <tr>
        <td>Процесс</td>
        <td><strong>{{$process->title}}</strong><div class="ref">{{$process->process_reference}}</div></td>
    </tr>   
    <tr>
        <td>Создатель</td>
        <td>{{$process->creator_presentation}}</td>
    </tr>   
    <tr>
        <td>Время создания</td>
        <td>{{$process->creation_time}}</td>
    </tr>   
 </table> 
    <td>
    <table class="note bordered">
        <tr><td colspan="2"><strong><a href="processitems?uuid={{$process->process_reference}}">Предметы процесса</a></strong></td></tr>
        @forelse($items as $i)
        <tr>
            <td><a href="item?uuid={{$process->process_reference}}&id={{$i->id}}">{{$i->title}}</a> </td>
        </tr>   
        @empty
        <tr><td>нет предметов</td></tr>  
        @endforelse
        </tr>
    </table>
    </td>
    </tr>
 </table>
 
 @php($event_id = 0)
 @php($counter = 1)
 @php($g_counter = 0)
 @php($class = 'highlight-blue')

 <table class="table">
    <tr>
        <th>Событие</th>
        <th>Статус События</th>
        <th>Время События</th>
        <th>Отправитель</th>
        <th>Получатель</th>
</tr>
    @forelse($history as $p)
    @if($event_id != $p->event_id)
        @php($counter++)
        @php($event_id = $p->event_id)
        @php($g_counter=1)
    @else
        @php($g_counter++)
    @endif
    @if($counter%2 == 1)
        @php($class = 'highlight-red')
    @else
        @php($class = 'highlight-green')
    @endif
    <tr> 
        @if($g_counter==1)
            <td class="{{$class}}"><a href="eventitems?event_id={{$p->event_id}}">{{$p->e_title}}</a><div class="ref" >{{$p->event_reference}}</div></td>
            <td class="{{$class}}">{{$p->e_state}}</td>
            <td class="{{$class}}">{{$p->event_time}}</td>
            <td class="{{$class}}">
                {{$p->s_title}}<br>
                {{$p->sender}}
                <div class="ref" >{{$p->s_reference}}</div></td>
        @else
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        @endif
        <td class="{{$class}}">
            {{$p->r_title}}<br>
            {{$p->reciever}}
            <div class="ref" >{{$p->r_reference}}</div>
        </td>
    </tr>   
    @empty
       <tr><td>история отсутствует</td></tr>  
    @endforelse
 </table>
 <div>
        <select id="ctype">
    @forelse($coord as $c)
    <option value="{{$c->id}}">{{$c->ctype}}</option>
    @empty
    @endforelse
    </select>
    <button onclick="showCoord('{{$process->id}}')">Показать</button>
</div>
<div id="coord" class="scroll"></div>
</body>

<style type="text/css">
    a{
        color:darkblue;
        text-decoration: none;
    }
    a:hover{
        color: firebrick;
        text-decoration: underline;
    }

div.scroll{
  width:auto;
  height: 200px;
  overflow: scroll;    
}

div.ref{
    color: gray;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-size:x-small;
}

.info{
        border: 1px solid #fff;
        border-collapse: collapse;
    }

.note{
    padding: 10px;
    background-color: lightgoldenrodyellow;
}
.bordered{
    border: 1px solid burlywood;
}

.table{
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-size:x-small;
	border: 1px solid darkgray;
    border-collapse: collapse;
	width: 100%;
	margin-bottom: 20px;
}
.table th {
	font-weight: bold;
	padding: 5px;
	background: #efefef;
	border: 1px solid darkgray;
}
.table td{
	padding: 5px 10px;
	border: 1px solid darkgray;
	text-align: left;
}
.main {
	background: #fff;
}
.highlight-blue{
	background: #ddeeff;
}
.highlight-green{
	background: #daffdd;
}
.highlight-red{
	background: #ffdddd ;
}
</style> 
</html>
