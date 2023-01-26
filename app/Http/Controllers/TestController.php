<?php
namespace App\Http\Controllers;
use App\Models\EventInformation;
use DateTime;

define('_BR', '<br>');

use App\Models\DocumentTitle;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\ItemType;
use App\Models\Process;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Service\XmlContainer;

class TestController extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  public function index()
  {
    $processes = Process::all();
    return view('Index', ["processes"=>$processes]);
  }

  /**
   * Ретроспектива для указанного Процесса
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function process()
  {
    $uuid = $_GET["uuid"];

    $process = Process::where('process_reference', $uuid)->first();
    if (!isset($process)) {
      echo "Process: " . $uuid . _BR
        . 'Не найден';
      return;
    }
    // Документ
    $sql = '
    select distinct 
      d.title, d.document_reference
    from events e 
      inner join document_titles d on e.document_title_id = d.id  
    where e.process_id = ?    
    ';

    $doc = DB::select($sql, [$process->id]);
    $process->doc = $doc[0];

    // Предметы процесса
    $sql = '
    select distinct it.title, it.id 
    from item_types it 
    inner join event_items i on it.id = i.item_type_id 
    inner join events e on i.event_id = e.id 
    where e.process_id = ?    
    ';

    $items = DB::select($sql, [$process->id]);
    
    //данные координации
    $sql = '
    select distinct 
    ct.id,
    ct."name" as ctype 
    from coordination_types ct 
      inner join coordinations c on ct.id = c.coordination_type_id 
      inner join event_items i on c.event_item_id = i.id 
      inner join events e on i.event_id = e.id 
      inner join event_agents ea on e.id = ea.event_id 
    where 
      e.process_id = ?
      and ea.title != \'Инициатор\'    
    ';
    
    $coord=DB::select($sql, [$process->id]);
    
    // все события процесса
    $sql = '
select 
  e.id as event_id,
	e.title as e_title,
	ei.state as e_state,
	ei.event_time,
  ei.event_reference,
	sa.title as s_title,
	sender."view" as sender,
  sender.agent_reference as s_reference, 
	ra.title as r_title,
  r."view" as reciever,
  r.agent_reference as r_reference
from processes p
  inner join events e on p.id = e.process_id  
  inner join event_information ei on e.event_information_id = ei.id 
  inner join event_agents sa on (e.id = sa.event_id and sa."type" = 1  )
  inner join agents sender on sa.agent_id = sender.id
  left join event_agents ra on (e.id = ra.event_id and ra."type" = 2)
  left join agents r on (ra.agent_id = r.id)
where p.process_reference = ?
order by e.id, ra.id;';

    $history = DB::select($sql, [$uuid]);

    return view('Process', ["process" => $process, "items" => $items, "coord"=>$coord, "history" => $history]);

  }

  public function eventitems()
  {
    $event_id = $_GET["event_id"];
    $event = Event::where('id', $event_id)->first();
    if (!isset($event)) {
      echo "Событие не найдено";
      return;
    }
    $ei = EventInformation::find($event->event_information_id);

    $process = Process::where('id', $event->process_id)->first();
    if (!isset($process)) {
      echo "Процесс не найден";
      return;
    }

    $sql = '
select 
    i.id as item_id,
    it.title as item,
    s.title as i_state,
    ot.title as o_type,
    coalesce (a."view", ap."view" ) as owner
  from events e 
    inner join event_items i on e.id = i.event_id
    left join item_states s on i.item_state_id = s.id 
    inner join owner_types ot on i.owner_type_id = ot.id 
    inner join item_types it on i.item_type_id = it.id
    left join event_agents ea on (e.id = ea.event_id and i.owner_type_id = ea.owner_type_id)
    left join agents a on ea.agent_id = a.id 
    left join (
      select distinct 
        i.item_type_id, oa.owner_type_id, oa.agent_id, oe.process_id  
      from events oe 
        inner join event_agents oa on oe.id = oa.event_id
        inner join event_items i on oe.id = i.event_id
    ) o on (ea.agent_id is null and e.process_id = o.process_id and i.owner_type_id = o.owner_type_id and i.item_type_id = o.item_type_id)
    left join agents ap on o.agent_id = ap.id
  where
    e.id = ?;
    ';

    $data = DB::select($sql, [$event_id]);

    // данные для координации
    $coord = isset($_GET["coord"]);
    if ($coord) {
      
          $sql = '
        select 
        c.event_item_id,
        t."name" ,
        c.value,
        c."json" 
      from coordinations c 
      inner join coordination_types t on c.coordination_type_id = t.id 
      where c.event_item_id = ?    
      ';

      foreach ($data as $key => $el) {
        $el->coord = DB::select($sql, [$el->item_id]);
      }
    }

    return view("EventItems", [
      "process" => $process, 
      "event" => $event, 
      "eventInf"=>$ei, 
      "coord"=>$coord, 
      "data" => $data
    ]);
  }

  /**
   * Предметы для указанного Процесса
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function processitems()
  {

    $uuid = $_GET["uuid"];
    $date = isset($_GET["date"]) ? DateTime::createFromFormat('Y-m-d', $_GET["date"])->setTime(23,59,59) : null;
  
    $process = Process::where('process_reference', $uuid)->first();
    if (!isset($process)) {
      echo "Процесс: " . $uuid . _BR
        . 'Не найден';
      return;
    }
    
  // состояние предметов
      $sql = '
      select 
      i.id as item_id,
      i.title as item,
      a."view" as owner,
      a.agent_reference as o_reference,
      s.title as i_state,
      o.title as o_type,
      e.title as e_title,
      ei.state as e_state,
      ei.event_time as event_time,
      ei.event_reference,
      sender."view" as sender,
      sender.agent_reference as s_reference 
    from item_types t
    inner join (
        select  
          max(inf.event_time),
          max(inf.created_at),
          max(i.id) as event_item_id,
          i.item_type_id,
          i.owner_type_id,
          ea.agent_id,
          e.process_id
        from events e
          inner join event_information inf on e.event_information_id = inf.id 
          inner join event_items i on e.id = i.event_id 
          inner join event_agents ea on (e.id = ea.event_id and i.owner_type_id=ea.owner_type_id)
          inner join processes p on e.process_id = p.id
        where 
          p.process_reference = ?
          '.(isset($date)? 'and inf.event_time <= ?' : '').'
        group by 
          e.process_id,
          i.item_type_id,
          i.owner_type_id,
          ea.agent_id 
      ) le on t.id = le.item_type_id
      left join event_items i on (le.event_item_id = i.id )
      left join item_states s on i.item_state_id = s.id  
      left join events e on i.event_id = e.id 
      left join agents a on le.agent_id = a.id 
      left join event_agents sa on (e.id = sa.event_id and sa."type"=1) 
      left join agents sender on sa.agent_id = sender.id 
      left join event_information ei on e.event_information_id = ei.id  
      left join owner_types o on le.owner_type_id = o.id 
      order by t.id, t.title, a.id, a."view"     
      ';
  
      if(isset($date))
      {
        $itemstate = 'По состоянию на: '.$date->format('d/m/Y');
        $data = DB::select($sql, [$uuid, $date]);
      }
      else
      {
        $itemstate = 'Актуальное';
        $data = DB::select($sql, [$uuid]);
      }
  
      // данные для координации
      $coord = isset($_GET["coord"]);
    if ($coord) {
      
          $sql = '
        select 
        c.event_item_id,
        t."name" ,
        c.value,
        c."json" 
      from coordinations c 
      inner join coordination_types t on c.coordination_type_id = t.id 
      where c.event_item_id = ?    
      ';

      foreach ($data as $key => $el) {
        $el->coord = DB::select($sql, [$el->item_id]);
      }
    }

      return view('ProcessItems', [
        "process" => $process, 
        "itemstate"=>$itemstate, 
        "data" => $data,
        "coord" => $coord
      ]);
  
  }


/**
 * Состояние Предмета на указанную дату
 * Параметры запроса:
 * uuid - ссылка Процесса (processes.process_reference)
 * id - идентификатор Предмета (item_types.id)
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
 */
