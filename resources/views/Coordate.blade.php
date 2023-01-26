<table>
    <tr>
        <th>Владелец</th>
        <th>Значение</th>
</tr>
    @forelse($data as $d)
    <tr>
        <td>{{$d->owner}}</td>
        <td>{{$d->value}}{!!($d->json!=null ? str_replace(' ', '&nbsp;', str_replace(PHP_EOL,'<br/>',$d->json)) : '')!!}</td>
</tr>   
    @empty
       <tr><td>отсутствует</td></tr>  
    @endforelse
 </table>
