<?php
/**
 * @file
 * Provides ExternalModule class for OnCore Client.
 */

namespace FRCOVID\ExternalModule;

use ExternalModules\AbstractExternalModule;

use RCView;
use REDCap;
use REDCapEntity\EntityDB;
use REDCapEntity\EntityFactory;
use REDCapEntity\StatusMessageQueue;

/**
 * ExternalModule class for OnCore Client.
 */
class ExternalModule extends AbstractExternalModule {

    /**
     * @inheritdoc.
     */
    function redcap_every_page_top($project_id) {
    }

    function redcap_save_record($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {
        $appointment_form = $this->getProjectSetting('appointment_form');
        if ( $this->framework->isSurveyPage() && $instrument = $appointment_form) {
            $get_data = [
                'project_id' => $project_id,
                'records' => [$record],
                'events' => [$event_id]
            ];
            $redcap_data = \REDCap::getData($get_data);
            $appointment_id = $redcap_data[$record][$event_id][$appointment_form];
            $factory = new EntityFactory();
            $Appointment = $factory->getInstance('fr_appointment', $appointment_id);
            $Appointment->setData(['record_id' => $record]);
            $Appointment->save();

            //\REDCap::saveData(); // split relevant data out to @SURVEY-HIDDEN fields
        }
    }

    /**
     * @inheritdoc.
     */
    function redcap_module_system_enable($version) {
        EntityDB::buildSchema($this->PREFIX);
    }

    function redcap_module_project_enable($version) {
        // fill in dates for $interval at every $min_interval minutes
    }


    function createAllFutureAppointmentBlocks() {
        $factory = new EntityFactory();
        $query = $factory->query('test_site');
        $test_sites = $query->execute();
        foreach ($test_sites as $test_site) {
            $this->createFutureAppointmentBlocks($test_site);
        }
    }

    function createFutureAppointmentBlocks($test_site) {
        $data = $test_site->getData();
        $project_id = $data['project_id'];
        $minute_interval = $data['site_appointment_duration'];
        $open_time = $data['open_time'];
        $close_time = ($data['close_time'] !== '00:00') ? $data['close_time'] : '23:59';
        $horizon_days = $data['horizon_days'];
        $closed_days = $data['closed_days'];
        $mults_needed = $horizon_days*(60/$minute_interval)*24;
        $site_id = $test_site->getId();
        $closed_days_line = (isset($closed_days)) ? "AND weekday(date) NOT IN (" . $closed_days . ")" : '';

        $sql = "
INSERT INTO redcap_entity_fr_appointment (created, updated, site, appointment_block_date, project_id)
SELECT unix_timestamp(), unix_timestamp(), $site_id, FLOOR(UNIX_TIMESTAMP(date)), $project_id
            FROM (
                SELECT (CURDATE() + 1 + INTERVAL c.number*$minute_interval MINUTE) AS date
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
            WHERE date between now() and now() + INTERVAL $horizon_days DAY " .
            $closed_days_line . "
            AND TIME(date) between TIME('$open_time') and TIME('$close_time') - INTERVAL 1 SECOND
                -- do not create duplicate appointment times at any site
                AND NOT EXISTS (
                    SELECT * FROM redcap_entity_fr_appointment WHERE
                    CONCAT(redcap_entity_fr_appointment.site, redcap_entity_fr_appointment.appointment_block_date)
                        = CONCAT($site_id, FLOOR(UNIX_TIMESTAMP(date)))
                )
            ";

        $result = $this->framework->query($sql);
    }

    /**
     * @inheritdoc.
     */
    function redcap_module_system_disable($version) {
        EntityDB::dropSchema($this->PREFIX);
    }

    function redcap_entity_types() {
        $types = [];

        $types['test_site'] = [
            'label' => 'Test Site',
            'label_plural' => 'Test Sites',
            'icon' => 'home_pencil',
            'special_keys' => [
                'project' => 'project_id',
            ],
            'properties' => [
                'site_long_name' => [
                    'name' => 'Testing Site Full Name',
                    'type' => 'text',
                ],
                'site_short_name' => [
                    'name' => 'Testing Site Abbreviation',
                    'type' => 'text',
                ],
                'site_address' => [
                    'name' => 'Testing Site Address',
                    'type' => 'text',
                ],
                'site_appointment_duration' => [
                    'name' => 'Appointment duration (minutes)',
                    'type' => 'text',
                ],
                'open_time' => [
                    'name' => 'Open time',
                    'type' => 'text',
                ],
                'close_time' => [
                    'name' => 'Close time',
                    'type' => 'text',
                ],
                'closed_days' => [
                    // Single day only supported until Entity gets update for multiselect
                    'name' => 'Day this site is closed',
                    'type' => 'text',
                    'choices' => [
                        '0' => 'Monday',
                        '1' => 'Tuesday',
                        '2' => 'Wednesday',
                        '3' => 'Thursday',
                        '4' => 'Friday',
                        '5' => 'Saturday',
                        '6' => 'Sunday',
                    ]
                ],
                'horizon_days' => [
                    'name' => 'Future days of appointments',
                    'type' => 'text',
                ],
                'project_id' => [
                    'name' => 'Project ID',
                    'type' => 'project',
                    'required' => true,
                ],
            ],
        ];

        $types['fr_appointment'] = [
            'label' => 'Appointment',
            'label_plural' => 'Appointments',
            'icon' => 'clipboard',
            'special_keys' => [
                'project' => 'project_id',
            ],
            'properties' => [
                'appointment_block_date' => [
                    'name' => 'Appointment Block',
                    'type' => 'date',
                ],
                'appointment_block_time' => [
                    'name' => 'Appointment',
                    'type' => 'date',
                ],
                'site' => [
                    'name' => 'Test Site',
                    'type' => 'entity_reference',
                    'entity_type' => 'test_site',
                ],
                'record_id' => [
                    'name' => 'REDCap Record',
                    'type' => 'record',
                ],
                // make unique constraint
                'record_id_and_event' => [
                    'name' => 'REDCap Record + event',
                    'type' => 'text',
                ],
                'project_id' => [
                    'name' => 'Project ID',
                    'type' => 'project',
                    'required' => true,
                ],
            ],
        ];

        /* SQL
           SELECT a.id, CONCAT(b.site_short_name, ' - ', from_unixtime(a.appointment_block_date, '%m/%d/%Y %W %h:%i %p'))
           FROM ((SELECT * FROM redcap_entity_fr_appointment
            WHERE record_id IS NULL
            ORDER BY appointment_block_date) as a
            INNER JOIN redcap_entity_test_site as b
            ON a.site = b.id);
            */

        return $types;
    }

    /**
     * Includes a local JS file.
     *
     * @param string $path
     *   The relative path to the js file.
     */
    protected function includeJs($path) {
        echo '<script src="' . $this->getUrl($path) . '"></script>';
    }

    /**
     * Sets JS settings.
     *
     * @param array $settings
     *   A keyed array containing settings for the current page.
     */
    protected function setJsSettings($settings) {
        echo '<script>onCoreClient = ' . json_encode($settings) . ';</script>';
    }

    function sendEmail($email_info) {
		$to = $email_info['to'];
        $sender = $email_info['sender'];
		$subject = $email_info['subject'];
		$body = $email_info['body'];
        $cc = $email_info['cc'] ? implode(',', $email_info['cc']) : '';

		$success = REDCap::email($to, $sender, $subject, $body, $cc);
		return $success;
    }

}
