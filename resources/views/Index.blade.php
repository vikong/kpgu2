<html>
    <head>
    </head>
 <body>
<h1>КПГУ2.0</h1>
<table class="table">
@forelse($processes as $p)
    <tr>
        <td>{{$p->creator_presentation}}</td>
        <td>{{$p->process_reference}}</td>
        <td>{{$p->process_presentation}}</td>
        <td><a href="process?uuid={{$p->process_reference}}">{{$p->title}}</a></td>
    </tr>
    @empty
       <tr><td>процессов нет</td></tr>  
    @endforelse
	
</table>

<style type="text/css">


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
}
</style> 
</html>
