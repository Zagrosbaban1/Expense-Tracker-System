<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$msg = '';
$userid = (int)$_SESSION['detsuid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $currentPassword = (string)($_POST['currentpassword'] ?? '');
    $newPassword = (string)($_POST['newpassword'] ?? '');
    $confirmPassword = (string)($_POST['confirmpassword'] ?? '');

    if ($newPassword !== $confirmPassword || strlen($newPassword) < 8) {
        $msg = 'New password and confirm password must match and be at least 8 characters.';
    } else {
        $stmt = mysqli_prepare($con, 'SELECT Password FROM tbluser WHERE ID = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 'i', $userid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($row && verify_password_legacy_aware($currentPassword, $row['Password'])) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $up = mysqli_prepare($con, 'UPDATE tbluser SET Password = ? WHERE ID = ?');
            mysqli_stmt_bind_param($up, 'si', $newHash, $userid);
            mysqli_stmt_execute($up);
            mysqli_stmt_close($up);
            $msg = 'Your password successfully changed.';
        } else {
            $msg = 'Your current password is wrong.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker || Change Password</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="app-page">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row"><ol class="breadcrumb"><li><a href="#"><em class="fa fa-home"></em></a></li><li class="active">Change Password</li></ol></div>
    <div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">Change Password</div><div class="panel-body">
        <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
        <div class="col-md-12">
            <form role="form" method="post" action="" name="changepassword">
                <?php echo csrf_input(); ?>
                <div class="form-group"><label>Current Password</label><input type="password" name="currentpassword" class="form-control" required="true"></div>
                <div class="form-group"><label>New Password</label><input type="password" name="newpassword" class="form-control" required="true"></div>
                <div class="form-group"><label>Confirm Password</label><input type="password" name="confirmpassword" class="form-control" required="true"></div>
                <div class="form-group has-success"><button type="submit" class="btn btn-primary" name="submit">Change</button></div>
            </form>
        </div>
    </div></div></div></div>
</div>
<?php include_once('includes/footer.php');?>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
