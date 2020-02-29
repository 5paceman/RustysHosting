<?php

require_once 'core/init.php';
require_once 'functions/sanitize.php';

$user = new User();
$service = null;
if($user->isLoggedIn())
{
  if(Input::exists('get'))
  {
    $serviceId = Input::get("serviceId");
    if(isset($serviceId))
    {
      $isAdmin = $user->isAdmin();
      $result = DB::getInstance()->get('services', array('service_id', '=', $serviceId));
      if($result->count())
      {
        $user_id = $result->first()->user_id;
        if(!$isAdmin && $user_id !== $user->data()->id)
        {
          Redirect::to('profile.php');
        } else {
          $service = new Service(null, $result->first());
          if(!$service->isValid())
          {
            Redirect::to('profile.php');
          }
        }
      } else {
        Redirect::to('profile.php');
      }
    } else {
      Redirect::to('profile.php');
    }
  } else {
    Redirect::to('profile.php');
  }
} else {
  Redirect::to('login.php');
}
?>

<!DOCTYPE html>
<html data-wf-page="5dd67627501842721c02381e" data-wf-site="5dd179360336825db0f49358">

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
    <title>Manage Service</title>
    <meta content="Manage Service" property="og:title">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="Webflow" name="generator">
    <link href="css/normalize.css" rel="stylesheet" type="text/css">
    <link href="css/webflow.css" rel="stylesheet" type="text/css">
    
    <link href="css/rustyshosting.webflow.css" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
    <script type="text/javascript">
        WebFont.load({
            google: {
                families: ["Open Sans:300,300italic,400,400italic,600,600italic,700,700italic,800,800italic"]
            }
        });
    </script>
    <script type="text/javascript">
        var serverIp = "<?php echo $service->ip(); ?>";
        var serverID = "<?php echo $service->data()->service_id; ?>";
        var serverPort = <?php echo ($service->port() + 1); ?>;
        var serverPassword = "<?php echo $service->data()->service_password; ?>";
        var wsString = "http://" + serverIp + ":" + serverPort + "/" + serverPassword;
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

