SELECT a.id, CONCAT('Tent ', b.site_short_name, ' - ', from_unixtime(a.appointment_block, '%m/%d/%Y %W %h:%i %p')) FROM
    (
        (SELECT * FROM redcap_entity_fr_appointment
            WHERE ( record_id IS NULL OR
                (record_id = [record-name])
                )
            AND project_id = [project-id]
            AND (
            ( appointment_block > UNIX_TIMESTAMP( DATE( NOW() + INTERVAL IF(HOUR(NOW()) >= 15, 2, 1) DAY ) ) )
            OR (
                IF([is-survey], 0, 1)
                AND
                record_id = [record-name]
               )
            )
            ORDER BY appointment_block
        ) as a
    INNER JOIN redcap_entity_test_site as b ON a.site = b.id
    )
    ORDER BY a.appointment_block
    ;