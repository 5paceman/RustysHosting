<?php
require_once 'core/init.php';

$user = new User();
if(Input::exists() && $user->isLoggedIn()) {
    if(Token::check(Input::get('token')))
    {
        $db = DB::getInstance();
        $service_id = Input::get('serviceId');
        if($service_id)
        {
            $service = $db->get('services', array('id', '=', $service_id));
            if($service->count() && $service->first()->user_id == $user->data()->id)
            {
                \Stripe\Stripe::setApiKey(Config::get('stripe/secret_api_key'));
                
                $session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'mode' => 'setup',
                    'setup_intent_data' => [
                      'metadata' => [
                        'customer_id' => $user->data()->stripe_id,
                        'subscription_id' => $service->first()->stripe_id,
                      ],
                    ],
                    'success_url' => (Config::get('stripe/success_page').'?session_id={CHECKOUT_SESSION_ID}'),
                    'cancel_url' => (Config::get('stripe/cancel_page')),
                  ]);
                
                ?>
                <!DOCTYPE html>
                <head>
                <meta charset="utf-8">
                <title>Checkout</title>
                <script src="https://js.stripe.com/v3/"></script>
                <script>
                    var stripe = Stripe('<?php echo Config::get('stripe/public_api_key'); ?>');
                    function load() {
                    stripe.redirectToCheckout({sessionId: '<?php echo $session->id; ?>'}).then(function (result) {});
                    }
                </script>
                </head>
                <body onload="load()">
                </body>
                </html>
                <?php

            }
        }
    }
}