public function item()
{
  $uuid = $_GET["uuid"];
  $item_id = $_GET["id"];
  $date = isset($_GET["date"]) ? DateTime::createFromFormat('Y-m-d',$_GET["date"])->setTime(23,59,59) : null;

  $process = Process::where('process_reference', $uuid)->first();
  if (!isset($process)) {
    echo "Процесс: " . $uuid . _BR
      . 'Не найден';
    return;
  }
  $item = ItemType::where('id', $item_id)->first();
  if (!isset($item)) {
    echo "Предмет: " . $item_id . _BR
      . 'Не найден';
    return;
  }
// состояние предмета
    $sql = '
    select 
    i.id as item_id,
    a."view" as owner,
    sender."view" as sender,
    s.title as i_state,
    o.title as o_type,
    e.title as e_title,
    ei.state as e_state,
    ei.event_time as event_time 
  from item_types t
  inner join (
      select  
        max(inf.event_time),
        max(inf.created_at),
        max(i.id) as event_item_id,
        i.item_type_id,
        i.owner_type_id,
        ea.agent_id,
        e.process_id
      from events e
        inner join event_information inf on e.event_information_id = inf.id 
        inner join event_items i on e.id = i.event_id 
        inner join event_agents ea on (e.id = ea.event_id and i.owner_type_id=ea.owner_type_id)
        inner join processes p on e.process_id = p.id
      where 
        p.process_reference = ?
        and i.item_type_id = ?
        '.(isset($date)? 'and inf.event_time <= ?' : '').'
      group by 
        e.process_id,
        i.item_type_id,
        i.owner_type_id,
        ea.agent_id 
    ) le on t.id = le.item_type_id
    left join event_items i on (le.event_item_id = i.id )
    left join item_states s on i.item_state_id = s.id  
    left join events e on i.event_id = e.id 
    left join agents a on le.agent_id = a.id 
    left join event_agents sa on (e.id = sa.event_id and sa."type"=1)  
    left join agents sender on sa.agent_id = sender.id
    left join event_information ei on e.event_information_id = ei.id  
    left join owner_types o on le.owner_type_id = o.id 
    order by t.id, t.title, a.id, a."view",  ei.event_time     
    ';

    if(isset($date))
    {
      $itemstate = 'По состоянию на: '.$date->format('d/m/Y');
      $states = DB::select($sql, [$uuid, $item_id, $date]);
    }
    else
    {
      $itemstate = 'актуальное';
      $states = DB::select($sql, [$uuid, $item_id]);
    }

    // данные для координации
    $sql = '
    select 
    c.event_item_id,
    t."name" ,
    c.value,
    c."json" 
  from coordinations c 
  inner join coordination_types t on c.coordination_type_id = t.id 
  where c.event_item_id = ?    
  ';
    foreach ($states as $key => $el) {
      $el->coord = DB::select($sql, [$el->item_id]);
    }

    
    return view('ItemState', [
      "item"=>$item, 
      "process" => $process, 
      "itemstate"=>$itemstate, 
      "hist"=>false, 
      "data" => $states
    ]);

}
/**
 * Ретроспектива состояний Предмета по Процессу
 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
 */
  public function itemhist()
  {
    $uuid = $_GET["uuid"];
    $item_id = $_GET["id"];

    $process = Process::where('process_reference', $uuid)->first();
    if (!isset($process)) {
      echo "Процесс: " . $uuid . _BR
        . 'Не найден';
      return;
    }
    $item = ItemType::where('id', $item_id)->first();
    if (!isset($item)) {
      echo "Предмет: " . $item_id . _BR
        . 'Не найден';
      return;
    }
    // события Предмета
    $sql = '
    select 
    i.id as item_id,
    i.owner_type_id,
    a.id as owner_id,
    a."view" as owner,
    sender."view" as sender,
    s.title as i_state,
    o.title as o_type,
    e.title as e_title,
    ei.state as e_state,
    ei.event_time as event_time 
  from item_types t
    left join event_items i on t.id = i.item_type_id
    left join item_states s on i.item_state_id = s.id  
    left join owner_types o on i.owner_type_id = o.id 
    left join events e on i.event_id = e.id 
    left join event_agents ea on (e.id = ea.event_id and ea.owner_type_id  = o.id)  
    left join agents a on ea.agent_id = a.id
    left join event_agents sa on (e.id = sa.event_id and sa."type"=1)  
    left join agents sender on sa.agent_id = sender.id
    left join event_information ei on e.event_information_id = ei.id 
    inner join processes p on e.process_id = p.id
    where 
      p.process_reference = ?
      and i.item_type_id = ?
      order by ei.event_time, t.id, t.title, a.id, a."view"
    ';

    $states = DB::select($sql, [$uuid, $item_id]);

    // если в получателях отсутствует тип владельца, то ищем такой тип для всего процесса
    foreach ($states as $key => $s) {
      if (!isset($s->owner_id)) {
        $sql = '
          select distinct ea.agent_id, a."view"  
          from event_agents ea 
          inner join agents a on ea.agent_id = a.id 
          inner join events e on ea.event_id = e.id 
          inner join processes p on e.process_id = p.id 
          where p.process_reference = ?
          and ea.owner_type_id = ?
          ';

        $owners = DB::select($sql, [$uuid, $s->owner_type_id]);
        if (isset($owners[0])) {
          $s->owner = $owners[0]->view;
        }
      }
    }
    return view('ItemState', [
      "item" => $item,
      "process" => $process,
      "itemstate" => "все",
      "hist" => true,
      "data" => $states
    ]);

  }

  /**
   * Данные для координации по Предмету и Процессу
   * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function coord()
  {
    $event_item_id = $_GET["id"];

      $sql = '
      select 
      c.event_item_id,
      t."name" ,
      c.value,
      c."json" 
    from coordinations c 
    inner join coordination_types t on c.coordination_type_id = t.id 
    where c.event_item_id = ?    
    ';
      $data = DB::select($sql, [$event_item_id]);

    return view('coord', ["data" => $data]);

  }
  public function coordate()
  {
    $pid = $_GET["pid"]; //3
    $cid = $_GET["cid"]; //13
    $owner = 'Инициатор';
    $sql = '
    select 
    c.value, 
    c."json",
    a."view" as owner
    from coordination_types ct 
      inner join coordinations c on ct.id = c.coordination_type_id 
      inner join event_items i on c.event_item_id = i.id 
      inner join events e on i.event_id = e.id 
      inner join event_agents ea on (e.id = ea.event_id and i.owner_type_id = ea.owner_type_id)
      inner join agents a on ea.agent_id = a.id 
      inner join processes p on e.process_id = p.id 
    where 
      e.process_id = ?
      and ct.id = ?
    ';

    $data = DB::select($sql,[$pid, $cid]);

    return view('coordate', ["data"=>$data]);

    
  }
}
