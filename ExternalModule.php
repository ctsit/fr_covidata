<?php

namespace FRCOVID\ExternalModule;

use ExternalModules\AbstractExternalModule;

use RCView;
use REDCap;
use Records;
use REDCapEntity\EntityDB;
use REDCapEntity\EntityFactory;
use REDCapEntity\StatusMessageQueue;

class ExternalModule extends AbstractExternalModule {

    function redcap_survey_page_top($project_id, $record = NULL, $instrument, $event_id, $group_id = NULL, $survey_hash, $response_id = NULL, $repeat_instance = 1 ) {
        $appointment_form = $this->framework->getProjectSetting('appointment_form');
        if ($instrument == $appointment_form) {
            $this->setJsSettings(['data_endpoint' => $this->framework->getProjectSetting('data_endpoint')]);
            $this->includeJs('js/populate_appointment_table.js');
            $this->includeCss('css/appointment_table.css');
        }
    }

    function redcap_save_record($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {
        $appointment_form = $this->framework->getProjectSetting('appointment_form');
        if ($instrument == $appointment_form) {
            $repeat_implement = $this->framework->getProjectSetting('repeat_implement');
            $using_repeat_instances = 0;
            if ($repeat_implement == 'instances') {
                $encounter = $repeat_instance;
            } else {
                // custom fit to UF FRCovid project
                // looks at custom event label and parses out encounter
                $sql = "SELECT custom_event_label FROM redcap_events_metadata WHERE
                    event_id = $event_id;";
                $result = $this->framework->query($sql);
                $result = $result->fetch_all();
                $encounter = substr($result[0][0], strlen('test-'));
            }
            $this->splitAppointmentData($project_id, $record, $event_id, $appointment_form, $encounter, $using_repeat_instances);
        }
    }

    function splitAppointmentData($project_id, $record, $event_id, $appointment_form, $encounter, $using_repeat_instances = 1) {
        $get_data = [
            'project_id' => $project_id,
            'records' => [$record],
            'events' => [$event_id]
            ];

        $redcap_data = \REDCap::getData($get_data);

        // Get appointment block id
        if ($using_repeat_instances) {
            // repeating
            $repeat_instance = $encounter;
            $appointment_id = $redcap_data[$record]['repeat_instances'][$event_id][$appointment_form][$repeat_instance]['appointment'];
        } else {
            // non repeating
            $appointment_id = $redcap_data[$record][$event_id][$appointment_form];
        }

        $factory = new EntityFactory();

        //check for an existing appointment for this person and visit number
        $results = $factory->query('fr_appointment')
            ->condition('project_id', $project_id)
            ->condition('record_id', $record)
            ->condition('encounter', $encounter)
            ->execute();

        if (!empty($results)) {
            $old_appointment_id = key($results);
            $this->cancelAppointment($factory, $old_appointment_id);
        }

        $schedule_info = $this->scheduleAppointment($factory, $project_id, $record, $appointment_id, $encounter);
        $appointment_data = $schedule_info[0];
        $Site = $schedule_info[1];

        $test_date_and_time = date('Y-m-d H:i', $appointment_data['appointment_block']);

        $save_data = [
            'research_encounter_id' => $this->encodeUnique($record, $encounter),
            'site_short_name' => $Site['site_short_name'],
            'site_long_name' => $Site['site_long_name'],
            'site_address' => $Site['site_address'],
            'test_date_and_time' => $test_date_and_time,
            'test_type' => $Site['testing_type']
        ];

        if ($using_repeat_instances) {
            $redcap_data[$record]['repeat_instances'][$event_id][$appointment_form][$repeat_instance] = $save_data;
        } else {
            $redcap_data[$record][$event_id] = $save_data;
        }

        // split relevant data out to @SURVEY-HIDDEN fields
        \REDCap::saveData($project_id, 'array', $redcap_data);
    }

    function cancelAppointment($factory, $old_appointment_id) {
            $OldAppointment = $factory->getInstance('fr_appointment', $old_appointment_id);
            $OldAppointment->setData([
                    'record_id' => NULL,
                    'encounter' => NULL,
            ]);
            $OldAppointment->save();
    }

    function scheduleAppointment($factory, $project_id, $record_id, $appointment_id, $encounter = 1) {
        $Appointment = $factory->getInstance('fr_appointment', $appointment_id);
        $Appointment_data = $Appointment->getData();
        $Appointment->setData([
                'record_id' => $record_id,
                'encounter' => (string) $encounter,
        ]);
        $Appointment->save();
        $Site = $factory->getInstance('test_site', $Appointment_data['site'])
            ->getData();
        return [$Appointment_data, $Site];
    }

    function encodeUnique($record_id, $instance_id, $record_pad = 5, $instance_pad = 2) {
        /* converts record id and instance id into hex,
         * creates a checksum
         * concats all
         * 11 characters total
         * <location_id>-<record>-<instance>-checksum
         * 3 characters for human readability ('---')
         * 1 character for location ID
         * 5 characters for record; 16^5 = > 1 million records
         * 2 characters for instance; 16^2 = 256 visits per person
         * 1 character for checksum
         */
        $location_encode = dechex($this->getProjectSetting('location_id'));
        $record_encode = str_pad(dechex($record_id), $record_pad, '0', STR_PAD_LEFT);
        $visit_encode = str_pad(dechex($instance_id), $instance_pad, '0', STR_PAD_LEFT);
        $check_digit = $this->generateLuhnChecksum($record_encode . $visit_encode);
        return strtoupper($location_encode . '-' . $record_encode . '-' . $visit_encode . '-' . $check_digit);
    }

    function generateLuhnChecksum($input) {
        // https://en.wikipedia.org/wiki/Luhn_mod_N_algorithm
        $sum = 0;
        $factor = 2;

        settype($input, 'string');
        for ($i = strlen($input) - 1; $i >= 0; $i--) {
            $addend = hexdec($input[$i]) * $factor;
            $addend = floor($addend / 16) + ($addend % 16); // sum of individual digits expressed in base16

            $sum += $addend;
            $factor = ($factor == 2) ? 1 : 2;
        }

        $remainder = $sum % 16;
        return dechex( (16 - $remainder) % 16 );
    }

    function redcap_module_system_enable($version) {
        EntityDB::buildSchema($this->PREFIX);
    }

    function redcap_module_project_enable($version) {
        // fill in dates for $interval at every $min_interval minutes
    }

    function createAllFutureAppointmentBlocks($override = False) {
        if ($override !== "run anyway") { // cron will pass an array by default
            return;
        }
        $factory = new EntityFactory();
        $query = $factory->query('test_site');
        $test_sites = $query->execute();
        foreach ($test_sites as $test_site) {
            $test_site->createFutureAppointmentBlocks();
        }
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
            'class' => [
                'path' => 'classes/entity/TestSite.php',
                'name' => 'FRCOVID\Entity\TestSite'
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
                'start_date' => [
                    'name' => 'First date of available appointments',
                    'type' => 'date',
                ],
                'horizon_days' => [
                    'name' => 'Future days of appointments',
                    'type' => 'text',
                ],
                'testing_type' => [
                    'name' => 'Type of testing done at this site',
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
                'appointment_block' => [
                    'name' => 'Appointment Block',
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
                'encounter' => [
                    'name' => 'Encounter number',
                    'type' => 'text',
                ],
                'project_id' => [
                    'name' => 'Project ID',
                    'type' => 'project',
                    'required' => true,
                ],
            ],
        ];

        return $types;
    }

    protected function includeCss($path) {
        echo '<link rel="stylesheet" href="' . $this->getUrl($path) . '">';
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
        echo '<script>FRCOVID = ' . json_encode($settings) . ';</script>';
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
