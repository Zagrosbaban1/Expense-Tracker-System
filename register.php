<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $fname = trim((string)($_POST['name'] ?? ''));
    $mobno = preg_replace('/[^0-9]/', '', (string)($_POST['mobilenumber'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $repeat = (string)($_POST['repeatpassword'] ?? '');

    if ($fname === '' || $email === '' || strlen($mobno) !== 10) {
        $msg = 'Please provide valid details.';
    } elseif ($password !== $repeat || strlen($password) < 8) {
        $msg = 'Passwords must match and be at least 8 characters.';
    } else {
        $chk = mysqli_prepare($con, 'SELECT ID FROM tbluser WHERE Email = ? LIMIT 1');
        mysqli_stmt_bind_param($chk, 's', $email);
        mysqli_stmt_execute($chk);
        $exists = mysqli_stmt_get_result($chk);
        $hasUser = mysqli_fetch_assoc($exists);
        mysqli_stmt_close($chk);

        if ($hasUser) {
            $msg = 'This email is already associated with another account.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $ins = mysqli_prepare($con, 'INSERT INTO tbluser(FullName, MobileNumber, Email, Password) VALUES (?, ?, ?, ?)');
            mysqli_stmt_bind_param($ins, 'ssss', $fname, $mobno, $email, $passwordHash);
            $ok = mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);
            $msg = $ok ? 'You have successfully registered.' : 'Something went wrong. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Signup</title>
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
            <div class="panel-heading">Sign Up</div>
            <div class="panel-body">
                <form role="form" action="" method="post" name="signup">
                    <?php echo csrf_input(); ?>
                    <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
                    <fieldset>
                        <div class="form-group"><input class="form-control" placeholder="Full Name" name="name" type="text" required="true"></div>
                        <div class="form-group"><input class="form-control" placeholder="E-mail" name="email" type="email" required="true"></div>
                        <div class="form-group"><input type="text" class="form-control" name="mobilenumber" placeholder="Mobile Number" maxlength="10" pattern="[0-9]{10}" required="true"></div>
                        <div class="form-group"><input class="form-control" placeholder="Password" name="password" type="password" required="true"></div>
                        <div class="form-group"><input type="password" class="form-control" name="repeatpassword" placeholder="Repeat Password" required="true"></div>
                        <div class="checkbox">
                            <button type="submit" value="submit" name="submit" class="btn btn-primary">Register</button><span style="padding-left:250px">
                            <a href="index.php" class="btn btn-primary">Login</a></span>
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
