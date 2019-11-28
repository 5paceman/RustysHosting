<?php
require_once 'core/init.php';

if(!$username = Input::get('user')) {
    Redirect::to('index.php');
} else {
    $user = new User($username);
    if($user->exists()) {
        echo 'Username:'.$user->data()->username.'<br>';
        echo 'Email:'.$user->data()->email.'<br>';
    } else {
        Redirect::to(404);
    }
}

?>

