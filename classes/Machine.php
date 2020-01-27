<?php
class Machine {

    public static function getLeastUsedMachine($region_id) {
        $machine_id = -1;
        $candidates = array();
        $machines = DB::getInstance()->get('machines', array('region_id', '=', $region_id));
        foreach($machines->results() as $machine)
        {
            $totalUsedRam = 0;
            $services = DB::getInstance()->get('services', array('machine_id', '=', $machine->id));
            foreach($services->results() as $service)
            {
                $plan = DB::getInstance()->get('plans', array('id', '=', $service->plan_id));
                $totalUsedRam += $plan->first()->ram;
            }
            if($totalUsedRam < $machine->ram - 4)
            {
                $candidates["{$machine->id}"] = ($totalUsedRam / $machine->ram);
            }
        }

        $count = 5;
        foreach($candidates as $candidateid => $usedrampercentage)
        {
            if($usedrampercentage < $count)
            {
                $machine_id = $candidateid;
                $count = $usedrampercentage;
            }
        }
        return $machine_id;
    }

    public static function getIP($machine_id)
    {
        $machine = DB::getInstance()->get('machines', array('id', '=', $machine_id));
        return $machine->first()->ip;
    }

    public static function getNextPort($game_id, $machine_id) {
        $game = DB::getInstance()->get('games', array('id', '=', $game_id));
        $highestPort = $game->first()->baseport - 1;
        $services = DB::getInstance()->get('services', array('machine_id', '=', $machine_id));
        foreach($services->results() as $service)
        {
            if($service->port > $highestPort)
            {
                $highestPort = $service->port;
            }
        }
        $highestPort = $highestPort + 3;
        return $highestPort;
    }
}