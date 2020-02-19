<?php
chdir("/var/www/rustyshosting.io/");
require_once 'core/init.php';
require_once 'functions/sanitize.php';

$user = new User();

if(!$user->isLoggedIn())
{
    Redirect::to(404);
}

if(!$user->isAdmin())
{
    Redirect::to(404);
}

if(Input::exists())
{
    if(Input::get('id') && Input::get('planId') && Input::get('regionId'))
    {
        $plans = DB::getInstance()->get('plans', array('id', '=', Input::get('planId')));
        if($plans->count())
        {
            $service = new Service();
            $service->create(Input::get('planId'), Input::get('id'), 0, 1, Input::get('regionId'));
            Redirect::to("index.php");
        }
    }
}