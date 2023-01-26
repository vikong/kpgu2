-- владелец предмета для события
select distinct
	i.item_type_id, coalesce(o.agent_id, ea.agent_id) as owner_id
from events e 
	inner join event_information ei on e.event_information_id = ei.id
	inner join event_items i on e.id = i.event_id
	left join event_agents ea on (e.id = ea.event_id and i.owner_type_id = ea.owner_type_id)
	left join (
		select distinct oa.owner_type_id, oa.agent_id, oe.process_id  
		from events oe 
			inner join event_agents oa on oe.id = oa.event_id
	) o on (ea.agent_id is null and e.process_id = o.process_id and i.owner_type_id = o.owner_type_id)
where
	ei.event_reference = '19a68744-48c9-11ed-b878-0242ac120002';


-- владелец предмета для процесса
select distinct ea.owner_type_id, ea.agent_id, e.process_id  
from events e 
	inner join event_agents ea on e.id = ea.event_id
	inner join processes p on e.process_id = p.id 
where
	p.process_reference = '4b4841f4-3913-11ed-a261-0242ac120002'
	
