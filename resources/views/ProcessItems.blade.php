<html>
    <head>
        <title>Предметы Процесса</title>
    <script>
            async function showCoord(event_item_id) {
                let el = document.getElementById("coord");
                let response = await fetch(`http://127.0.0.1:8000/coord?id=${event_item_id}`);
                if (response.ok) { 
                    el.innerHTML = await response.text();
                } else {
                    alert("Ошибка HTTP: " + response.status);
                }
            }            
        </script>

    </head>

<body>
<h2>Предметы Процесса</h2>
 <table>
    <tr>
        <td>
            <table class="note bordered">
                <tr>
                    <td><a href="process?uuid={{$process->process_reference}}">Процесс</a></td>
                    <td><strong>{{$process->title}}</strong></td>
                </tr>
                <tr>
                    <td>Состояние</td>
                    <td class="warn">{{$itemstate}}</td>
                </tr>   
                <tr>
                    <td>Создатель</td>
                    <td>{{$process->creator_presentation}}</td>
                </tr>   
                <tr>
                    <td>Время создания</td>
                    <td>{{$process->creation_time}}</td>
                </tr>   
                <tr>
                    <td>УУИД</td>
                    <td>{{$process->process_reference}}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        @if($coord) <a href="processitems?uuid={{$process->process_reference}}">Без координации</a>
                        @else <a href="processitems?uuid={{$process->process_reference}}&coord">Данные координации</a> @endif
                    </td>
                </tr>   
            </table>
        </td>
    </tr>
 </table> 
 <br>
<table class="table">
<tr>
    <th>Предмет</th>
    <th>Состояние Предмета</th>
    <th>Вид Владельца</th>
    <th>Владелец</th>
    <th>Событие</th>
    <th>Время События</th>
    <th>Отправитель</th>
</tr>
@forelse($data as $d)
    <tr class="main">
        <td><button class="coord" onclick="showCoord('{{$d->item_id}}')">{{$d->item}}</button></td>
        <td>{{$d->i_state}}</td>
        <td>{{$d->o_type}}</td>
        <td>{{$d->owner}} <div class="ref">{{$d->o_reference}}</div></td>
        <td>{{$d->e_title}}<div class="ref">{{$d->event_reference}}</div></td>
        <td>{{$d->event_time}}</td>
        <td>{{$d->sender}}<div class="ref">{{$d->s_reference}}</div></td>
    </tr>
    @isset($d->coord)
        <tr> 
            @forelse($d->coord as $c)
                <tr class="coord">    
                    <td colspan="2"> {{$c->name}} </td>
                    <td colspan="4"> {{$c->value}}{!!($c->json!=null? str_replace(' ', '&nbsp;', str_replace(PHP_EOL,'<br/>',$c->json)) : '')!!} </td>
                </tr>
            @empty
                <td colspan="6"> отсутствует </td>
            @endforelse
        </tr>   
    @endisset
    @empty
       <tr><td>отсутствует</td></tr>  
    @endforelse

</table>
<div id="coord" class="scroll"></div>

 <style type="text/css">
        a{
        color:darkblue;
        text-decoration: none;
    }
    a:hover{
        color: firebrick;
        text-decoration: underline;
    }

.warn{
    color: firebrick;
    font-size: large;
}
div.scroll{
  width:auto;
  height: 340px;
  overflow: scroll;    
}
div.ref{
    color: gray;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-size: x-small;
    margin-top: 3px;
}
button.coord {
    background-color: transparent;
    background-repeat: no-repeat;
    border: none;
    cursor: pointer;
    overflow: hidden;
    outline: none;
}
button.coord:hover{
    color: firebrick;
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
    font-size: small;
	border: 1px solid #ade;
    border-collapse: collapse;
	width: 100%;
	margin-bottom: 20px;
}
.table th {
	font-weight: bold;
	padding: 5px;
	background: #efefef;
	border: 1px solid #ade;
}
.table td{
	padding: 5px 10px;
	border: 1px solid #ade;
	text-align: left;
}
.coord{
	background: #fff;
}
.main {
	background: #ddeeff;
}
</style> 
</html>
