<?php
require_once 'core/init.php';

$user = new User();
if($user->isLoggedIn())
{
    Redirect::to('profile.php');
}

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {
        $validate = new Validate();
        $validation = $validate->check(array(
            'username' => array ('required' => true),
            'password' => array ('required' => true),
            'g-recaptcha-response' => array('required' => true)
        ));

        if($validation->passed()) {
            if(Recaptcha::verify(Input::get('g-recaptcha-response')))
            {
                $user = new User();
                $remember = (Input::get('remember') === 'on') ? true : false;
                $login = $user->login(Input::get('username'), Input::get('password'), $remember);

                if($login) {
                    Redirect::to('profile.php');
                } else {
                    echo 'failed';
                }
            } else {
                echo 'Recaptcha failed.';
            }
        } else {
            foreach($validation->errors() as $error) {
                echo $error.'<br />';
            }
        }
    }
}
?>
<!DOCTYPE html>
<!--  Last Published: Sun Nov 17 2019 02:03:58 GMT+0000 (Coordinated Universal Time)  -->
<html data-wf-page="5dd07f30edfe6a37ec68c3c4" data-wf-site="5d8fb360124e070f85051b6c">
<head>
  <meta charset="utf-8">
  <title>Login</title>
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
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <script>
       function onSubmit(token) {
         document.getElementById("email-form").submit();
       }
     </script>
</head>
<body class="body-2">
<div data-collapse="medium" data-animation="default" data-duration="400" class="navbar w-nav">
    <div class="container w-container"><a href="index.php" class="brand w-nav-brand"><img src="images/logo.png" alt="" class="image" width="48" height="48"><h2 class="heading-22">Rusty's Hosting</h2></a></div>
  </div>
  <div class="main">
    <div class="form-block-3 w-form">
      <form id="email-form" action="" method="post">
        <h3 class="heading-5">Login</h3>
        <label for="name">Username</label>
        <input type="text" class="text-field-3 w-input" name="username" id="username">
        <label for="password">Password</label>
        <input type="password" class="w-input" name="password" id="password">
        <label class="w-checkbox">
        <input type="checkbox" id="remember" name="remember" class="w-checkbox-input">
        <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>">
        <span class="w-form-label">Remember me?</span><span class="w-form-label"><a style="margin-left: 10px;" href="forgottenpassword.php">Forgotten Password?</a></span></label>
        <label><span class="w-form-label"><a href="register.php">Don't have an account?</a></span></label>
        <button value="Login" data-sitekey="<?php echo Config::get("recaptcha/site"); ?>" data-callback="onSubmit" class="g-recaptcha submit-button-2 w-button">Login</button>
        <br/>
        <?php
            if(Input::exists() && !$validation->passed())
            {
                foreach($validation->errors() as $error) {
                    echo $error."<br />";
                }
            }
        ?>
        </form>
    </div>
  </div>
</body>
</html>