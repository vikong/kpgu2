-- состояние предмета нарастающая
-- В каком состоянии находится предмет по указанному процессу
select 
	p.process_reference as ПроцессУУИД, 
	p.creator_presentation as Создатель,
	p.creation_time as ВремяСоздания,
	p.title as ИнформацияОПроцессе,
	ei.state as СтатусСобытия,
	ei.title, 
	o.title as ТипВладельца,
	sender."view" as Отправитель, 
	r."view" as Получатель,
	e.title as Событие,
	i.title as Предмет,
	s.title as СостояниеПредмета,
	i.id as ИдентификаторПредмета
from item_types it 
	left join event_items i on it.id = i.item_type_id
	left join item_states s on i.item_state_id = s.id  
	left join owner_types o on i.owner_type_id = o.id 
	left join events e on i.event_id = e.id 
	left join event_agents ea on (e.id = ea.event_id and ea.title=o.title) 
	left join agents r on (ea.agent_id = r.id)
	left join event_information ei on e.event_information_id = ei.id 
	left join agents sender on (e.sender_id = sender.id)
	inner join processes p on e.process_id = p.id
where 
	p.process_reference = '4b4841f4-3913-11ed-a261-0242ac120002' -- процесс
	and i.title='Сотрудник' -- предмет
order by e.created_at 