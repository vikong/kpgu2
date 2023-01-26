-- выбирает состояние указанного предмета
select 
	it.title as Предмет,
    p.title as ИнформацияОПроцессе,
    s.title as СостояниеПредмета,  
    o.title as ТипВладельца,
    e.title as Событие,
    ei.state as СтатусСобытия
from item_types it 
    left join event_items i on it.id = i.item_type_id 
    left join item_states s on i.item_state_id = s.id  
    left join owner_types o on i.owner_type_id = o.id 
    left join events e on i.event_id = e.id 
    left join event_information ei on e.event_information_id = ei.id 
    inner join processes p on e.process_id = p.id 
order by e.created_at 