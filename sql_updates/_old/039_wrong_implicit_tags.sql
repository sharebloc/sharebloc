delete ts from tag_selection ts
join tag t on t.tag_id=ts.tag_id and t.tag_type!='vendor'
where entity_type!='vendor';

delete ts from tag_selection ts
where tag_id=0;