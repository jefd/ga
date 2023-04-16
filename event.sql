select event_type.name, event.name, event.start, event.end, event.public, event.academia, event.government, event.industry from event inner join event_type on event_type.id = event.event_type_id order by start;





