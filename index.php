<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    verify_csrf_or_die();

    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = mysqli_prepare($con, 'SELECT ID, Password FROM tbluser WHERE Email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($user && verify_password_legacy_aware($password, $user['Password'])) {
        if (preg_match('/^[a-f0-9]{32}$/i', $user['Password'])) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $up = mysqli_prepare($con, 'UPDATE tbluser SET Password = ? WHERE ID = ?');
            mysqli_stmt_bind_param($up, 'si', $newHash, $user['ID']);
            mysqli_stmt_execute($up);
            mysqli_stmt_close($up);
        }

        session_regenerate_id(true);
        $_SESSION['detsuid'] = (int)$user['ID'];
        header('Location: dashboard.php');
        exit;
    }

    $msg = 'Invalid details.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Login</title>
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
            <div class="panel-heading">Log in</div>
            <div class="panel-body">
                <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
                <form role="form" action="" method="post" name="login">
                    <?php echo csrf_input(); ?>
                    <fieldset>
                        <div class="form-group">
                            <input class="form-control" placeholder="E-mail" name="email" type="email" required="true" autofocus>
                        </div>
                        <a href="forgot-password.php">Forgot Password?</a>
                        <div class="form-group">
                            <input class="form-control" placeholder="Password" name="password" type="password" required="true">
                        </div>
                        <div class="checkbox">
                            <button type="submit" value="login" name="login" class="btn btn-primary">Login</button><span style="padding-left:250px">
                            <a href="register.php" class="btn btn-primary">Register</a></span>
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
