<?php
require_once 'core/init.php';

$user = new User();
if(!$user->isLoggedIn())
{
  Redirect::to('login.php');
}
?>

<!DOCTYPE html>
<html data-wf-page="5dd1c386c5a7ed6b905e4a0b" data-wf-site="5dd179360336825db0f49358">
<head>
  <meta charset="utf-8">
  <title>Profile</title>
  <meta content="Profile" property="og:title">
  <meta content="width=device-width, initial-scale=1" name="viewport">
  <meta content="Webflow" name="generator">
  <link href="css/normalize.css" rel="stylesheet" type="text/css">
  <link href="css/webflow.css" rel="stylesheet" type="text/css">
  <link href="css/profile.css" rel="stylesheet" type="text/css">
  <link href="css/rustyshosting.webflow.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">WebFont.load({  google: {    families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic"]  }});</script>
  <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
  <script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
  <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">
  <link href="images/webclip.png" rel="apple-touch-icon">
</head>
<body class="body-3">
  <div class="top-bar"><a href="index.php" class="link-block w-inline-block"><img src="images/logo.png" height="48" width="48" alt="" class="image"><h2 class="heading-12">Rusty&#x27;s Hosting</h2></a>
    <div class="text-block-5"><span class="list-icon"></span><?php echo $user->data()->email; ?></div>
  </div>
  <div class="profile-main">
    <div class="profile-sidebar">
      <ul class="list-2">
        <li id="acc-settings-btn" data-w-id="8d79276c-960b-56b8-5a82-91417ae109c8" class="profile-li"><span class="list-icon"></span> Account Settings</li>
        <li id="servers-btn" data-w-id="8d79276c-960b-56b8-5a82-91417ae109c9" class="profile-li"><span class="text-span-3 list-icon"> </span>Servers</li>
        <li id="support-btn" data-w-id="7f0d0736-e6ad-6de3-dcf2-85357cd59524" class="profile-li"><span class="text-span-4"></span> <span class="text-span-5">Support</span></li>
        <li id="faq-btn" data-w-id="8d79276c-960b-56b8-5a82-91417ae109ca" class="profile-li"><span class="text-span-4 list-icon"></span> <span class="text-span-5">FAQ</span></li>
      </ul>
    </div>
    <div class="profile-content">
      <div class="account-settings">
        <h3 class="heading-9">Account Settings</h3>
        <div class="info-form">
          <h4 class="heading-13">Info</h4>
          <form id="account-settings" method="POST" action="update.php" name="account-settings" data-name="account-settings"><label for="Firstname">First Name</label><input type="text" class="settings-text-input w-input" maxlength="256" name="Firstname" data-name="Firstname" id="Firstname"><label for="lastname">Last Name</label><input type="text" maxlength="256" name="lastname" data-name="lastname" id="lastname" class="settings-text-input w-input"><label for="email">Email</label><input type="email" maxlength="256" name="email" data-name="email" id="email" class="settings-text-input w-input"><input type="submit" value="Update" data-wait="Please wait..." class="submit-button-3 w-button"></form>
        </div>
        <div class="password-form">
          <h4 class="heading-13">Update Password</h4>
          <form id="update-password" method="POST" action="updatepassword.php" name="update-password" data-name="update-password"><label for="current-password">Current Password</label><input type="password" class="settings-text-input w-input" maxlength="256" name="current-password" data-name="current-password" id="current-password"><label for="new-password">New Password</label><input type="password" maxlength="256" name="new-password" data-name="new-password" id="new-password" class="settings-text-input w-input"><label for="repeat-password">Repeat Password</label><input type="password" maxlength="256" name="repeat-password" data-name="repeat-password" id="repeat-password" class="settings-text-input w-input"><input type="submit" value="Update" data-wait="Please wait..." class="submit-button-3 w-button"></form>
        </div>
      </div>
      <div class="services">
        <h3 class="heading-10">Servers</h3>
        <table class="serviceTable">
        <thead>
            <tr>
                <th scope="col">Service ID</th>
                <th scope="col">IP</th>
                <th scope="col">Port</th>
                <th scope="col">Plan</th>
                <th scope="col">Expiry</th>
                <th scope="col">Billing</th>
            </tr>
        </thead>
    <?php
    $service = new Service();
    $services = $service->findAll($user->data()->id);
    $token = Token::generate();
    foreach($services as $user_service) {
        ?>
        <tr>
            <td><a style="color: black; text-decoration: none;" href="manage.php?serviceId=<?php echo $user_service->data()->service_id; ?>"><span class="list-icon"> </span><?php echo $user_service->data()->service_id;?></a></td>
            <td><?php echo $user_service->ip(); ?></td>
            <td><?php echo $user_service->port(); ?></td>
            <td><?php echo $user_service->plan(); ?></td>
            <?php
            $time = strtotime($user_service->expiry());

            echo '<td>'.date("jS F Y h:ia", $time).'</td>';

            if($time < time()) {
                echo '<td><form action="purchase.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="planId" value="'.$user_service->data()->plan_id.'"/><input type="hidden" name="regionId" value="1"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Pay"><br/></form></td>';
            } elseif($time > time()) {
                echo '<td><form action="updatesub.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Update"><br/></form><form action="deletesub.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Cancel"><br/></form></td>';
            }
            ?>
        </tr>
        <?php
    }
    ?>
    </table> 
      </div>
      <div class="faq">
        <h3 class="heading-9">FAQ</h3>
        <div class="w-richtext">
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>How do I FTP?</h4>
          <p>Using a FTP client such as FileZilla use the server IP and FTP logins details provided on your servers information page, these will also be emailed to you when a game server is created</p>
          <h4>‍</h4>
        </div>
      </div>
      <div class="support">
        <h3 class="heading-10">Support</h3>
        <div class="w-richtext">
          <h2>Logging a support request</h2>
          <p>‍</p>
          <p>Once a ticket has been raised all communication will be via email, please make sure you have an up to date email address before raising a ticket.</p>
          <p>Please ensure you read through the FAQ before submitting a support request. Please also thoroughly read through the below before raising a ticket, its important to pick the correct urgency, repeated failure to pick the correct urgency will mean all future requests will automatically be moved to low urgency regardless of impact to your service.</p>
          <h4>Urgency</h4>
          <ul>
            <li><strong>High - </strong>24 hour response time, for support requests that need fixing ASAP such as unable to FTP or turn your server on</li>
            <li><strong>Medium - </strong>For requests that need looking at promptly but aren&#x27;t detrimental to service</li>
            <li><strong>Low - </strong>For low level requests such as queries relating to plugins/server configuration</li>
          </ul>
        </div>
        <div class="form-block-4 w-form">
          <form id="email-form-2" name="email-form-2" data-name="Email Form 2"><label for="serviceid">Service ID</label><input type="text" class="settings-text-input w-input" maxlength="256" name="serviceid" data-name="serviceid" id="serviceid" required=""><label for="serviceid-2">Urgency</label><select id="urgency" name="urgency" data-name="urgency" required="" class="w-select"><option value="Second">Low</option><option value="First">Medium</option><option value="">High</option></select><label for="description">Description</label><textarea data-name="description" maxlength="5000" id="description" name="description" required="" class="w-input"></textarea><input type="submit" value="Submit" data-wait="Please wait..." class="submit-button-4 w-button"></form>
          <div class="w-form-done">
            <div>Thank you! Your submission has been received!</div>
          </div>
          <div class="w-form-fail">
            <div>Oops! Something went wrong while submitting the form.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.4.1.min.220afd743d.js" type="text/javascript" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
  <script src="js/webflow.js" type="text/javascript"></script>
  <script src="js/profile.js" type="text/javascript"></script>
  <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
</body>
</html>