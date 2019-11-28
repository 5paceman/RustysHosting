<?php
require_once 'core/init.php';

class Redis {

    private static $_instance = null;
    private static $COMMON_QUEUE = "brooce:queue:common:pending";
    private $_predis;

    private function __construct()
    {
        $this->$_predis = new Predis\Client([
            'host' => Config::get('redis/ip'),
            'port' => Config::get('redis/post')
        ]);
    }

    public static function getInstance()
    {
        if(!isset(self::$_instance))
        {
            self::$_instance = new Redis();
        }
        return self::$_instance;
    }

    public function putJob($command, $queue, $options = array())
    {
        $job = array(
            'command' => $command
        );
        $job = array_merge($job, $options);
        $this->_predis->lpush($queue, json_encode($job));
    }

    public function putCronJob($command, $name, $time, $options)
    {
        $job = $time." queue:common ".$options." ".$command;
        $this->_predis->hset("brooce:cron:jobs", $name, $job);
    }
}
?>