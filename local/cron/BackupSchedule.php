#!/usr/bin/php

<?php
chdir("/var/www/rustyshosting.io/");
require_once 'core/init.php';

define('AKEEBAENGINE', 1);

use Akeeba\Engine\Postproc\Connector\S3v4\Configuration;
use Akeeba\Engine\Postproc\Connector\S3v4\Connector;

$DB = DB::getInstance();

$services = $DB->get('services', array('*'));

function RemoveBackup($space, $file)
{
    $configuration = new Configuration(Config::get('backup/spaces_access'), Config::get('backup/spaces_secret'));
    $configuration->setEndpoint(Config::get('backup/'.$space));
    $connector = new Connector($configuration);
    $connector->deleteObject($space, $file);
}

if($services->count())
{
    foreach($services->results() as $service)
    {
        $size = 0;
        $backups = $DB->get('backups', array('service', '=', $service->id));
        if($backups->count())
        {
            foreach($backups->results() as $backup)
            {
                $size += $backup->size;
                if(strtotime($backup->date) < strtotime('-30 days'))
                {
                    RemoveBackup($backup->space, $backup->path);
                }
            }
        }
        if($size < $service->backup_size && (strtotime($service->expiry) > time()))
        {
            Redis::getInstance()->putJobToMachine($service->machine_id, "BackupInstance.sh {$service->service_id}");
        }
    }
}


?>