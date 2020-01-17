<?php
require_once 'core/init.php';

$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if(Input::exists()) {
        $service = new Service();
        $serviceID = Input::get('service_id');
            if($service->find($serviceID)) {
                if($service->data()->user_id === $user->data()->id || $user->isAdmin())
                {
                    if(Input::get('action') === "ping")
                    {
                        $status = Redis::getInstance()->getJson("reporting:servers:status:$serviceID");
                        if($status)
                        {
                            echo ucfirst($status->running)." ($status->rustpid)";
                        } else {
                            echo 'Setting up...';
                        }
                    } else if(Input::get('action') === "logs")
                    {
                        $status = Redis::getInstance()->getJson("reporting:servers:status:$serviceID");
                        if($status)
                        {
                            echo $status->log;
                        }
                    }
                    
                } else {
                    echo 'Service doesnt exist.';
                }
            } else {
                echo 'Service doesnt exist.';
            }
    }
}