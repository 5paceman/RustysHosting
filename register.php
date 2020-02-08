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
        ),
        'comms' => array(
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
                'group' => 2,
                'firstname' => Input::get('Firstname'),
                'lastname' => Input::get('Lastname'),
                'receive_emails' => (Input::get('comms') === 'on' ? 1 : 0)
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
<!DOCTYPE html>
<!--  Last Published: Sun Nov 17 2019 02:03:58 GMT+0000 (Coordinated Universal Time)  -->
<html data-wf-page="5dd07f30edfe6a37ec68c3c4" data-wf-site="5d8fb360124e070f85051b6c">
<head>
  <meta charset="utf-8">
  <title>Register</title>
  <meta content="Register" property="og:title">
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
  <style>
        meter {
    /* Reset the default appearance */
    -webkit-appearance: none;
        -moz-appearance: none;
            appearance: none;

    margin: 0 auto 1em;
    width: 100%;
    height: 0.5em;

    /* Applicable only to Firefox */
    background: none;
    background-color: rgba(0, 0, 0, 0.1);
    }

    meter::-webkit-meter-bar {
    background: none;
    background-color: rgba(0, 0, 0, 0.1);
    }

        /* Webkit based browsers */
    meter[value="1"]::-webkit-meter-optimum-value { background: red; }
    meter[value="2"]::-webkit-meter-optimum-value { background: yellow; }
    meter[value="3"]::-webkit-meter-optimum-value { background: orange; }
    meter[value="4"]::-webkit-meter-optimum-value { background: green; }

    /* Gecko based browsers */
    meter[value="1"]::-moz-meter-bar { background: red; }
    meter[value="2"]::-moz-meter-bar { background: yellow; }
    meter[value="3"]::-moz-meter-bar { background: orange; }
    meter[value="4"]::-moz-meter-bar { background: green; }
  </style>
</head>
<body class="body-2">
<div data-collapse="medium" data-animation="default" data-duration="400" class="navbar w-nav">
    <div class="container w-container"><a href="index.php" class="brand w-nav-brand"><img src="images/logo.png" alt="" class="image" width="48" height="48"><h2 class="heading-22">Rusty's Hosting</h2></a></div>
  </div>
  <div class="main">
    <div class="form-block-3 w-form">
      <form id="email-form" action="" method="post">
        <h3 class="heading-5">Register</h3>
        <label for="name">Username</label>
        <input type="text" class="text-field-3 w-input" name="username" id="username"autocomplete="off" value="<?php echo escape(Input::get('username')); ?>">
        <label for="password">Password</label>
        <input type="password" class="w-input" name="password" id="password">
        <meter max="4" id="password-strength-meter"></meter>
        <p id="password-strength-text"></p>
        <label for="repeat_password">Repeat Password</label>
        <input type="password" class="w-input" name="repeat_password" id="repeat_password">
        <label for="email">Email</label>
        <input type="text" class="text-field-3 w-input" name="email" id="email"autocomplete="on" value="<?php echo escape(Input::get('email')); ?>">
        <label class="w-checkbox checkbox-field">
        <div class="w-checkbox-input w-checkbox-input"></div>
        <input type="checkbox" id="comms" name="comms"><span for="receiveemails" class="w-form-label"><strong class="bold-text">Receive marketing/communication emails</strong></span></label>
        <label for="Firstname">First Name</label>
        <input type="text" class="text-field-3 w-input" name="Firstname" id="Firstname" autocomplete="on">
        <label for="Lastname">Last Name</label>
        <input type="text" class="text-field-3 w-input" name="Lastname" id="Lastname" autocomplete="on">
        <input type="hidden" id="token" name="token" value="<?php echo Token::generate(); ?>">
        <input type="submit" value="Register" class="submit-button-2 w-button"></form>
    </div>
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
  <script src="js/register.js"></script>
  <script src="js/webflow.js"></script>
</body>
</html>
