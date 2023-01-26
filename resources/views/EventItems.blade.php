<html>
    <head>
        <title>Событие {{$eventInf->title}}</title>
    </head>

<body>
<h2>Событие</h2>
 <table>
    <tr>
        <td>
            <table class="note bordered">
                <tr>
                    <td><a href="process?uuid={{$process->process_reference}}">Процесс</a></td>
                    <td><strong>{{$process->title}}</strong></td>
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
                <tr><td>&nbsp;</td></tr>   
            </table>
        </td>
        <td>
            <table class="note bordered">
                <tr>
                    <td>Событие</td>
                    <td><strong>{{$eventInf->title}}</strong></td>
                </tr>
                <tr>
                    <td>Представление</td>
                    <td>{{$eventInf->event_presentation}}</td>
                </tr>
                <tr>
                    <td>Время События</td>
                    <td>{{$eventInf->event_time}}</td>
                </tr>
                <tr>
                    <td>Ссылка</td>
                    <td>{{$eventInf->event_reference}}</td>
                </tr>   
                <tr>
                    <td colspan="2">@if($coord) <a href="eventitems?event_id={{$event->id}}">Скрыть данные координации</a>
                        @else <a href="eventitems?event_id={{$event->id}}&coord">Показать данные координации</a> @endif
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
</tr>
@forelse($data as $d)
    <tr class="main">
        <td>{{$d->item}}</td>
        <td>{{$d->i_state}}</td>
        <td>{{$d->o_type}}</td>
        <td>{{$d->owner}}</td>
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

 <style type="text/css">
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


.table{
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
