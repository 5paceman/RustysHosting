<?php
require_once 'core/init.php';

$user = new User();

if(Input::exists('POST') && $user->isLoggedIn()) {
    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'current-password' => array(
            'required' => true,
            'min' => 6
        ),
        'new-password' => array(
            'required' => true,
            'min' => 6
        ),
        'repeat-password' => array(
            'required' => true,
            'matches' => 'new-password'
        )
    ));

    if($validation->passed()) {

        $currentPassword = Hash::make(Input::get('current-password'), $user->data()->salt);
        if($user->data()->password === $currentPassword) {
            $newSalt = Hash::salt(32);
            $newPassword = Hash::make(Input::get('new-password'), $newSalt);
            $user->update(array(
                'password' => $newPassword,
                'salt' => $newSalt
            ));
            echo "Success";
        } else {
            echo 'Incorrect password.';
        }
    } else {
        foreach($validation->errors() as $error) {
            echo $error.'<br>';
        }
    }
}

?>