<body class="body-4">
    <div class="top-bar"><a href="index.php" class="link-block w-inline-block"><img src="images/logo.png" height="48" width="48" alt="" class="image"><h2 class="heading-12">Rusty&#x27;s Hosting</h2></a>
        <div class="text-block-5"><span class="list-icon"></span><?php echo $user->data()->email; ?></div>
    </div>
    <div class="profile-main">
        <div class="profile-sidebar">
            <ul class="list-2">
                <a style="color: rgb(238, 225, 225); text-decoration: none;" href="profile.php#accountsettings"><li data-w-id="61614472-444d-4a12-5406-bcf3d425552e" class="profile-li"><span class="list-icon"></span> Account Settings</li></a>
                <a style="color: rgb(238, 225, 225); text-decoration: none;" href="profile.php#servers"><li data-w-id="61614472-444d-4a12-5406-bcf3d4255532" class="profile-li"><span class="text-span-3 list-icon"> </span>Servers</li></a>
                <a style="color: rgb(238, 225, 225); text-decoration: none;" href="profile.php#support"><li data-w-id="61614472-444d-4a12-5406-bcf3d4255536" class="profile-li"><span class="text-span-4"></span> <span class="text-span-5">Support</span></li></a>
                <a style="color: rgb(238, 225, 225); text-decoration: none;" href="profile.php#faq"><li data-w-id="61614472-444d-4a12-5406-bcf3d425553c" class="profile-li"><span class="text-span-4 list-icon"></span> <span class="text-span-5">FAQ</span></li></a>
            </ul>
        </div>
        <div class="profile-content">
            <div data-duration-in="300" data-duration-out="100" class="w-tabs">
                <div class="w-tab-menu">
                    <a data-w-tab="Tab 1" class="tab-link-tab-1 tab-button w-inline-block w-tab-link w--current">
                        <div>Information</div>
                    </a>
                    <a data-w-tab="Tab 4" class="tab-button w-inline-block w-tab-link">
                        <div>Config</div>
                    </a>
                    <a data-w-tab="Tab 2" class="tab-button w-inline-block w-tab-link">
                        <div>Console</div>
                    </a>
                    <a data-w-tab="Tab 5" class="tab-button w-inline-block w-tab-link">
                        <div>Backups</div>
                     </a>
                    <a data-w-tab="Tab 3" class="tab-button w-inline-block w-tab-link">
                        <div>Tools</div>
                    </a>
                </div>
                <div class="w-tab-content">
                    <div data-w-tab="Tab 1" class="tab-pane-tab-1 w-tab-pane w--tab-active">
                        <div class="w-richtext">
                            <h5>Information:</h5>
                            <p><b>Service ID &amp; FTP Username:</b>
                                <?php echo $service->data()->service_id; ?>
                            </p>
                            <p><b>FTP &amp; Rcon Password:</b>
                                <?php echo escape($service->data()->service_password); ?>
                            </p>
                            <p><b>IP Address:</b>
                                <?php echo $service->ip(); ?>
                            </p>
                            <p><b>Port:</b>
                                <?php echo $service->port(); ?>
                            </p>
                            <p><b>Status:</b> <span id="serverStatus"><span/></p>
                        </div>
                        <div class="server-button-group">
                            <a data-command="start" data-w-id="5e7c759d-ffc8-5d5a-060e-18934b0754a0" style="background-color:rgb(205,65,43)" href="#" class="server-command server-buttons-left w-button"></a><a data-command="stop" data-w-id="6b6ecd9a-f155-eafe-39bb-15fd494f3466" style="background-color:rgb(205,65,43)" href="#" class="server-command server-buttons-middle w-button"></a><a data-command="restart" data-w-id="e771a266-a598-7038-e480-6b8a05788d88" style="background-color:rgb(205,65,43)" href="#" class="server-command server-buttons-right w-button"></a>
                        </div>
                        <div><strong>Server Log:</strong></div>
                        <div class="server-logs"><p style="white-space: pre-wrap;" id="server-logs"></p></div>
                    </div>
                    <div data-w-tab="Tab 4" class="tab-pane-tab-4 w-tab-pane">
                      <div class="w-form"><form id="config-form" name="email-form-3" action="update-config.php" data-name="Email Form 3">
                          <label for="hostname">Hostname</label>
                          <input type="text" class="config-input w-input" name="hostname" value="<?php echo escape($service->config()->hostname); ?>" data-name="hostname" id="hostname">
                          <label for="hostname-2">Server Description</label>
                          <textarea data-name="description" maxlength="512" id="description" name="description" class="textarea-2 w-input"><?php echo escape($service->config()->description); ?></textarea>
                          <label for="maxplayers">Max Players</label>
                          <input type="number" class="text-field-5 w-input"  name="maxplayers" value="<?php echo $service->config()->max_players; ?>" data-name="maxplayers" id="maxplayers" required="">
                          <label for="worldsize">World Size</label>
                          <input type="number" class="text-field-5 w-input" name="worldsize" value="<?php echo $service->config()->world_size; ?>" data-name="worldsize" id="worldsize" required="">
                          <label for="seed">Seed</label>
                          <input type="number" class="text-field-5 w-input"  name="seed" value="<?php echo $service->config()->seed; ?>" data-name="seed" id="seed" required="">
                          <label for="tickrate">Tick Rate</label>
                          <input type="number" class="text-field-5 w-input" name="tickrate" value="<?php echo $service->config()->tick_rate; ?>" data-name="tickrate" id="tickrate" required="">
                          <input type="hidden" value="<?php echo $service->data()->service_id; ?>" id="service_id" name="service_id">
                          <label class="w-checkbox checkbox-field">
                        <div class="w-checkbox-input w-checkbox-input--inputType-custom checkbox <?php echo ($service->config()->global_chat === "1" ? "w--redirected-checked" : ""); ?>"></div>
                        <input type="checkbox" id="globalchat" name="globalchat" data-name="globalchat" <?php echo ($service->config()->global_chat === "1" ? "checked" : "");  ?> style="opacity:0;position:absolute;z-index:-1"><span for="globalchat" class="w-form-label"><strong class="bold-text">Global Chat</strong></span></label>
                          <label for="headerimage">Header Image URL</label>
                          <input type="text" class="config-input w-input" maxlength="256" value="<?php echo $service->config()->header_image; ?>" name="headerimage" data-name="headerimage" id="headerimage">
                          <label for="service-password">Service Password</label>
                          <input type="text" class="config-input w-input" value="<?php echo $service->data()->service_password; ?>" id="service-password" name="service-password">
                          <input type="submit" value="Update" class="submit-button-7 w-button">
                      </form></div>
                    </div>
                    <div data-w-tab="Tab 2" class="tab-pane-tab-2 w-tab-pane">
                        <label id="status" for="console">Status: disconnected</label>
                        <textarea name="console" maxlength="5000" id="console" data-name="console" class="textarea w-input"></textarea>
                        <input type="text" class="text-field-4 w-input" maxlength="256" name="command" data-name="command" id="command">
                        <input type="submit" onclick="connect();" value="Connect" class="submit-button-6 w-button">
                        <input type="submit" onclick="send();" value="Send" class="submit-button-6 w-button">
                    </div>
                    <div data-w-tab="Tab 3" class="tab-pane-tab-3 w-tab-pane">
                        <div class="div-block-2">
                            <div class="grid-cell">
                                <div class="w-form">
                                    <form class="form">
                                        <label for="name" class="field-label">Install RustIO</label>
                                        <input type="submit" value="Install" data-wait="Please wait..." data-command="installrustio" class="server-command submit-button-5 w-button">
                                    </form>
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="w-form">
                                    <form class="form">
                                        <label for="name" class="field-label">Update Rust</label>
                                        <input type="submit" value="Install" data-wait="Please wait..." data-command="update" class="server-command submit-button-5 w-button">
                                        <label class="field-label">Please note this will immediately shutdown your server.</label>
                                    </form>
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="w-form">
                                    <form class="form">
                                        <label for="name" class="field-label">Install/Update Oxide</label>
                                        <input type="submit" value="Install" data-wait="Please wait..." data-command="oxide" class="server-command submit-button-5 w-button">
                                        <label class="field-label">Please note this will immediately shutdown your server.</label>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div data-w-tab="Tab 5" class="tab-pane-tab-5 w-tab-pane">
                        <p><b>Notice:</b> Please note that restoring a backup will immediately stop your server.</p>
                        <table class="serviceTable">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Restore</th>
                                    <th scope="col">Download</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <?php

                            function formatBytes($size, $precision = 1)
                            {
                                $base = log($size, 1024);
                                $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   
                            
                                return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
                            }
                                $size = 0;
                                $backups = DB::getInstance()->get('backups', array("service", "=", $service->id()));
                                if($backups->count())
                                {
                                    
                                    foreach($backups->results() as $result)
                                    {
                                        $size += $result->size;
                                        ?>
                                        <tr>
                                            <td><?php echo $result->date; ?></td>
                                            <td><?php echo $result->path; ?></td>
                                            <td><form id="restorebackup" action="servicecommand.php" method="POST"><input type="hidden" name="service_id" value="<?php echo $result->service; ?>"><input type="hidden" name="backupID" value="<?php echo $result->id; ?>"><input type="hidden" name="command" value="restore"><input style="margin-left: 5px;" class="text-span-3 service-buttons submit-button-4 w-button" type="submit" value=""><br/></form></td>
                                            <td><form action="download.php" method="POST"><input type="hidden" name="service_id" value="<?php echo $result->service; ?>"><input type="hidden" name="backupID" value="<?php echo $result->id; ?>"><input style="margin-left: 5px;" class="text-span-3 service-buttons submit-button-4 w-button" type="submit" value=""><br/></form></td>
                                            <td><form id="deletebackup" action="servicecommand.php" method="POST"><input type="hidden" name="service_id" value="<?php echo $result->service; ?>"><input type="hidden" name="backupID" value="<?php echo $result->id; ?>"><input type="hidden" name="command" value="deletebackup"><input style="margin-left: 5px;" class="text-span-3 service-buttons submit-button-4 w-button" type="submit" value=""><br/></form></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                $planId = $service->data()->plan_id;
                                $max = DB::getInstance()->get('plans', array('id', '=', $planId))->first()->backup_size;
                                $percentage = ceil(($size / $max) *  100);
                            ?>
                        </table>
                        <p style="text-align: center; margin-top: 10px;">Backup Usage: </p>
                        <div id="progress-bar" class="all-rounded">
                            <div id="progress-bar-percentage" class="all-rounded" style="width: <?php echo $percentage; ?>%"><span><?php echo (($size == 0) ? 0 : formatBytes($size)).'/'.formatBytes($max); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.4.1.min.220afd743d.js" type="text/javascript" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="js/webflow.js" type="text/javascript"></script>
    <script src="js/manage.js" type="text/javascript"></script>
    <script src="js/fancyTable.min.js" type="text/javascript"></script>
    <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
</body>

</html>