-- общая картина состояния процесса
-- информация по процессу
select 
	p.creator_presentation as Создатель,
	p.process_reference as ПроцессУУИД, 
	p.creation_time as ВремяСоздания,
	p.title as ИнформацияОПроцессе,
	e.title as Событие,
	ei.state as СтатусСобытия,
	ei.event_time as ВремяСобытия,
	sa."view" as Отправитель, 
	ea.title as ВидПолучателя,
	r."view" as Получатель, 
	io.title as ВидВладельца, 
	it.title as Предмет,
	s.title as СостояниеПредмета
from processes p
	left join events e on p.id = e.process_id  
	left join event_information ei on e.event_information_id = ei.id 
	left join event_agents sender on (e.id = sender.event_id and sender."type"=1  )
	left join agents sa on sender.agent_id = sa.id
	left join event_items i on e.id = i.event_id
	left join owner_types io on i.owner_type_id = io.id 
	left join item_types it on i.item_type_id = it.id 
	left join item_states s on i.item_state_id = s.id  
	left join event_agents ea on (e.id = ea.event_id )
	left join agents r on (ea.agent_id = r.id and ea."type"=2)
where p.process_reference = '4b4841f4-3913-11ed-a261-0242ac120002'
order by e.created_at;

