<?php

namespace FRCOVID\Entity;

use FRCOVID\ExternalModule\ExternalModule;
use RCView;
use REDCapEntity\EntityList;
use REDCapEntity\StatusMessageQueue;

class TestSiteList extends EntityList {

    protected function renderPageBody() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['create_all_future_appointment_blocks'])) {
                $this->module->createAllFutureAppointmentBlocks(True);
                StatusMessageQueue::enqueue('Created future appointment blocks.');
            }
        }

        parent::renderPageBody();
    }

    protected function renderAddButton() {

        $btn = RCView::i(['class' => 'fas fa-calendar-alt']);
        $btn = RCView::button([
            'type' => 'submit',
            'name' => 'create_all_future_appointment_blocks',
            'class' => 'btn btn-primary',
        ], $btn . ' Generate future appointments for <b>all</b> sites');

        echo RCView::form(['id' => 'generate_all_appointment_blocks', 'method' => 'post'], $btn);
        parent::renderAddButton();
    }
}
