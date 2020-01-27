<?php

require_once 'core/init.php';

$user = new User();
if(Input::exists() && $user->isLoggedIn()) {
    if(Token::check(Input::get('token')))
    {
        $db = DB::getInstance();
        $service_id = Input::get('serviceId');
        $isAdmin = $user->isAdmin();
        if($service_id)
        {
            $service = $db->get('services', array('id', '=', $service_id));
            if($service->count() && ($service->first()->user_id == $user->data()->id || $isAdmin))
            {
                \Stripe\Stripe::setApiKey(Config::get('stripe/secret_api_key'));
                $subscription = \Stripe\Subscription::update(
                    $service->first()->stripe_id,
                    [
                        'cancel_at_period_end' => true,
                    ]
                );
                if($subscription->cancel_at_period_end)
                {
                    http_response_code(200);
                    echo 'Success';
                } else {
                    http_response_code(400);
                    echo 'Failed to cancel subscription.';
                }
            }

        }
    }

}