SELECT a.id, CONCAT(b.site_short_name, ' - ', from_unixtime(a.appointment_block_date, '%m/%d/%Y %W %h:%i %p')) FROM
    (
        (SELECT * FROM redcap_entity_fr_appointment
            WHERE record_id IS NULL
            AND project_id = [project-id]
            ORDER BY appointment_block_date
        ) as a
    INNER JOIN redcap_entity_test_site as b ON a.site = b.id
    )
    ORDER BY a.appointment_block_date
    ;
