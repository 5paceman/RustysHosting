<?php

require_once 'core/init.php';

\Stripe\Stripe::setApiKey(Config::get('stripe/secret_api_key'));

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, Config::get('stripe/signing_secret')
    );
} catch (\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

switch ($event->type) {
    case 'checkout.session.completed':
        handleCheckoutSessionSucceeded($event->data->object);
    break;
    case 'invoice.payment_succeeded':
        handleInvoicePaymentSucceeded($event->data->object);
    break;
    case 'invoice.payment_failed':
        handleInvoiceFailed($event->data->object);
    break;
}

http_response_code(200);

function handleInvoiceFailed($invoice)
{
    $customer = \Stripe\Customer::retrieve($invoice->customer);
    $userid = DB::getInstance()->get('users', array('stripe_id', '=', $invoice->customer))->first()->id;
    $amount = sprintf('$%0.2f', $invoice->amount_due / 100.0);
    foreach($invoice->lines->data as $invoiceline)
    {
        if($invoiceline->subscription)
        {
            $service = DB::getInstance()->get('services', array('stripe_id', '=', $invoiceline->subscription));
            $serviceId = $service->first()->id;
            if($service->count())
            {
                Redis::getInstance()->putJobToMachine($service->first()->machine_id, "PauseAccount.sh {$service->first()->service_id}", array(
                    "locks" => array(
                        "service:{$serviceId}"
                    )
                ));
                $datetime = new DateTime("-1 day");
                $expiry = $datetime->format('Y-m-d H:i:s');
                $result = DB::getInstance()->update('services', $serviceId, array(
                    'expiry' => $expiry
                ));
            }
        }
    }

    $name = "Invoice".($invoice->created).".pdf";
    file_put_contents('/var/www/invoices/'.$name, fopen($invoice->invoice_pdf, 'r'));
    $user = new User($userid);
    Email::getInstance()->sendEmailWithAttachments($user->data()->email, "Failed Invoice Payment", "failed-invoice", array(
        'name' => $user->data()->firstname,
        'cost' => $amount,
        'pay_url' => $invoice->hosted_invoice_url
    ), array(
        '/var/www/invoices/'.$name
    ));
}

function handleInvoicePaymentSucceeded($invoice)
{
    if($invoice->billing_reason === "subscription_cycle" || $invoice->billing_reason === "manual")
    {
        $db = DB::getInstance();
        foreach($invoice->lines->data as $invoiceline)
        {
            if($invoiceline->subscription)
            {
                $subscription_id = $invoiceline->subscription;
                $services = $db->get('services', array('stripe_id', '=', $subscription_id));
                if($services->count())
                {
                    $service = new Service($services->first()->id, null);
                    $datetime = new DateTime("+1 month +2 day");
                    $expiry = $datetime->format('Y-m-d H:i:s');
                    $result = $db->update('services', $service->id(), array(
                        'expiry' => $expiry
                    ));
                    Redis::getInstance()->putJobToMachine($service->data()->machine_id, "RenewBilling.sh {$service->data()->service_id}", array(
                        "locks" => array(
                            "service:{$service->data()->id}"
                        )
                    ));
                    if(!$result)
                    {
                        print_r($db->errorInfo());
                    }
                }
            }
        }
    }
    if(isset($invoice->invoice_pdf)) {
        $userid = DB::getInstance()->get('users', array('stripe_id', '=', $invoice->customer))->first()->id;
        $name = "Invoice".($invoice->created).".pdf";
        file_put_contents('/var/www/invoices/'.$name, fopen($invoice->invoice_pdf, 'r'));
        $user = new User($userid);
        Email::getInstance()->sendEmailWithAttachments($user->data()->email, "Invoice", "invoice", array(
            'name' => $user->data()->firstname,
            'invoice_url' => $invoice->hosted_invoice_url
        ), array(
            '/var/www/invoices/'.$name
        ));
        unlink('/var/www/invoices/'.$name);
    }
}

function handleCheckoutSessionSucceeded($checkout) {
    if($checkout->mode === "subscription")
    {
        $db = DB::getInstance();
        $subscription = \Stripe\Subscription::retrieve(
            $checkout->subscription
        );
        print_r($subscription->metadata);
        $users = $db->get('users', array('id', '=', $subscription->metadata->user_id));
        if($users->count()) {
            $user = $users->first();
            $plan_id = $subscription->items->data[0]->plan->id;
            $plans = $db->get('plans', array('stripe_id', '=', $plan_id));
            if($plans->count()) {
                if(isset($subscription->metadata->service_id))
                {
                    $service = new Service($subscription->metadata->service_id, null);
                    if($service->exists())
                    {
                        $datetime = new DateTime("+1 month +2 day");
                        $expiry = $datetime->format('Y-m-d H:i:s');
                        $result = $db->update('services', $subscription->metadata->service_id, array(
                            'expiry' => $expiry,
                            'stripe_id' => $subscription->id
                        ));
                    }
                } else {
                    $service = new Service();
                    $service->create($plans->first()->id, $user->id, $subscription->id, 1, $subscription->metadata->region_id);
                }
                $db->update('users', $user->id, array(
                    'stripe_id' => $checkout->customer
                ));   
            }
        }
    } else if ($checkout->mode === "setup") {
        $setup_intent = \Stripe\SetupIntent::retrieve($checkout->setup_intent);
        print_r($setup_intent);
        $payment_method = \Stripe\PaymentMethod::retrieve($setup_intent->payment_method);
        $payment_method->attach(['customer' => $setup_intent->metadata->customer_id]);
        \Stripe\Subscription::update(
            $setup_intent->metadata->subscription_id,
            [
                'default_payment_method' => $setup_intent->payment_method,
            ]);
    }
}