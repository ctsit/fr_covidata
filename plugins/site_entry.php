<?php

$view = new \REDCapEntity\EntityList('test_site', $module);
$view->setOperations(['create', 'update', 'delete'])
    ->render('project', 'Title');
