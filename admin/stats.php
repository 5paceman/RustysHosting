<?php

require_once 'init/core.php';

$user = new User();

if($user->isLoggedIn() && $user->isAdmin())
{
    if(Input::exists())
    {
        $statistic = Input::get('statistic');
        if($statistic === "cpu")
        {
            $machine = Input::get('machine');
            $cpu = Redis::getInstance()->get("reporting:machines:".$machine."cpu");
            echo $cpu;
        } else if($statistic === "memory")
        {
            $machine = Input::get('machine');
            $memory = Redis::getInstance()->get("reporting:machines:".$machine."memory");
            echo $memory;
        } else if($statistic === "hdd")
        {
            $machine = Input::get('machine');
            $hdd = Redis::getInstance()->get("reporting:machines:".$machine."hdd");
            echo $hdd;
        } else if($statistic === "users")
        {
            $machine = Input::get('machine');
            $users = Redis::getInstance()->get("reporting:machines:".$machine."users");
            echo $users;
        }
    }
}