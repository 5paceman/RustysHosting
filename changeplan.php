<?php
require_once 'core/init.php';
require_once 'functions/sanitize.php';

$user = new User();
$error = "";
$cost = 0;
$service = null;
$current_prorations = [];
$proration_date = time();
if(Input::exists() && $user->isLoggedIn()) {
    if(Token::check(Input::get('token')))
    {
        $db = DB::getInstance();
        $service_id = Input::get('serviceId');
        if($service_id)
        {
            $services = $db->get('services', array('id', '=', $service_id));
            
            if($services->count() && $services->first()->user_id == $user->data()->id)
            {
                $service = new Service($services->first()->id, null);
                $planId = Input::get('planId');
                $currentPlanId = $service->data()->plan_id;
                if($planId)
                {
                    if($planId === $currentPlanId)
                    {
                        $error = "You can't change to the plan you're currently on.";
                    } else {
                        $plans = $db->get('plans', array('id', '=', $planId));
                        if($plans->count())
                        {
                            if(Input::get('confirmed'))
                            {
                                \Stripe\Stripe::setApiKey(Config::get('stripe/secret_api_key'));
                                $subscription = \Stripe\Subscription::retrieve($service->data()->stripe_id);
                                \Stripe\Subscription::update($service->data()->stripe_id, [
                                    'items' => [
                                        [
                                            'id' => $subscription->items->data[0]->id,
                                            'plan' => $plans->first()->stripe_id,
                                        ],
                                    ],
                                ]);

                                $db->update('services', $service->data()->id, array(
                                    'plan_id' => $plans->first()->id
                                ));
                                $plans = $db->get('plans', array('id', '=', $planId));
                                Redis::getInstance()->putJobToMachine($service->data()->machine_id, "ChangePlan.sh plan{$currentPlanId} plan{$plans->first()->id} {$service->data()->service_id}", array(
                                    "locks" => array(
                                        "service:{$service->id()}"
                                    )
                                ));
                            } else {
                                \Stripe\Stripe::setApiKey(Config::get('stripe/secret_api_key'));
                                
                                $subscription = \Stripe\Subscription::retrieve($service->data()->stripe_id);

                                $items = [
                                    [
                                        'id' => $subscription->items->data[0]->id,
                                        'plan' => $plans->first()->stripe_id, 
                                    ],
                                ];

                                $invoice = \Stripe\Invoice::upcoming([
                                    'customer' => $user->data()->stripe_id,
                                    'subscription' => $service->data()->stripe_id,
                                    'subscription_items' => $items,
                                    'subscription_proration_date' => $proration_date,
                                ]);

                                foreach ($invoice->lines->data as $line) {
                                    if ($line->period->start - $proration_date <= 1) {
                                        array_push($current_prorations, $line);
                                        $cost += $line->amount;
                                    }
                                }
                                $cost = sprintf('$%0.2f', $cost / 100.0);
                            }
                        }
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<!--  Last Published: Sun Nov 17 2019 02:03:58 GMT+0000 (Coordinated Universal Time)  -->
<html data-wf-page="5dd07f30edfe6a37ec68c3c4" data-wf-site="5d8fb360124e070f85051b6c">
<head>
  <meta charset="utf-8">
  <title>Change Plan</title>
  <meta content="Change Plan" property="og:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/rustyshosting.webflow.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic","Work Sans:regular"]  }});</script>
  <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
</head>
<body class="body-2">
  <div class="main">
    <div class="form-block-3 w-form">
    <?php
        if($error)
        {
            ?> <p><?php echo $error; ?></p> <?php
        }else if(!Input::get('planId'))
        {
    ?>
      <form id="email-form" action="" method="post">
        <h3 class="heading-5">Change Plan</h3>
        <label for="name">Service ID</label>
        <input type="text" class="text-field-3 w-input" value="<?php echo $service->data()->service_id; ?>" readonly>
        <input type="hidden" id="serviceId" name="serviceId" class="text-field-3 w-input" value="<?php echo $service->data()->id; ?>">
        <label for="planID">New Plan</label>
        <select id="planId" name="planId" data-name="plan" class="w-select">
            <option value="1">12$ Wood - 20-30 Slots</option>
            <option value="2">16$ Stone - 30-50 Slots</option>
            <option value="3">24$ Metal - 50-100 Slots</option>
            <option value="4">32$ Kevlar - 100+ Slots</option>
        </select>
        <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>">
        <label>
        <input type="submit" value="Change" class="submit-button-2 w-button">
        
      </form>
      
    <?php
        } else if(Input::get('planId') && !Input::get('confirmed'))
        {
    ?>
        <form id="email-form" action="" method="post">
        <h3 class="heading-5">Change Plan</h3>
        <label for="name">Service ID</label>
        <input type="text" class="text-field-3 w-input" value="<?php echo $service->data()->service_id; ?>" readonly>
        <input type="hidden" id="serviceId" name="serviceId" class="text-field-3 w-input" value="<?php echo $service->data()->id; ?>">
        <input type="hidden" id="planId" name="planId" value="<?php echo escape(Input::get('planId')); ?>" readonly>
        <label><?php echo $cost; ?> will be added to your next invoice, are you happy to continue?</label>
        <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>">
        <input type="hidden" id="confirmed" name="confirmed" value="1">
        <label>
        <input type="submit" value="Change" class="submit-button-2 w-button">
        <p><?php echo $error; ?></p>
      </form>
    <?php 
        } else if(Input::get('planId') && Input::get('confirmed')) {
            echo 'Complete. Please restart your server.';
        }
    ?>
    </div>
  </div>
</body>
</html>