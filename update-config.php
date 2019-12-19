<?php
require_once 'core/init.php';

$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if(Input::exists()) {
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'service_id' => array(
                'required' => true
            ),
            'hostname' => array(
                'required' => true,
                'max' =>  50
            ),
            'maxplayers' => array(
                'required' => true,
                'numeric' => true
            ),
            'worldsize' => array(
                'required' => true,
                'numeric' => true,
                'minNum' => 1000,
                'maxNum' => 6000
            ),
            'seed' => array(
                'required' => true,
                'numeric' => true
            ),
            'globalchat' => array(
                'required' => true,
            ),
            'tickrate' => array(
                'required' => true,
                'minNum' => 10,
                'maxNum' => 25
            ),
            'headerimage' => array(
                'required' => true,
                'max' => 256
            ),
            'description' => array(
                'required' => true,
                'max' => 512
            )
        ));

        if($validation->passed()) {
            $service = new Service();
            if($service->find(Input::get('service_id'))) {
                if($service->data()->user_id === $user->data()->id)
                {
                    $result = DB::getInstance()->update('service_configurations', $service->id(), array(
                        'hostname' => Input::get('hostname'),
                        'world_size' => Input::get('worldsize'),
                        'seed' => Input::get('seed'),
                        'tick_rate' => Input::get('tickrate'),
                        'max_players' => Input::get('maxplayers'),
                        'global_chat' => ((Input::get('globalchat') === 'on') ? 1 : 0),
                        'header_image' => Input::get('headerimage'),
                        'description' => Input::get('description'),
                    ), 'service_id');
                    if($result)
                    {
                        Redis::getInstance()->putJobToMachine($service->data()->machine_id, "UpdateConfig.sh \"".Input::get('service_id')."\" \"".Input::get('hostname')."\""." \"".Input::get('worldsize')."\" \"".Input::get('seed')."\" \"".Input::get('tickrate')."\" \"".Input::get('maxplayers')."\" \"".Input::get('description')."\" \"".Input::get('headerimage')."\" ".((Input::get('globalchat') === 'on') ? "\"true\"" : "\"false\""));
                        echo 'Updated.';
                    } else {
                        echo 'Problem updating settings, please try again';
                        print_r(DB::getInstance()->errorInfo());
                    }
                } else {
                    echo 'Service doesnt exist.';
                }
            } else {
                echo 'Service doesnt exist.';
            }

        } else {
            foreach($validation->errors() as $error) {
                echo $error.'<br>';
            }
        }
    } else {
        echo 'No input can be left blank!';
    }
}