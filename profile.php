<?php
require_once 'core/init.php';

$user = new User();
if(!$user->isLoggedIn())
{
  Redirect::to('login.php');
}

$token = Token::generate();
?>

    <!DOCTYPE html>
    <html data-wf-page="5dd1c386c5a7ed6b905e4a0b" data-wf-site="5dd179360336825db0f49358">

    <head>
          <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-158342655-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-158342655-1');
        </script>
        <meta charset="utf-8">
        <title>Profile</title>
        <meta content="Profile" property="og:title">
        <meta content="width=device-width, initial-scale=1" name="viewport">
        <meta content="Webflow" name="generator">
        <link href="css/normalize.css" rel="stylesheet" type="text/css">
        <link href="css/webflow.css" rel="stylesheet" type="text/css">
        <link href="css/profile.css" rel="stylesheet" type="text/css">
        <link href="images/favicon.png" rel="shortcut icon" type="image/x-icon">
        <link href="images/webclip.png" rel="apple-touch-icon">
        <link href="css/rustyshosting.webflow.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
        <script type="text/javascript">
            WebFont.load({
                google: {
                    families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic"]
                }
            });
        </script>
        <!-- [if lt IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js" type="text/javascript"></script><![endif] -->
        <script type="text/javascript">
            ! function(o, c) {
                var n = c.documentElement,
                    t = " w-mod-";
                n.className += t + "js", ("ontouchstart" in o || o.DocumentTouch && c instanceof DocumentTouch) && (n.className += t + "touch")
            }(window, document);
        </script>
        <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">
        <link href="images/webclip.png" rel="apple-touch-icon">
    </head>

    <body class="body-3">
        <div class="top-bar">
            <a href="index.php" class="link-block w-inline-block"><img src="images/logo.png" height="48" width="48" alt="" class="image">
                <h2 class="heading-12">Rusty&#x27;s Hosting</h2></a>
            <div data-delay="0" class="logout w-dropdown">
                <div class="dropdown-toggle w-dropdown-toggle">
                    <div class="text-block-5"><span class="list-icon"></span>
                        <?php echo $user->data()->email; ?> <span class="list-icon"></span></div>
                </div>
                <nav class="dropdown-list w-dropdown-list"><a href="logout.php" class="dropdown-link w-dropdown-link"><span class="list-icon"></span>Logout</a></nav>
            </div>
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
                    <div class="info-form w-form">
                        <h4 class="heading-13">Info</h4>
                        <form id="account-settings-form" method="POST" action="update.php" name="account-settings" data-name="">
                            <label for="Firstname">First Name</label>
                            <input type="text" class="settings-text-input w-input" value="<?php echo $user->data()->firstname; ?>" maxlength="256" name="firstname" data-name="Firstname" id="Firstname">
                            <label for="lastname">Last Name</label>
                            <input type="text" maxlength="256" name="lastname" value="<?php echo $user->data()->lastname; ?>"  data-name="lastname" id="lastname" class="settings-text-input w-input">
                            <label for="email">Email</label>
                            <input type="email" maxlength="256" name="email" value="<?php echo $user->data()->email; ?>" data-name="email" id="email" class="settings-text-input w-input">
                            <label class="w-checkbox checkbox-field">
                                <div class="w-checkbox-input w-checkbox-input--inputType-custom checkbox <?php echo (($user->data()->receive_emails == 1) ? "w--redirected-checked" : ""); ?>"></div>
                                <input type="checkbox" id="receiveemails" name="receiveemails" <?php echo (($user->data()->receive_emails == 1) ? "checked" : ""); ?> data-name="receiveemails" style="opacity:0;position:absolute;z-index:-1"><span for="receiveemails" class="w-form-label"><strong class="bold-text-2"> Marketing/Communication Emails</strong></span></label>
                            <input type="submit" value="Update" class="submit-button-3 w-button">
                        </form>
                    </div>
                    <div class="password-form">
                        <h4 class="heading-13">Update Password</h4>
                        <form id="update-password" method="POST" action="updatepassword.php" name="update-password" data-name="update-password">
                            <label for="current-password">Current Password</label>
                            <input type="password" class="settings-text-input w-input" maxlength="256" name="current-password" data-name="current-password" id="current-password">
                            <label for="new-password">New Password</label>
                            <input type="password" maxlength="256" name="new-password" data-name="new-password" id="new-password" class="settings-text-input w-input">
                            <label for="repeat-password">Repeat Password</label>
                            <input type="password" maxlength="256" name="repeat-password" data-name="repeat-password" id="repeat-password" class="settings-text-input w-input">
                            <input type="submit" value="Update" data-wait="Please wait..." class="submit-button-3 w-button">
                        </form>
                    </div>
                </div>
                <div class="services">
                    <h3 class="heading-10">Servers</h3>
                    <div class="form-block-5 w-form">
                        <form action="purchase.php" method="POST" class="form-2">
                            <h3 class="heading-17">New Server</h3>
                            <label for="regionId">Region</label>
                            <input type="hidden" name="planId" value="1" />
                            <input type="hidden" name="token" value="<?php echo $token ?>" />
                            <select id="regionId" name="regionId" data-name="region" class="w-select">
                                <option value="1">West Europe</option>
                            </select>
                            <select id="planId" name="planId" data-name="plan" class="w-select">
                                <option value="1">14$ Wood - 20-30 Slots</option>
                                <option value="2">18$ Stone - 30-50 Slots</option>
                                <option value="3">24$ Metal - 50-100 Slots</option>
                                <option value="4">38$ C4 - 100+ Slots</option>
                            </select>
                            <input type="submit" value="Buy" class="submit-button-8 w-button">
                        </form>
                    </div>
                    <?php
          if(!$user->data()->free_trial_offer)
          {
            ?>
                        <div class="free-trial">
                            <h1 class="free-trial-excl">!</h1>
                            <div class="free-trial-text">Start your free 24 hour trial, no credit/debit card details required!</div>
                            <div class="free-trial-form w-form">
                                <form id="free-trial" action="freetrial.php" method="POST" class="free-trial-form-content">
                                    <select id="region" name="region" data-name="region" class="ft-form-select w-select">
                                        <option value="1">West Europe</option>
                                    </select>
                                    <input type="submit" value="Start Trial" data-wait="Please wait..." class="fr-form-submit w-button">
                                </form>
                            </div>
                            <?php
          }
        ?>
                        </div>
                        <?php
          $service = new Service();
          $services = $service->findAll($user->data()->id);
          foreach($services as $user_service) {
              if(strtotime($user_service->expiry()) < strtotime("+24 hour") && strtotime($user_service->expiry()) > strtotime("now"))
              {
                $expires = strtotime($user_service->expiry());
                  ?>
                            <div class="popup">
                                <h1 class="popup-icon">!</h1>
                                <div class="popup-text">Your service <b><?php echo $user_service->data()->service_id; ?></b> expires in less than 24 hours!</div>
                                <div class="popup-bold-text" data-time="<?php echo date("c",$expires); ?>">24h 22m 13s</div>
                            </div>
                            <?php
              } 
          }
        ?>
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
    foreach($services as $user_service) {
        ?>
                                        <tr>
                                            <td><a style="color: black; text-decoration: none;" href="manage.php?serviceId=<?php echo $user_service->data()->service_id; ?>"><span class="list-icon"> </span><?php echo $user_service->data()->service_id;?></a></td>
                                            <td>
                                                <?php echo $user_service->ip(); ?>
                                            </td>
                                            <td>
                                                <?php echo $user_service->port(); ?>
                                            </td>
                                            <td>
                                                <?php echo $user_service->plan(); ?>
                                            </td>
                                            <?php
            $time = strtotime($user_service->expiry());

            if($time < time()) {
                echo '<td>Expired</td><td><form action="purchase.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="planId" value="'.$user_service->data()->plan_id.'"/><input type="hidden" name="regionId" value="1"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Pay"><br/></form></td>';
            } elseif($time > time()) {
                echo '<td>'.date("jS F Y h:ia", $time).'</td>'.'<td><form action="updatesub.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Update"><br/></form><form action="changeplan.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Change Plan"><br/></form></form><form action="deletesub.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><input style="margin-left: 5px;" class="service-buttons submit-button-4 w-button" type="submit" value="Cancel"><br/></form></td>';
            }
            ?>
                                        </tr>
                                        <?php
    }
    ?>
                                </table>
                </div>
                <div class="faq" style="height: 100%;">
                    <h3 class="heading-9">FAQ</h3>
                    <div class="w-richtext" style="height: 80%; overflow: scroll;">
                        <h4>How do I manage my server?</h4>
                        <p>Click the Cog(gear) icon next to the left of the server's Service ID.</p>
                        <h4>How do I FTP(File Transfer Protocol)?</h4>
                        <p>Download a FTP client (for example FileZilla) and use the server's IP and Service username and password as the connection details.</p>
                        <h4>How can I run commands on the server?</h4>
                        <p>Use the "Console" tab on the server manage page. Select "Connect" and then type the command in the box and select "Send". For a list of all possible server commands look here: <a href="https://rust.fandom.com/wiki/Server_Commands">Server Commands.</a></p>
                        <h4>How do "Backups" work?</h4>
                        <p>Backups are taken every hour and there is a maximum of 10GB of storage (per server). Backups are stored on an external cloud server, meaning the server is always safely backed up. </p>
                        <h4>How do I restart/stop/start the server?</h4>
                        <p>On the "Information" tab there are three buttons marked with the Play(<span style="font-family: 'Fa solid 900', sans-serif;"></span>), Stop(<span style="font-family: 'Fa solid 900', sans-serif;"></span>) and Restart(<span style="font-family: 'Fa solid 900', sans-serif;"></span>) icons. Click these accordingly.</p>
                        <h4>How can I install RustIO?</h4>
                        <p>On the "Tools" tab select the "Install" RustIO button. This will automatically install RustIO. After 10 minutes, restart the server using the Restart(<span style="font-family: 'Fa solid 900', sans-serif;"></span>) icon and RustIO should be installed.</p>
                        <h4>How can I update my server?</h4>
                        <p>On the "Tools" tab select "Update Rust". It will always download the latest version of the Rust server. This will also consequently remove Oxide.</p>
                        <h4>How can I install Oxide plugins?</h4>
                        <p>Connect to the server's FTP and place the plugins in the "Plugins" folder.</p>
                        <h4>How do I install Oxide?</h4>
                        <p>On the service management page under the "Tools" tab is the button to install Oxide.</p>
                        <h4>How do I uninstall Oxide?</h4>
                        <p>On the tools tab, click to Update Rust. This will update Rust in which doing so will uninstall Oxide.</p>
                        <h4>How do I purchase a server?</h4>
                        <p>On the "Servers" tab is "New Servers" box. Select a region and hit "Buy". You will be taken to the payment provider.The payment provider will request for payment details and upon completion the server will be created.</p>
                        <h4>How do I cancel a subscription?</h4>
                        <p>On the "Servers" tab, find the server that you wish to cancel the subscription from and select "Cancel". This will automatically stop billing at the end of the current expiry.</p>
                        <h4>Why does my server show it expires in a month?</h4>
                        <p>The expiry is the date and time of your next bill for each server. Unless the subscription has been canceled, you will automatically be billed at the end of the expiry period. It will then update for another 30 days.</p>
                        <h4>How do I report an issue with the server/website?</h4>
                        <p>On your profile choose "Support" on the left, fill in the support ticket form with the correct ServiceID and Priority. Alternatively send an email to <a href="mailto:support@rustyshosting.io">support@rustyshosting.io</a>
                    </div>
                </div>
                <div class="support">
                    <h3 class="heading-10">Support</h3>
                    <div class="w-richtext">
                        <h2>Logging a support request</h2>
                        <p>‍</p>
                        <p>Once a ticket has been raised all communication will be via email, please make sure you have an up-to-date email address before raising a ticket.</p>
                        <p>Please ensure you read through the FAQ before submitting a support request. Please also <b>thoroughly</b> read through the below before raising a ticket, its important to pick the correct urgency, repeated failure to pick the correct urgency will mean all future requests will automatically be moved to low urgency regardless of impact to your service.</p>
                        <h4>Urgency</h4>
                        <ul>
                            <li><strong>High - </strong>24 hour response time, for support requests that need fixing ASAP such as unable to FTP or turn your server on</li>
                            <li><strong>Medium - </strong>For requests that need looking at promptly but aren&#x27;t detrimental to service</li>
                            <li><strong>Low - </strong>For low level requests such as queries relating to basic control panel features</li>
                        </ul>
                    </div>
                    <div class="form-block-4">
                        <form id="support-form" action="support.php" method="POST">
                            <label for="serviceid">Service ID</label>
                            <select id="serviceid" name="serviceid" data-name="serviceid" required="" class="settings-text-input w-select">
                                <?php
                foreach($services as $service)
                {
                  echo '<option value="'.$service->data()->service_id.'">'.$service->data()->service_id.'</option>';
                }
              ?>
                            </select>
                            <label for="serviceid-2">Urgency</label>
                            <select id="urgency" name="urgency" data-name="urgency" required="" class="settings-text-input w-select">
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                            <label for="description">Description</label>
                            <textarea data-name="description" maxlength="5000" id="description" name="description" required="" class="w-input"></textarea>
                            <input type="submit" value="Submit" class="submit-button-4 w-button">
                        </form>
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
