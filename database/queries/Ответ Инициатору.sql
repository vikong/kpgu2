-- ответы Инициатору 
select 
	p.creator_presentation as Создатель,
	p.process_reference as ПроцессУУИД, 
	p.creation_time as ВремяСоздания,
	p.title as ИнформацияОПроцессе,
	sa.id,
	sa."view" as Отправитель, 
	e.title as Событие,
	ei.state as СтатусСобытия,
	ei.event_time as ВремяСобытия,
	t.title as Предмет,
	s.title as СостояниеПредмета
from event_agents ea 
	inner join events e on ea.event_id = e.id 
	inner join event_items i on e.id = i.event_id 
	inner join item_types t on i.item_type_id = t.id 
	inner join item_states s on i.item_state_id = s.id  
	inner join event_information ei on e.event_information_id = ei.id 
	inner join processes p on e.process_id = p.id 
	inner join agents sa on ea.agent_id = sa.id 
	inner join event_agents r on (e.id = r.event_id and r."type"= 2 ) --получатель 
where 
	ea."type"= 1 --отправитель
	and p.process_reference = 'd53da7a2-6c38-11ed-a1eb-0242ac120002' --'4b4841f4-3913-11ed-a261-0242ac120002'
	and r.agent_id in (
        -- фильтр по Получателю - Инициатор по Процессу 
		select distinct ea.agent_id
		from event_agents ea
			inner join owner_types ot on ea.owner_type_id = ot.id 
			inner join events e on ea.event_id = e.id 
			inner join processes p on e.process_id = p.id 
		where 
			p.process_reference = 'd53da7a2-6c38-11ed-a1eb-0242ac120002' --'4b4841f4-3913-11ed-a261-0242ac120002'
			and ot.title = 'Инициатор'
	)

