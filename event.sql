select event_type.type_name, event.name, event.start, event.end, event.public, event.academia, event.government, event.industry from event inner join event_type on event_type.type_id = event.event_type_id order by start;


select event_type.type_name, event.name, event.start, event.end, event.public, event.academia, event.government, event.industry from event inner join event_type on event_type.type_id = event.event_type_id and event_type.type_id = 1 order by start;


