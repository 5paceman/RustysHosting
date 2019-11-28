<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://js.stripe.com/v3/"></script>
</head>
<?php 
require_once 'core/init.php';

if(Session::exists('home')) {
    echo Session::flash('home');
}


$user = new User();
if($user->isLoggedIn()) {

    if($user->hasPermission('admin')) {
        echo 'You\'re an admin<br/>';
    }
    $token = Token::generate();
    ?>
        <p>Hello <a href="profile.php?user=<?php echo escape($user->data()->username);?>"><?php echo escape($user->data()->username);?></a></p>
        <ul>
            <li><a href="update.php">Update</a></li>
            <li><a href="changepassword.php">Change Password</a></li>
            <li><a href="logout.php">Log Out</a></li>
        </ul>
        <form action="purchase.php" method="POST"><input type="hidden" name="planId" value="1"/><input type="hidden" name="regionId" value="1"/><input type="hidden" name="token" value="<?php echo $token ?>"/><button type="submit">Purchase Plan</button><br/></form>
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Plan</th>
                    <th scope="col">Expiry</th>
                    <th scope="col">IP</th>
                    <th scope="col">Port</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
    <?php
    $service = new Service();
    $services = $service->findAll($user->data()->id);
    
    foreach($services as $user_service) {
        ?>
        <tr>
            <td><?php echo $user_service->plan(); ?></td>
            <td><?php echo $user_service->expiry(); ?></td>
            <td><?php echo $user_service->ip(); ?></td>
            <td><?php echo $user_service->port(); ?></td>
            <?php
            $time = strtotime($user_service->expiry());
            if($time < time()) {
                echo '<td><form action="purchase.php" method="POST"><input type="hidden" name="planId" value="1"/><input type="hidden" name="regionId" value="1"/><input type="hidden" name="token" value="'.$token.'"/><button type="submit">Pay</button><br/></form></td>';
            } elseif($time > time()) {
                echo '<td><form action="updatesub.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><button type="submit">Manage</button><br/></form><form action="deletesub.php" method="POST"><input type="hidden" name="serviceId" value="'.$user_service->id().'"/><input type="hidden" name="token" value="'.$token.'"/><button type="submit">Cancel</button><br/></form></td>';
            }
            ?>
        </tr>
        <?php
    }
    ?>
    </table> 
    <?php
} else {
    echo '<p>You need to <a href="login.php">login</a> or <a href="logout.php">register</a></p>';
}
?>
