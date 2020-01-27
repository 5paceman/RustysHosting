<?php
require_once 'core/init.php';

define('AKEEBAENGINE', 1);

use Akeeba\Engine\Postproc\Connector\S3v4\Configuration;
use Akeeba\Engine\Postproc\Connector\S3v4\Connector;

$user = new User();
if(!$user->isLoggedIn()) {
    echo 'You need to be logged in.';
} else {
    if(Input::exists()) {
        $isAdmin = $user->isAdmin();
        $service = new Service();
            if($service->find(Input::get('service_id'))) {
                if(($service->data()->user_id === $user->data()->id && $service->isValid()) || $isAdmin)
                {
                    switch(Input::get('command'))
                    {
                        case 'restart':
                            echo 'Server is restarting..';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "systemctl restart {$service->data()->service_id}", array(
                                "locks" => array(
                                    "service:{$service->id()}"
                                )
                            ));
                        break;
                        case 'stop':
                            echo 'Server is stopping..';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "systemctl stop {$service->data()->service_id}", array(
                                "locks" => array(
                                    "service:{$service->id()}"
                                )
                            ));
                        break;
                        case 'start':
                            echo 'Server is starting..';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "systemctl start {$service->data()->service_id}", array(
                                "locks" => array(
                                    "service:{$service->id()}"
                                )
                            ));
                        break;
                        case 'installrustio':
                            echo 'Rust IO is installing. Please allow 5 minutes and then restart your server.';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "InstallRustIO.sh /home/{$service->data()->service_id}/rust", array(
                                "locks" => array(
                                    "service:{$service->id()}"
                                )
                            ));
                        break;
                        case 'update':
                            echo 'Rust is updating. Please wait for server startup.';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "UpdateRust.sh {$service->data()->service_id}", array(
                                "locks" => array(
                                    "service:{$service->id()}"
                                )
                            ));
                        break;
                        case 'oxide':
                            echo 'Oxide is updating/Installing. Please wait for server startup.';
                            Redis::getInstance()->putJobToMachine($service->data()->machine_id, "UpdateOxide.sh {$service->data()->service_id}", array(
                                "locks" => array(
                                    "service:{$service->id()}"
                                )
                            ));
                        break;
                        case 'restore':
                            $backup = DB::getInstance()->get('backups', array("id", "=", Input::get('backupID')));
                            if($backup->count())
                            {
                                if($backup->first()->service === $service->id())
                                {
                                    echo "Backup is restoring. Please wait for server startup.";
                                    Redis::getInstance()->putJobToMachine($service->data()->machine_id, "RestoreBackup.sh {$service->data()->service_id} {$backup->first()->path} {$backup->first()->space}", array(
                                        "locks" => array(
                                            "service:{$service->id()}"
                                        )
                                    ));
                                }
                            }
                        break;
                        case 'deletebackup':
                            $backup = DB::getInstance()->get('backups', array("id", "=", Input::get('backupID')));
                            
                            if($backup->count())
                            {
                                if($backup->first()->service == $service->id())
                                {
                                    echo "Backup deleted.";
                                    $space = $backup->first()->space;
                                    $configuration = new Configuration(Config::get('backup/spaces_access'), Config::get('backup/spaces_secret'));
                                    $configuration->setEndpoint(Config::get('backup/'.$space));
                                    $connector = new Connector($configuration);
                                    $connector->deleteObject($space, $backup->first()->path);
                                    DB::getInstance()->delete('backups', array("id", "=", Input::get('backupID')));
                                }
                            }
                        break;
                        default:
                            echo 'Unknown command.';
                        break;
                    }
                } else {
                    echo 'Service doesnt exist.';
                }
            } else {
                echo 'Service doesnt exist.';
            }
    }
}