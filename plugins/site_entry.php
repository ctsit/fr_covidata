<?php

require_once dirname(__DIR__) . '/classes/entity/list/TestSiteList.php';

use FRCOVID\Entity\TestSiteList;

$view = new TestSiteList('test_site', $module);
$view->setOperations(['create', 'update', 'delete'])
    // TODO: Create an extended Entity object at classes/entity/TestSite.php
    //->setBulkOperation('create_future_appointments', 'Manually generate new appointments', 'Generated new appointments for each site', 'green')
    ->render('project');
