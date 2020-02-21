<?php
require_once 'core/init.php';


$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if($user->data()->free_trial_offer)
    {
        echo "You've already used your free trial offer.";
    } else if(!empty(Input::get('region'))){
        $service = new Service();
        $service->create(1 /* Basic Plan */, $user->data()->id, "", 1, Input::get('region'));
        DB::getInstance()->update('users', $user->data()->id, array(
            'free_trial_offer' => 1
        ));
        Redirect::to('profile.php');
    } else {
        echo "Missing region?";
    }
}