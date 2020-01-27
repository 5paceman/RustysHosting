#!/usr/bin/php

<?php
chdir("/var/www/");
require_once 'core/init.php';

$services = DB::getInstance()->get("services", array('*'))->results();

foreach($services as $service)
{
    $service = new Service(null, $service);
    if(!$service->isValid())
    {
        Redis::getInstance()->putJobToMachine($service->data()->machine_id, "PauseAccount.sh {$service->data()->service_id}", array(
            "locks" => array(
                "service:{$service->id()}"
            )
        ));
    }
    if(strtotime($service->expiry()) < strtotime("-30 day"))
    {
        Redis::getInstance()->putJobToMachine($service->data()->machine_id, "DeleteAccount.sh {$service->data()->service_id} {$service->data()->port}", array(
            "locks" => array(
                "service:{$service->id()}"
            )
        ));
    }
}