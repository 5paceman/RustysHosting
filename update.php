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
                )
            ));

            if($validation->passed()) {
                try {
                    $user->update(array(
                        'email' => Input::get('email')
                    ));

                    Session::flash('home', 'Your details have been updated.');
                    Redirect::to('index.php');
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

<form action="" method="post">
    <label for="email">Email</label>
    <input type="text" name="email" value="<?php echo escape($user->data()->email); ?>">
    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>"/><br/>
    <input type="submit" value="Update">
</form>