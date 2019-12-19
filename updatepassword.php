<?php
require_once 'core/init.php';

$user = new User();

if(Input::exists('POST') && $user->isLoggedIn()) {
    if(Token::check(Input::get('token'))) {
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