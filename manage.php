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
      $result = DB::getInstance()->get('services', array('service_id', '=', $serviceId));
      if($result->count())
      {
        $user_id = $result->first()->user_id;
        if($user_id !== $user->data()->id)
        {
          Redirect::to('profile.php');
        } else {
          $service = new Service(null, $result->first());
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
    <script>
            var websocket = null;

            function connect() {
                websocket = new WebSocket("ws://<?php echo $service->ip().":".($service->port() + 1)."/".$service->data()->service_password; ?>");
                websocket.onmessage = onMessage;
            }

            function onMessage(event) {
                var textbox = document.getElementById("console");
                console.log(event.data);
                var data = JSON.parse(event.data);
                textbox.value += data.Message;
            }

            function send()
            {
                var message = document.getElementById("command").value;
                document.getElementById("command").value = "";
                var packet = {
                    Identifier: 1,
                    Message: message,
                    Name: "WebRcon"
                };
                websocket.send(JSON.stringify(packet));
            }


            function checkConnection() {
                if(websocket != null)
                {
                    if(websocket.readyState  == WebSocket.OPEN)
                    {
                        document.getElementById("status").innerHTML = "Status: connected";
                    } else if(websocket.readyState == WebSocket.CLOSED) {
                        document.getElementById("status").innerHTML = "Status: disconnected";
                    } else if (websocket.readyState == WebSocket.CONNECTING) {
                        document.getElementById("status").innerHTML = "Status: connecting";
                    }
                }
            }
            
            setInterval(checkConnection, 3000);
        </script>
        <script src="js/manage.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" type="text/javascript"></script>
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
    <div class="profile-main">
        <div class="profile-sidebar">
            <h1 class="heading-11"></h1>
            <h3 class="heading-8">Test</h3>
            <ul class="list-2">
                <li data-w-id="61614472-444d-4a12-5406-bcf3d425552e" class="profile-li"><span class="list-icon"></span> Account Settings</li>
                <li data-w-id="61614472-444d-4a12-5406-bcf3d4255532" class="profile-li"><span class="text-span-3 list-icon"> </span>Servers</li>
                <li data-w-id="61614472-444d-4a12-5406-bcf3d4255536" class="profile-li"><span class="text-span-4"></span> <span class="text-span-5">Support</span></li>
                <li data-w-id="61614472-444d-4a12-5406-bcf3d425553c" class="profile-li"><span class="text-span-4 list-icon"></span> <span class="text-span-5">FAQ</span></li>
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
                    <a data-w-tab="Tab 3" class="tab-button w-inline-block w-tab-link">
                        <div>Tools</div>
                    </a>
                </div>
                <div class="w-tab-content">
                    <div data-w-tab="Tab 1" class="tab-pane-tab-1 w-tab-pane w--tab-active">
                        <div class="w-richtext">
                            <h5>Information:</h5>
                            <p>Service ID &amp; FTP Username:
                                <?php echo $service->data()->service_id; ?>
                            </p>
                            <p>FTP &amp; Rcon Password:
                                <?php echo escape($service->data()->service_password); ?>
                            </p>
                            <p>IP Address:
                                <?php echo $service->ip(); ?>
                            </p>
                            <p>Status: </p>
                            <p>Port:
                                <?php echo $service->port(); ?>
                            </p>
                            <p><strong>Server Log</strong>:</p>
                        </div>
                        <div class="text-block"></div>
                    </div>
                    <div data-w-tab="Tab 4" class="tab-pane-tab-4 w-tab-pane">
                      <form id="config-form" name="email-form-3" action="update-config.php">
                          <label for="hostname">Hostname</label>
                          <input type="text" class="config-input w-input" name="hostname" value="<?php echo escape($service->config()->hostname); ?>" data-name="hostname" id="hostname">
                          <label for="maxplayers">Max Players</label>
                          <input type="number" class="text-field-5 w-input"  name="maxplayers" value="<?php echo $service->config()->max_players; ?>" data-name="maxplayers" id="maxplayers" required="">
                          <label for="worldsize">World Size</label>
                          <input type="number" class="text-field-5 w-input" name="worldsize" value="<?php echo $service->config()->world_size; ?>" data-name="worldsize" id="worldsize" required="">
                          <label for="seed">Seed</label>
                          <input type="number" class="text-field-5 w-input"  name="seed" value="<?php echo $service->config()->seed; ?>" data-name="seed" id="seed" required="">
                          <label for="tickrate">Tick Rate</label>
                          <input type="number" class="text-field-5 w-input" name="tickrate" value="<?php echo $service->config()->tick_rate; ?>" data-name="tickrate" id="tickrate" required="">
                          <input type="submit" value="Update" data-wait="Please wait..." class="submit-button-7 w-button">
                      </form>
                    </div>
                    <div data-w-tab="Tab 2" class="tab-pane-tab-2 w-tab-pane">
                        <label id="status" for="console">Status: disconnected</label>
                        <textarea name="console" maxlength="5000" id="console" data-name="console" class="textarea w-input"></textarea>
                        <input type="text" class="text-field-4 w-input" maxlength="256" name="command" data-name="command" id="command">
                        <input onclick="connect();" value="Connect" class="submit-button-6 w-button">
                        <input onclick="send();" value="Send" class="submit-button-6 w-button">
                    </div>
                    <div data-w-tab="Tab 3" class="tab-pane-tab-3 w-tab-pane">
                        <div class="div-block-2">
                            <div class="grid-cell">
                                <div class="w-form">
                                    <form id="email-form" name="email-form" data-name="Email Form" class="form">
                                        <label for="name" class="field-label">Install RustIO</label>
                                        <input type="submit" value="Install" data-wait="Please wait..." class="submit-button-5 w-button">
                                    </form>
                                    <div class="w-form-done"></div>
                                    <div class="w-form-fail">
                                        <div>Oops! Something went wrong while submitting the form.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="w-form">
                                    <form id="email-form" name="email-form" data-name="Email Form" class="form">
                                        <label for="name" class="field-label">Update Rust &amp; Oxide</label>
                                        <input type="submit" value="Install" data-wait="Please wait..." class="submit-button-5 w-button">
                                    </form>
                                    <div class="w-form-done"></div>
                                    <div class="w-form-fail">
                                        <div>Oops! Something went wrong while submitting the form.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.4.1.min.220afd743d.js" type="text/javascript" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="js/webflow.js" type="text/javascript"></script>
    <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
</body>

</html>