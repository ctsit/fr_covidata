<?php

namespace FRCOVID\Entity;

use FRCOVID\ExternalModule\ExternalModule;
use REDCapEntity\Entity;

class TestSite extends Entity {
    protected $module;

    function __construct($factory, $entity_type, $id = null) {
        parent::__construct($factory, $entity_type, $id);
        $this->module = new \FRCOVID\ExternalModule\ExternalModule();
    }

    function getModule() {
        return $this->module;
    }

    function create_future_appointments() {
        return $this->createFutureAppointmentBlocks();
    }

    function createFutureAppointmentBlocks() {
        $data = $this->getData();
        $project_id = $data['project_id'];
        $minute_interval = $data['site_appointment_duration'];
        $open_time = $data['open_time'];
        $close_time = ($data['close_time'] !== '00:00') ? $data['close_time'] : '23:59';
        $horizon_days = $data['horizon_days'];
        $closed_days = $data['closed_days'];
        $mults_needed = (1 + $horizon_days)*(60/$minute_interval)*24;
        $site_id = $this->getId();
        $closed_days_line = (isset($closed_days)) ? "AND weekday(date) NOT IN (" . $closed_days . ")" : '';
        $start_date = $data['start_date'];

        // If the start_date is in the past or not set, use today's date
        $start_date = (!empty($start_date) && $start_date > time()) ? strftime('%Y-%m-%d', $start_date) : date('Y-m-d');

        if ( $data['use_custom_daily_schedule'] == '1' ) {
            // mapping of weekday name to integer in MySQL spec
            $dow_map = array('Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 2, 'Thursday' => 3, 'Friday' => 4, 'Saturday' => 5, 'Sunday' => 6);
            $custom_daily = json_decode(json_encode($data['custom_daily_schedule']), True); // stored in DB as nested stdClass objects
            $custom_days_sql = " AND (
            ";
            foreach($custom_daily as $day => $hours) {
                $dow = $dow_map[$day];
                $open = $hours['open'];
                $close = $hours['close'];
                // TODO: finish and integrate
                $custom_days_sql .= "(
                            WEEKDAY(date) = $dow
                            AND
                            TIME(date) between TIME('$open') AND TIME('$close') - INTERVAL 1 SECOND
                        )";
                if ($day !== "Sunday") {
                    $custom_days_sql .= " OR ";
                }
            }
            $custom_days_sql .= ")";
            $days_sql = $custom_days_sql;
        } else {
        $days_sql = $closed_days_line . "
            AND TIME(date) between TIME('$open_time') and TIME('$close_time') - INTERVAL 1 SECOND";
        }


        $sql = "
INSERT INTO redcap_entity_fr_appointment (created, updated, site, appointment_block, project_id)
SELECT unix_timestamp(), unix_timestamp(), $site_id, FLOOR(UNIX_TIMESTAMP(date)), $project_id
            FROM (
                SELECT (DATE('$start_date') + INTERVAL c.number*$minute_interval MINUTE) AS date
                    FROM (SELECT singles + tens + hundreds number FROM 
                        ( SELECT 0 singles
                            UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
                            UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
                            UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
                        ) singles JOIN 
                        (SELECT 0 tens
                            UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
                            UNION ALL SELECT  40 UNION ALL SELECT  50 UNION ALL SELECT  60
                            UNION ALL SELECT  70 UNION ALL SELECT  80 UNION ALL SELECT  90
                        ) tens  JOIN 
                        (SELECT 0 hundreds
                            UNION ALL SELECT  100 UNION ALL SELECT  200 UNION ALL SELECT  300
                            UNION ALL SELECT  400 UNION ALL SELECT  500 UNION ALL SELECT  600
                            UNION ALL SELECT  700 UNION ALL SELECT  800 UNION ALL SELECT  900
                        ) hundreds
                    ORDER BY number DESC) c 
                WHERE c.number BETWEEN 0 AND $mults_needed
            ) dates
            WHERE date between DATE('$start_date') and CAST( (DATE('$start_date') + INTERVAL (1 + $horizon_days) DAY) AS DATETIME ) " .
            $days_sql . "
                -- do not create duplicate appointment times at any site
                AND NOT EXISTS (
                    SELECT * FROM redcap_entity_fr_appointment WHERE
                    CONCAT(redcap_entity_fr_appointment.site, redcap_entity_fr_appointment.appointment_block)
                        = CONCAT($site_id, FLOOR(UNIX_TIMESTAMP(date)))
                )
            ";

        $result = $this->module->framework->query($sql);
        return $result;
    }

}
