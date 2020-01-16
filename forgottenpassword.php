<?php
require_once 'core/init.php';
require_once 'functions/stringtools.php';

$reset = false;

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check(array(
            'username' => array ('required' => true),
        ));

        if($validation->passed())
        {
            $result = DB::getInstance()->get('users', array('username', '=', Input::get('username')));
            if($result->count())
            {
                resetPassword($result->first()->id);
            } else  {
                $result = DB::getInstance()->get('users', array('email', '=', Input::get('username')));
                if($result->count())
                {
                    resetPassword($result->first()->id);
                }
            }
        }
    }
}

function resetPassword($id)
{
    $user = new User($id);
    $Password = generateRandomString(10, true);
    $newSalt = Hash::salt(32);
    $newPassword = Hash::make($Password, $newSalt);
    $user->update(array(
        'password' => $newPassword,
        'salt' => $newSalt
    ), $id);
    $variables = array(
        'name' => $user->data()->firstname,
        'password' => $Password
    );
    Email::getInstance()->sendEmail($user->data()->email, "Reset Password", "reset-password", $variables);
    $reset = true;
}

?>

<!DOCTYPE html>
<html data-wf-page="5dd07f30edfe6a37ec68c3c4" data-wf-site="5d8fb360124e070f85051b6c">
<head>
  <meta charset="utf-8">
  <title>Forgotten Password</title>
  <meta content="Login" property="og:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/rustyshosting.webflow.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic","Work Sans:regular"]  }});</script>
  <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
</head>
<body class="body-2">
  <div class="main">
    <div class="form-block-3 w-form">
        <?php 
            if(!$reset)
            {

            
        ?>
      <form id="email-form" action="" method="post">
        <h3 class="heading-5">Forgotten Password</h3>
        <label for="name">Username or Email</label>
        <input type="text" class="text-field-3 w-input" name="username" id="username">
        <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" value="Reset Password" class="submit-button-2 w-button"></form>
        <?php 
            } else {
        ?>
        <p>Your password has been reset, you should receive an email shortly.</p>
        <?php 
            }
        ?>
    </div>
  </div>
</body>
</html>