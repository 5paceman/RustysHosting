<?php
require_once 'core/init.php';

$user = new User();

if(Input::exists() && $user->isLoggedIn()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check(array(
            'passwordCurrent' => array(
                'required' => true,
                'min' => 6
            ),
            'password' => array(
                'required' => true,
                'min' => 6
            ),
            'repeat_password' => array(
                'required' => true,
                'matches' => 'password'
            )
        ));

        if($validation->passed()) {

            $currentPassword = Hash::make(Input::get('passwordCurrent'), $user->data()->salt);
            if($user->data()->password === $currentPassword) {
                $newSalt = Hash::salt(32);
                $newPassword = Hash::make(Input::get('password'), $newSalt);
                $user->update(array(
                    'password' => $newPassword,
                    'salt' => $newSalt
                ));

                Session::flash('home', 'Your password has been changed!');
                Redirect::to('index.php');
            } else {
                echo 'Incorrect password.';
            }
        } else {
            foreach($validation->errors() as $error) {
                echo $error.'<br>';
            }
        }
    }
}

?>

<form method="POST" action="">
    <label for="passwordCurrent">Current Password<br/></label>
    <input id="passwordCurrent" autocomplete="off" name="passwordCurrent" type="password" /><br/>
    <label for="password">Password<br/></label>
    <input id="password" autocomplete="off" name="password" type="password" /><br/>
    <label for="repeat_password">Repeat Password<br/></label>
    <input id="repeat_password" autocomplete="off" name="repeat_password" type="password" /><br/>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>"/><br/>
    <button type="submit">Submit</button>
</form>