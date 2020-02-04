<?php

$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if($user->data()->free_trial_offer)
    {
        echo "You've already used your free trial offer.";
    } else if(Input::get('region')){
        $service = new Service();
        $service->create(1 /* Basic Plan */, $user->data()->id, "", 1, Input::get('region'));
        DB::getInstance()->update('users', $user->data()->id, array(
            'free_trial_offer' => 1
        ));
    }
}