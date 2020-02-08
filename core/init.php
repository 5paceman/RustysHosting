<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$GLOBALS['config'] = array (
    'mysql' => array(
        'host' => 'localhost',
        'username' => 'webserver',
        'password' => 'S3cur3D4t4ba53!?',
        'db' => 'hosting'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token' 
    ),
    'stripe' => array(
        'public_api_key' => 'pk_live_d0Mkee67K7Fl7AtmdSAaRCms00XdFGfGyG',
        'secret_api_key' => 'sk_live_HxVaRVFpvmPdU19pLOONqF1600exQwNaC0',
        'signing_secret' => 'whsec_CmgImzjnus1bv97KIWDgaxnfqauiJ1hP',
        'success_page' => 'https://rustyhosting.io/profile.php',
        'cancel_page' => 'https://rustyhosting.io/profile.php'
    ),
    'email' => array(
        'smtp_host' => "smtp.office365.com",
        'smtp_port' => '587',
        'username' => 'noreply@rustyshosting.io',
        'password' => 'N0R3ply3m41l!?',
        'email' => 'noreply@rustyshosting.io',
        'email_name' => 'Rustys Hosting',
    ),
    'redis' => array(
        'ip' => '127.0.0.1',
        'port' => '6379'
    ),
    'backup' => array(
        'backup_expiry' => '30',
        'spaces_secret' => 'NEVnOA0WeovQLP1EAJ1IkYcsoaf/ZSlrnGnU9rZHezY',
        'spaces_access' => 'JLT2L3FRCBA7ZQPUXH72',
        'rustyshosting-eu' => 'fra1.digitaloceanspaces.com'
    )
);

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class) {
    require_once 'classes/'.$class.'.php';
});

require_once 'functions/sanitize.php';



if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));
    if($hashCheck->count()) {
       $user = new User($hashCheck->first()->user_id);
       $user->login(); 
    }
}
?>
