<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $contactno = preg_replace('/[^0-9]/', '', (string)($_POST['contactno'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));

    $stmt = mysqli_prepare($con, 'SELECT ID FROM tbluser WHERE Email = ? AND MobileNumber = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'ss', $email, $contactno);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $_SESSION['password_reset_user'] = (int)$row['ID'];
        header('Location: reset-password.php');
        exit;
    }

    $msg = 'Invalid details. Please try again.';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Forgot Password</title>
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
            <div class="panel-heading">Forgot Password</div>
            <div class="panel-body">
                <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
                <form role="form" action="" method="post" name="resetrequest">
                    <?php echo csrf_input(); ?>
                    <fieldset>
                        <div class="form-group"><input class="form-control" placeholder="E-mail" name="email" type="email" required="true" autofocus></div>
                        <div class="form-group"><input class="form-control" placeholder="Mobile Number" name="contactno" type="text" maxlength="10" pattern="[0-9]{10}" required="true"></div>
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
