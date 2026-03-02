<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$userid = (int)$_SESSION['detsuid'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $fullname = trim((string)($_POST['fullname'] ?? ''));
    $mobno = preg_replace('/[^0-9]/', '', (string)($_POST['contactnumber'] ?? ''));

    if ($fullname === '' || strlen($mobno) !== 10) {
        $msg = 'Please provide valid profile details.';
    } else {
        $stmt = mysqli_prepare($con, 'UPDATE tbluser SET FullName = ?, MobileNumber = ? WHERE ID = ?');
        mysqli_stmt_bind_param($stmt, 'ssi', $fullname, $mobno, $userid);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = $ok ? 'User profile has been updated.' : 'Something went wrong. Please try again.';
    }
}

$stmt = mysqli_prepare($con, 'SELECT FullName, Email, MobileNumber, RegDate FROM tbluser WHERE ID = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker || User Profile</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="app-page">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row"><ol class="breadcrumb"><li><a href="#"><em class="fa fa-home"></em></a></li><li class="active">Profile</li></ol></div>
    <div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">Profile</div><div class="panel-body">
        <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
        <div class="col-md-12">
            <form role="form" method="post" action="">
                <?php echo csrf_input(); ?>
                <div class="form-group"><label>Full Name</label><input class="form-control" type="text" value="<?php echo e($row['FullName'] ?? ''); ?>" name="fullname" required="true"></div>
                <div class="form-group"><label>Email</label><input type="email" class="form-control" name="email" value="<?php echo e($row['Email'] ?? ''); ?>" readonly="true"></div>
                <div class="form-group"><label>Mobile Number</label><input class="form-control" type="text" value="<?php echo e($row['MobileNumber'] ?? ''); ?>" required="true" name="contactnumber" maxlength="10" pattern="[0-9]{10}"></div>
                <div class="form-group"><label>Registration Date</label><input class="form-control" type="text" value="<?php echo e($row['RegDate'] ?? ''); ?>" readonly="true"></div>
                <div class="form-group has-success"><button type="submit" class="btn btn-primary" name="submit">Update</button></div>
            </form>
        </div>
    </div></div></div></div>
</div>
<?php include_once('includes/footer.php');?>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
