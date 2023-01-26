<html>
<head>
    <title>{{$item->title}}</title>
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
 <h2>{{$item->title}}</h2>
 
 <table class="note bordered">
    <tr>
        <td>Состояние</td>
        <td><span class="warn">
        @if($hist)
        <a href="item?uuid={{$process->process_reference}}&id={{$item->id}}">{{$itemstate}}</a>    
        @else
        <a href="itemhist?uuid={{$process->process_reference}}&id={{$item->id}}">{{$itemstate}}</a>
        @endif
        </span></td>
    </tr>
    <tr><td colspan="2">Процесс</td></tr>
    <tr>
        <td>УУИД</td>
        <td>{{$process->process_reference}}</td>
    </tr>   
    <tr>
        <td>Название</td>
        <td>{{$process->title}}</td>
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
 <br>
<table class="table">
<tr>
    <th>Событие</th>
    <th>Статус События</th>
    <th>Отправитель</th>
    <th>Время События</th>
    <th>Вид Владельца</th>
    <th>Владелец</th>
    <th>Состояние Предмета</th>
</tr>
@forelse($data as $d)
    <tr @if(!$hist) class="main" @endif>
        <td>{{$d->e_title}}</td>
        <td>{{$d->e_state}}</td>
        <td>{{$d->sender}}</td>
        <td>{{$d->event_time}}</td>
        <td>{{$d->o_type}}</td>
        <td>{{$d->owner}}</td>
        <td><button onclick="showCoord('{{$d->item_id}}')" class="coord">{{$d->i_state}}</button></td>
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
       <tr><td>история отсутствует</td></tr>  
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
.note{
    padding: 10px;
    background-color: lightgoldenrodyellow;
}
.bordered{
    border: 1px solid burlywood;
}

div.scroll{
  width:auto;
  height: 350px;
  overflow: scroll;    
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
