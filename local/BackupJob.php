#!/usr/bin/php

<?php
chdir("/var/www/");

require_once 'core/init.php';

$instanceID = $argv[1];
$filename = $argv[2];
$space = $argv[3];
$size = $argv[4];

$service = new Service($instanceID, null);

$DB = DB::getInstance();

$DB->insert('backups', array(
    'date' => date('Y-m-d H:i:s'),
    'path' => $filename,
    'service' => $service->id(),
    'space' => $space,
    'size' => $size
));


?>