<?php
require_once 'core/init.php';

$user = new User();
if(!$user->isLoggedIn()) {
    Redirect::to('login.php');
} else {
    if(Input::exists()) {
        if(Token::check(Input::get('token'))) {
            $validate = new Validate();
            $validation = $validate->check(array(
                'email' => array(
                    'email' => true,
                    'required' => true
                ),
                'firstname' => array(
                    'required' => true,
                    'min' => 1,
                    'max' => 50
                ),
                'lastname' => array(
                    'required' => true,
                    'min' => 1,
                    'max' => 50
                )
            ));

            if($validation->passed()) {
                try {
                    $user->update(array(
                        'email' => Input::get('email'),
                        'firstname' => Input::get('firstname'),
                        'lastname' => Input::get('lastname')
                    ));
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                foreach($validation->errors() as $error) {
                    echo $error.'<br>';
                }
            }
        }
    }
}

?>