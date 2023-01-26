-- получение данных по Предмуту и Типу данных для координации
select
    dt.creator_presentation as ВладелецПредмета,
    ct."name" as ТипДанныхДляКоординации,
    c.value as ЗначениеДанных,
    c."json" as ЗначениеДанныхJson
from item_types it
    left join event_items i on it.id = i.item_type_id
    left join item_states s on i.item_state_id = s.id
    left join events e on i.event_id = e.id
    left join event_information ei on e.event_information_id = ei.id
    left join coordinations c on (i.id = c.event_item_id)
    left join coordination_types ct  on (ct.id = c.coordination_type_id)
    left join document_titles dt on (dt.id  = e.document_title_id)
    inner join processes p on e.process_id = p.id
where it.title = 'Сотрудник'
and
    ct."name" ='ОтветственныеИсполнителя'
and p.process_reference = '4b4841f4-3913-11ed-a261-0242ac120002'
order by e.created_at
