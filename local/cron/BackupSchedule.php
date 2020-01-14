#!/usr/bin/php

<?php
chdir("/var/www/");
require_once 'core/init.php';

define('AKEEBAENGINE', 1);

use Akeeba\Engine\Postproc\Connector\S3v4\Configuration;
use Akeeba\Engine\Postproc\Connector\S3v4\Connector;

$DB = DB::getInstance();

$services = $DB->get('services', array('id', '=', '*'));

function RemoveBackup($space, $file)
{
    $configuration = new Configuration(Config::get('backup/spaces_access'), Config::get('backup/spaces_secret'));
    $configuration->setEndpoint(Config::get('backup/'.$space));
    $connector = new Connector($configuration);
    $connector->deleteObject($space, $file);
}


foreach($services->results() as $service)
{
    $size = 0;
    $backups = $DB->get('service', '=', $service->id);
    foreach($backups->results() as $backup)
    {
        $size += $backup->size;
        if(strtotime($backup->date) < strtotime('-30 days'))
        {
            RemoveBackup($backup->space, $backup->path);
        }
    }
    
    if($size > $service->backup_size)
    {
        Redis::getInstance()->putJobToMachine($service->id, "BackupInstance.sh {$service->service_id}");
    }
}


?>