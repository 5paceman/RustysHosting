<?php
require_once 'core/init.php';

$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if(Input::exists()) {
        $service = new Service();
            if($service->find(Input::get('service_id'))) {
                if($service->data()->user_id === $user->data()->id)
                {
                    switch(Input::get('command'))
                    {
                        case 'restart':
                            echo 'Server is restarting..';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "systemctl restart {$service->data()->service_id}");
                        break;
                        case 'stop':
                            echo 'Server is stopping..';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "systemctl stop {$service->data()->service_id}");
                        break;
                        case 'start':
                            echo 'Server is starting..';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "systemctl start {$service->data()->service_id}");
                        break;
                        case 'installrustio':
                            echo 'Rust IO is installing. Please allow 5 minutes and then restart your server.';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "InstallRustIO.sh /home/{$service->data()->service_id}/rust");
                        break;
                        case 'update':
                            echo 'Rust is updating. Please allow 10 minutes.';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "UpdateRust.sh {$service->data()->service_id}");
                        break;
                        default:
                            echo 'Unknown command.';
                        break;
                    }
                } else {
                    echo 'Service doesnt exist.';
                }
            } else {
                echo 'Service doesnt exist.';
            }
    }
}