select 
	c.event_item_id as ИдентификаторПредмета,
	t."name",
	c.value,
	c.json 
from coordinations c 
inner join coordination_types t on c.coordination_type_id = t.id 
where c.event_item_id = 1 -- идентификатор предмета items.id