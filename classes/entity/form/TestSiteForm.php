<?php

namespace FRCOVID\Entity;

use FRCOVID\ExternalModule\ExternalModule;
use RCView;
use REDCapEntity\EntityForm;
use REDCapEntity\StatusMessageQueue;

class TestSiteForm extends EntityForm {
    protected $module;

    protected function renderPageBody() {

        $module = $this->entity->getModule();

        $this->jsFiles[] = $module->framework->getUrl('js/site_form.js');
        $this->cssFiles[] = $module->framework->getUrl('css/site_form.css');

        parent::renderPageBody();

        echo RCView::button([
                'type' => 'button',
                'name' => 'prettify_json',
                'class' => 'btn btn-primary btn-sm',
                'onclick' => 'prettyPrint()',
                ], 'Pretty format JSON'
        );
    }

    protected function renderAddButton() {

        $btn = RCView::i(['class' => 'fas fa-sync']);
        $btn = RCView::button([
            'type' => 'button',
            'name' => 'prettify_json',
            'class' => 'btn btn-primary btn-sm',
        ], $btn . ' Refresh OnCore Staff List');

        echo RCView::form(['onclick' => 'prettyPrint()'], $btn);
    }

}
