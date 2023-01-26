select 
	t.title as Предмет,
	s.title as СостояниеПредмета,
	o.title as ВидВладельца,
	a."view" as Владелец,
	e.title as Событие,
	ei.state as СтатусСобытия,
	ei.event_time as ВремяСобытия 
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
				p.process_reference = '4b4841f4-3913-11ed-a261-0242ac120002'
				-- актуальное на дату
				-- and inf.event_time <= ?
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
	left join event_information ei on e.event_information_id = ei.id
	left join owner_types o on le.owner_type_id = o.id 
where 
	i.title='Поручение' -- Предмет
order by t.id, t.title, a.id, a."view",  ei.event_time 
	
