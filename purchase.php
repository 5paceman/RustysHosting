<?php
require_once 'core/init.php';
$user = new User();
if(Input::exists() && $user->isLoggedIn()) {
    if(Token::check(Input::get('token'))) {
        $db = DB::getInstance();
        $plan_id = Input::get('planId');
        if($plan_id) {
             $plan = $db->get('plans', array('id', '=', $plan_id));
             if($plan->count()) {
              \Stripe\Stripe::setApiKey(Config::get('stripe/secret_api_key'));
              $sessionData = array([
                'payment_method_types' => ['card'],
                'subscription_data' => [
                  'metadata' => [
                    'user_id' => $user->data()->id,
                    'region_id' => Input::get('regionId'),
                    'service_id' => Input::get('serviceId')
                  ],
                  'items' => [[
                    'plan' => $plan->first()->stripe_id,
                  ]],
                ],
                'success_url' => (Config::get('stripe/success_page').'?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => (Config::get('stripe/cancel_page')),
              ]);

              if($user->isStripeCustomer())
              {
                $customer = array('customer' => $user->data()->stripe_id);
                $sessionData += $customer;
              }

              $session = \Stripe\Checkout\Session::create($sessionData);
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