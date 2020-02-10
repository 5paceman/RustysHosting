<?php
chdir("/var/www/");
require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn() && $user->isAdmin())
{
    if(Input::exists('get'))
    {
        $statistic = Input::get('statistic');
        if($statistic === "all")
        {
            $json = array();
            $machines = DB::getInstance()->get('machines', array('*'));
            foreach($machines->results() as $machine)
            {
                $cpu = getStat('cpu', $machine->dns_name);
                $memory = getStat('memory', $machine->dns_name);
                $hdd = getStat('hdd', $machine->dns_name);
                $users = getStat('users', $machine->dns_name);
                $json[$machine->dns_name]['cpu'] = $cpu;
                $json[$machine->dns_name]['memory'] = $memory;
                $json[$machine->dns_name]['hdd'] = $hdd;
                $json[$machine->dns_name]['users'] = $users;
            }
            header('Content-Type: application/json');
            echo json_encode($json);
        } else if($statistic === "logs")
        {
            $logs = array_reverse(Redis::getInstance()->getList("reporting:machines:events"));
            echo '<pre>';
            foreach($logs as $line)
            {
                echo $line.'<br/>';
            }
            echo '</pre>';
        }
    }
}

function getStat($stat, $machine) {
    $result = Redis::getInstance()->getList("reporting:machines:".$machine.":".$stat);
    if(count($result) > 48)
    {
        Redis::getInstance()->trimList("reporting:machines:".$machine.":".$stat, count($result) - 48 -1, count($result));
        $result = Redis::getInstance()->getList("reporting:machines:".$machine.":".$stat);
    }
    return $result;
}