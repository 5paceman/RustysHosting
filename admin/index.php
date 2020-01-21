<?php
chdir("/var/www/");
require_once 'core/init.php';
require_once 'functions/sanitize.php';

$user = new User();

if(!$user->isLoggedIn())
{
    Redirect::to(404);
}

if(!$user->isAdmin())
{
    Redirect::to(404);
}

?>

<!DOCTYPE html>
<html data-wf-page="5e21d72b10e349e7adf17d44" data-wf-site="5dd179360336825db0f49358">

<head>
    <meta charset="utf-8">
    <title>Admin</title>
    <meta content="Admin" property="og:title">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="Webflow" name="generator">
    <link href="../css/normalize.css" rel="stylesheet" type="text/css">
    <link href="../css/webflow.css" rel="stylesheet" type="text/css">
    <link href="../css/rustyshosting.webflow.css" rel="stylesheet" type="text/css">
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
    <link href="../images/favicon.png" rel="shortcut icon" type="image/x-icon">
    <link href="../images/webclip.png" rel="apple-touch-icon">
</head>

<body class="body-6">
    <div class="sidebar">
        <h4 class="heading-19">Admin</h4>
        <div data-w-id="ad657537-f9e3-e02a-7982-3cead2dd7429" class="sidebar-item"><a href="#" class="sidebar-item-text"><span class="fa-solid"></span>Dashboard</a></div>
        <div data-w-id="f631cb84-eaa9-b8e8-24e3-8ead883ca505" class="sidebar-item"><a href="#" class="sidebar-item-text"><span class="fa-solid"></span>Users</a></div>
        <div data-w-id="642a9248-6343-11fd-a05f-72ad5cbeb591" class="sidebar-item"><a href="#" class="sidebar-item-text"><span class="fa-solid"></span>Machines</a></div>
        <div data-w-id="a0d48832-80a4-3016-f49d-8ba0692f46ab" class="sidebar-item"><a href="#" class="sidebar-item-text"><span class="fa-solid"></span>Services</a></div>
    </div>
    <div class="admin-content">
        <div data-w-id="ed3fce15-f066-69fb-471b-0667819f5f02" class="dashboard-content content-item-fill">
            <h3 class="heading-20">Dashboard</h3>
            <div class="dashboard-widgets">
                <div class="dashboard-widget"><canvas id="bar" width="100%" height="75%"></canvas></div>
                <div class="dashboard-widget"></div>
                <div class="dashboard-widget"></div>
                <div class="event-log">This is some text inside of a div block.</div>
            </div>
        </div>
        <div data-w-id="bb699d60-5e4b-dd1d-acc3-78ef0eafb91a" class="users-content content-item-fill">
            <h3 class="heading-20">Users</h3>
            <table id="usersTable" class="serviceTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Group</th>
                        <th scope="col">Firstname</th>
                        <th scope="col">Lastname</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $users = DB::getInstance()->get('users', array("*"))->results();
                        foreach($users as $user)
                        {
                            $id = $user->id;
                            $username = escape($user->username);
                            $email = escape($user->email);
                            $group = $user->group;
                            $Firstname = escape($user->firstname);
                            $Lastname = escape($user->lastname);
                            echo "<tr><td>{$id}</td><td>{$username}</td><td>{$email}</td><td>{$group}</td><td>{$Firstname}</td><td>{$Lastname}</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div data-w-id="2d3a7f45-7518-3343-9559-18bdfdeb31e9" class="machines-content content-item-fill">
            <h3 class="heading-20">Machine's</h3>
            <table id="machineTable" class="serviceTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">IP</th>
                        <th scope="col">Region</th>
                        <th scope="col">DNS Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $machines = DB::getInstance()->get('machines', array("*"))->results();
                        foreach($machines as $machine)
                        {
                            $id = $machine->id;
                            $IP = $machine->ip;
                            $Region = DB::getInstance()->get('regions', array("id", "=", $machine->region_id))->first()->name;
                            $DNSName = $machine->dns_name;
                            echo "<tr><td>{$id}</td><td>{$IP}</td><td>{$Region}</td><td>{$DNSName}</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div data-w-id="34e82d35-8825-51ef-b459-e1c05c90e599" class="services-content content-item-fill">
            <h3 class="heading-20">Service's</h3>
            <table id="servicesTable" class="serviceTable">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Service ID</th>
                        <th scope="col">Plan</th>
                        <th scope="col">User</th>
                        <th scope="col">Expiry</th>
                        <th scope="col">Machine</th>
                        <th scope="col">Port</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $services = DB::getInstance()->get('services', array("*"))->results();
                        foreach($services as $service)
                        {
                            $id = $service->id;
                            $ServiceID = $service->service_id;
                            $Plan = DB::getInstance()->get('plans', array('id', '=', $service->plan_id))->first()->name;
                            $User = DB::getInstance()->get('users', array('id', '=', $service->user_id))->first()->username;
                            $Expiry = $service->expiry;
                            $machine = DB::getInstance()->get('machines', array('id', '=', $service->machine_id))->first()->dns_name;
                            $port = $service->port;
                            echo "<tr><td><a style=\"color: black; text-decoration: none;\"href=\"../manage.php?serviceId={$ServiceID}\"><span class=\"list-icon\"> </span>{$id}</a></td><td>{$ServiceID}</td><td>{$Plan}</td><td>{$User}</td><td>{$Expiry}</td><td>{$machine}</td><td>{$port}</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js" type="text/javascript"></script>
    <script src="https://d3e54v103j8qbb.cloudfront.net/js/jquery-3.4.1.min.220afd743d.js" type="text/javascript" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="../js/webflow.js" type="text/javascript"></script>
    <script src="../js/fancyTable.min.js" type="text/javascript"></script>
    <script src="admin.js" type="text/javascript"></script>
    <!-- [if lte IE 9]><script src="https://cdnjs.cloudflare.com/ajax/libs/placeholders/3.0.2/placeholders.min.js"></script><![endif] -->
</body>

</html>