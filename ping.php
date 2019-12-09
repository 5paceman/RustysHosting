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
                    $connection = @fsockopen($service->ip(), $service->port() + 1, $errno, $errstr, 5);
                    if(is_resource($connection))
                    {
                        echo 'Running';
                        fclose($connection);
                    } else {
                        echo 'Stopped';
                    }
                } else {
                    echo 'Service doesnt exist.';
                }
            } else {
                echo 'Service doesnt exist.';
            }
    }
}