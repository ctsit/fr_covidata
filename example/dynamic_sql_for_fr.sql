-- REMOVE ALL COMMENTS BEFORE PASTING TO REDCAP

-- for FR-COVID-19
-- using individual events, for the cron job generating labels every day at 7pm.
SELECT a.id, CONCAT(b.site_short_name, ' - ', from_unixtime(a.appointment_block, '%m/%d/%Y %W %h:%i %p')) FROM
    (
        (SELECT * FROM redcap_entity_fr_appointment
            WHERE ( record_id IS NULL OR
                (record_id = [record-name])
                )
            AND project_id = [project-id]
            AND (
            ( appointment_block > UNIX_TIMESTAMP(
                     -- If it is later than 7pm, only show appointments at least 2 days from today
                    DATE( NOW() + INTERVAL IF(HOUR(NOW()) >= 19, 2, 1) DAY )
                )
            )
            -- unless it's an entry for the same visit for this person, and it's not a survey
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
