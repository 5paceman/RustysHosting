<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$GLOBALS['config'] = array (
    'mysql' => array(
        'host' => 'redacted',
        'username' => 'redacted',
        'password' => 'redacted',
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
    'recaptcha' => array(
        'secret' => "redacted",
        'site' => "redacted"
    ),
    'stripe' => array(
        'public_api_key' => 'redacted',
        'secret_api_key' => 'redacted',
        'signing_secret' => 'redacted',
        'success_page' => 'https://rustyhosting.io/profile.php',
        'cancel_page' => 'https://rustyhosting.io/profile.php'
    ),
    'email' => array(
        'smtp_host' => "smtp.office365.com",
        'smtp_port' => '587',
        'username' => 'noreply@rustyshosting.io',
        'password' => 'redacted',
        'email' => 'noreply@rustyshosting.io',
        'email_name' => 'Rustys Hosting',
    ),
    'redis' => array(
        'ip' => '127.0.0.1',
        'port' => '6379'
    ),
    'backup' => array(
        'backup_expiry' => '30',
        'spaces_secret' => 'redacted',
        'spaces_access' => 'redacted',
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
