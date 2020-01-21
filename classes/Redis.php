<?php
require_once 'core/init.php';

class Redis {

    private static $_instance = null;
    public static $COMMON_QUEUE = "brooce:queue:common:pending";
    private $_predis;

    private function __construct()
    {
        $this->_predis = new Predis\Client([
            'host' => Config::get('redis/ip'),
            'port' => Config::get('redis/port')
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

    public function putJobToMachine($machine_id, $command, $options = array())
    {
        $MachineName = DB::getInstance()->get('machines', array('id', '=', $machine_id))->first()->dns_name;
        $this->putJob($command, "brooce:queue:".$MachineName.":pending", $options);
    }

    public function getList($key)
    {
        $value = $this->_predis->lrange($key, 0, -1);
        return $value;
    }

    public function trimList($key, $start, $end)
    {
        $this->_predis->ltrim($key, $start, $end);
    }

    public function get($key)
    {
        $value = $this->_predis->get($key);
        return $value;
    }

    public function getJson($key)
    {
        $value = $this->_predis->get($key);
        return json_decode($value);
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