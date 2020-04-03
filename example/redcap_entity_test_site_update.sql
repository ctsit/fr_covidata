-- redcap_entity_test_site_update.sql
-- revise the project_id of test_site data

update redcap_entity_test_site
set project_id = 2
where project_id = 1;
