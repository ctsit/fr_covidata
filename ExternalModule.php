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

    /**
     * @inheritdoc.
     */
    function redcap_module_system_enable($version) {
        EntityDB::buildSchema($this->PREFIX);
    }

    function redcap_module_project_enable($version) {
        // fill in dates for $interval at every $min_interval minutes
    }


    /*
    private function create_future_appointments() {
        $factory = new EntityFactory();
        foreach ($test_site) {
            $data = [
            'appointment_block_date' => '',
            'site' => $test_site,
            'record_id' => '',
            'record_id_and_event' => ''
            ];
            $factory->create('fr_appointment', $data);
        }
    }
    */

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
            'icon' => 'house',
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
                'open_time' => [
                    'name' => 'Open time',
                    'type' => 'text',
                ],
                'close_time' => [
                    'name' => 'Close time',
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

        /*
        SELECT id, appointment_block_date FROM redcap_fr_appointment
        // concat site ID, get ABBRV
            WHERE record_id == NULL
            ORDER BY time, site
            AND site == [selected_site];
            // appear as: [site_id, KED - 04/24/2020 15:30]
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
