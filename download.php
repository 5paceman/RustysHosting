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
        $service = new Service();
            if($service->find(Input::get('service_id'))) {
                if($service->data()->user_id === $user->data()->id && $service->isValid())
                {
                    $backup = DB::getInstance()->get('backups', array('id', '=', Input::get('backupID')));
                    if($backup->count())
                    {
                        if($backup->first()->service === $service->id())
                        {
                            $space = $backup->first()->space;
                            $file = $backup->first()->path;
                            
                            $configuration = new Configuration(Config::get('backup/spaces_access'), Config::get('backup/spaces_secret'));
                            $configuration->setEndpoint(Config::get('backup/'.$space));
                            $connector = new Connector($configuration);
                            $url = $connector->getAuthenticatedURL($space, $file, 300);
                            $url = str_replace(Config::get('backup/'.$space), Config::get('backup/'.$space).'/'.$space, $url);
                            header('Location: '.$url);
                        }
                    }
                }
            }
        }
    
}
?>