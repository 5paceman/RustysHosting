<?php
require_once 'core/init.php';



if(Input::exists() && Token::check(Input::get('token'))) {

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'username' => array(
            'required' => true,
            'min' => 2,
            'max' => 20,
            'unique' => 'users',
            'numeric' => false
        ),
        'password' => array(
            'required' => true,
            'min' => 6
        ),
        'repeat_password' => array(
            'required' => true,
            'matches' => 'password'
        ),
        'email' => array(
            'required' => true,
            'email' => true       
        ),
        'Firstname' => array(
            'required' => true
        ),
        'Lastname' => array(
            'required' => true
        )
    ));

    if($validation->passed()) {
        $user = new User();

        $salt = Hash::salt(32);
        try {
            $user->create(array(
                'username' => Input::get('username'),
                'password' => Hash::make(Input::get('password'), $salt),
                'salt' => $salt,
                'email' => Input::get('email'),
                'joined' => date('Y-m-d H:i:s'),
                'group' => 1,
                'firstname' => Input::get('Firstname'),
                'lastname' => Input::get('Lastname')
            ));
            Email::getInstance()->sendEmail(Input::get('email'), "New Registration", "new-account", array(
                'name' => Input::get('firstname')
            ));
            Redirect::to('profile.php');
        } catch (Exception $e) {
            die($e->getMessage());
        }
    } else {
        foreach($validation->errors() as $error) {
            echo $error."<br />";
        }
    }
}
?>
<form method="POST" action="">
    <label for="username">Username<br/></label>
    <input type="text" id="username" name="username" autocomplete="off" value="<?php echo escape(Input::get('username')); ?>"/><br/>
    <label for="password">Password<br/></label>
    <input id="password" autocomplete="off" name="password" type="password" /><br/>
    <label for="repeat_password">Repeat Password<br/></label>
    <input id="repeat_password" autocomplete="off" name="repeat_password" type="password" /><br/>
    <label for="email">Email<br/></label>
    <input id="email" autocomplete="off" name="email" type="email" value="<?php echo escape(Input::get('email')); ?>" /><br/>
    <label for="Firstname">Firstname<br/></label>
    <input id="Firstname" autocomplete="off" name="Firstname" type="Firstname" /><br/>
    <label for="Lastname">Lastname<br/></label>
    <input id="Lastname" autocomplete="off" name="Lastname" type="Lastname" /><br/>
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>"/><br/>
    <button type="submit">Submit</button>
</form>