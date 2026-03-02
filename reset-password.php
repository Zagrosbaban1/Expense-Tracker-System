<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';

if (empty($_SESSION['password_reset_user'])) {
    header('Location: forgot-password.php');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $newPassword = (string)($_POST['newpassword'] ?? '');
    $confirm = (string)($_POST['confirmpassword'] ?? '');

    if ($newPassword !== $confirm || strlen($newPassword) < 8) {
        $msg = 'Passwords must match and be at least 8 characters.';
    } else {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $uid = (int)$_SESSION['password_reset_user'];
        $stmt = mysqli_prepare($con, 'UPDATE tbluser SET Password = ? WHERE ID = ?');
        mysqli_stmt_bind_param($stmt, 'si', $hash, $uid);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($ok) {
            unset($_SESSION['password_reset_user']);
            session_regenerate_id(true);
            $msg = 'Password successfully changed. Please login.';
        } else {
            $msg = 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Reset Password</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="app-page">
<div class="row">
    <h2 align="center">Daily Expense Tracker</h2>
    <hr />
    <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
        <div class="login-panel panel panel-default">
            <div class="panel-heading">Reset Password</div>
            <div class="panel-body">
                <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
                <form role="form" action="" method="post" name="changepassword">
                    <?php echo csrf_input(); ?>
                    <fieldset>
                        <div class="form-group"><input class="form-control" placeholder="Password" name="newpassword" type="password" required="true"></div>
                        <div class="form-group"><input class="form-control" placeholder="Confirm Password" name="confirmpassword" type="password" required="true"></div>
                        <div class="checkbox">
                            <button type="submit" name="submit" class="btn btn-primary">Reset</button><span style="padding-left:250px"><a href="index.php" class="btn btn-primary">Login</a></span>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
