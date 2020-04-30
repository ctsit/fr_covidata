-- for FR-COVID-19
-- using individual events, accounting for Sunday being closed, not allowing appointments for Monday after 3pm Saturday
SELECT a.id, CONCAT(b.site_short_name, ' - ', from_unixtime(a.appointment_block, '%m/%d/%Y %W %h:%i %p')) FROM
    (
        (SELECT * FROM redcap_entity_fr_appointment
            WHERE ( record_id IS NULL OR
                (record_id = [record-name])
                )
            AND project_id = [project-id]
            AND (
            ( appointment_block > UNIX_TIMESTAMP(
                     -- If it is later than 3pm, only show appointments at least 2 days from today
                    DATE( NOW() + INTERVAL IF(HOUR(NOW()) >= 15 AND WEEKDAY(NOW()) != 6, 2, 1) DAY ) +
                    -- if it's Saturday after 3, add an additional day
                    INTERVAL IF(WEEKDAY(NOW()) = 5 AND HOUR(NOW()) >= 15, 1, 0) DAY +
                    -- or if it's Sunday
                    INTERVAL IF(WEEKDAY(NOW()) = 6, 1, 0) DAY
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
