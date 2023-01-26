-- последние события по процессу
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
	p.process_reference = '4b4841f4-3913-11ed-a261-0242ac120002' -- процесс
	and inf.event_time <= '2021-12-13 00:00:00' -- время отсечения
group by 
	e.process_id,
	i.item_type_id,
	i.owner_type_id,
	ea.agent_id 
