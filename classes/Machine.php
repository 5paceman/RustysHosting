<?php
class Machine {

    public static function getLeastUsedMachine($region_id) {
        $count = 1000;
        $machine_id = -1;
        $machines = DB::getInstance()->get('machines', array('region_id', '=', $region_id));
        foreach($machines->results() as $machine)
        {
            $services = DB::getInstance()->get('services', array('machine_id', '=', $machine->id));
            if($services->count() < $count)
            {
                $count = $services->count();
                $machine_id = $machine->id;
            }
        }
        return $machine_id;
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
        return ($highestPort + 3);
    }
}