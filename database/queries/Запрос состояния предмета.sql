--Запрос состояния предмета
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
	c.cdata as ДанныеДляКоординации,
	c.cjson as ДанныеДляКоординации_Json,
	s.title as СостояниеПредмета
from processes p
	left join events e on p.id = e.process_id
	left join event_information ei on e.event_information_id = ei.id
	left join event_agents sender on (e.id = sender.event_id and sender."type"=1  )
	left join agents sa on sender.agent_id = sa.id
	left join event_items i on e.id = i.event_id
	left join (
		select event_item_id,
			cast(string_agg(concat_ws('=',name, value), ';') as text) as cdata,
			string_agg(cast(json as text), ';') as cjson
		from coordinations group by 1) c on c.event_item_id = i.id
	left join owner_types io on i.owner_type_id = io.id
	left join item_types it on i.item_type_id = it.id
	left join item_states s on i.item_state_id = s.id
	left join event_agents ea on (e.id = ea.event_id )
	left join agents r on (ea.agent_id = r.id and ea."type"=2)
where p.process_reference = 'd53da7a2-6c38-11ed-a1eb-0242ac120002'
  and e.event_information_id in (
    select
        max(ei3.id)
    from
        event_information ei3
    where
            ei3.event_time in (
            select
                max(ei2.event_time)
            from
                event_information ei2
                inner join events e
                	on e.event_information_id = ei2.id
                inner join processes p
                	on e.process_id = p.id
            where p.process_reference = 'd53da7a2-6c38-11ed-a1eb-0242ac120002'
              and ei2.event_time <= '2022-11-16 23:59:59'
                  -- ВремяСобытия '2022-11-16 23:59:59'
                  -- ВремяСобытия '2022-11-18 23:59:59'
				  -- ВремяСобытия '2022-11-20 23:59:59'
				  -- ВремяСобытия '2022-12-07 23:59:59'
        )
)
order by it.title, io.title;
