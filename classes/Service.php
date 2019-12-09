<?php

require 'functions/stringtools.php';

class Service {
    private $_data,
            $_db,
            $_serviceConfiguration;

    public function __construct($id = null, $data = null) {
        $this->_db = DB::getInstance();
        if($id && !$data)
        {
            $this->find($id);
        } else if(!$id && $data) {
            $this->_data = $data;
        }
    }



    private function retrieveConfig()
    {
        $service_config = $this->_db->get('service_configurations', array('service_id', '=', $this->id()));
        if($service_config->count())
        {
            $this->_serviceConfiguration = $service_config->first();
        }
    }

    public function findAll($user_id) {
        $services = array();
        $data = $this->_db->get('services', array('user_id', '=', $user_id));
        if($data->count()) {
            foreach($data->results() as $row) {
                $service = new Service(null, $row);
                $services[] = $service;
            }
        }
        return $services;
    }

    public function find($id)
    {
        if(is_numeric($id))
        {
            $data = $this->_db->get('services', array('id', '=', $id));
        } else {
            $data = $this->_db->get('services', array('service_id', '=', $id));
        }
        if($data->count()) {
            $this->_data = $data->first();
            $this->retrieveConfig();
            return true;
        } else {
            return false;
        }
    }

    public function create($plan_id, $user_id, $stripe_id, $game_id, $region_id) {
        $machine_id = Machine::getLeastUsedMachine($region_id);
        $datetime = new DateTime("+1 month");
        $expiry = $datetime->format('Y-m-d H:i:s');
        $port = Machine::getNextPort($game_id, $machine_id);
        $service_id = generateRandomString(12, false);
        $service_password = generateRandomString(10, true);
        $result = $this->_db->insert('services', array(
            'plan_id' => $plan_id,
            'service_id' => $service_id,
            'service_password' => $service_password,
            'user_id' => $user_id,
            'expiry' => $expiry,
            'machine_id' => $machine_id,
            'port' => $port,
            'stripe_id' => $stripe_id
        ));
        if(!$result) {
            print_r($this->_db->errorInfo());
        } else {
            $serviceId = $this->_db->first()->id;
            Redis::getInstance()->putJobToMachine($machine_id, "MakeUser.sh ".$service_id." plan1 20971520 ".$service_password." ".$port);
            
            
            $result = $this->_db->insert('service_configurations', array(
                'service_id' => $serviceId
            ));
        }
    }

    public function plan() {
        $plan = $this->_db->get('plans', array('id', '=', $this->_data->plan_id));
        if($plan->count()) {
            return $plan->first()->name;
        } else {
            return '';
        }
    }

    public function id()
    {
        return $this->_data->id;
    }

    public function expiry() {
        return $this->_data->expiry;
    }

    public function ip() {
        $machine = $this->_db->get('machines', array('id', '=', $this->_data->machine_id));
        if($machine->count()) {
            return $machine->first()->ip;
        } else {
            return '';
        }
    }

    public function config()
    {
        if(!isset($this->_serviceConfiguration))
        {
            $this->retrieveConfig();
        }
        return $this->_serviceConfiguration;
    }

    public function stripeID()
    {
        return $this->_data->stripe_id;
    }

    public function port() {
        return $this->_data->port;
    }
    

    public function data() {
        return $this->_data;
    }
}