<?php

require_once('core/init.php');

$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if(Input::exists()) {
        $service = new Service();
        if($service->find(Input::get('serviceid'))) {
            if($service->data()->user_id === $user->data()->id)
            {
                $urgency = Input::get('urgency');
                $description = Input::get('description');
                $variables = array(
                    'urgency' => $urgency,
                    'description' => $description,
                    'serviceid' => $service->data()->service_id
                );
                Email::getInstance()->sendEmailWithReplyTo($user->data()->email, 'New Support Request -'.$urgency, "support-email", $user->data()->email, $user->data()->firstname, $variables);
                echo 'Submitted.';
            }
        } else {
            echo 'Unknown ServiceID.';
        }
    }
}