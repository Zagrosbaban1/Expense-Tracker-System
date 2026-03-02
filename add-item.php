<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$userid = (int)$_SESSION['detsuid'];
$msg = '';

mysqli_query($con, "CREATE TABLE IF NOT EXISTS tblitemmaster (
  ID INT(11) NOT NULL AUTO_INCREMENT,
  UserId INT(11) NOT NULL,
  ItemName VARCHAR(255) NOT NULL,
  CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  UNIQUE KEY uniq_user_item (UserId, ItemName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $itemname = trim((string)($_POST['itemname'] ?? ''));
    if ($itemname === '') {
        $msg = 'Item name is required.';
    } else {
        $ins = mysqli_prepare($con, 'INSERT INTO tblitemmaster(UserId, ItemName) VALUES (?, ?)');
        mysqli_stmt_bind_param($ins, 'is', $userid, $itemname);
        $ok = mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);

        if ($ok) {
            header('Location: add-item.php');
            exit;
        }
        $msg = 'This item already exists or could not be added.';
    }
}

$listStmt = mysqli_prepare($con, 'SELECT ItemName FROM tblitemmaster WHERE UserId = ? ORDER BY ItemName ASC');
mysqli_stmt_bind_param($listStmt, 'i', $userid);
mysqli_stmt_execute($listStmt);
$itemList = mysqli_stmt_get_result($listStmt);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daily Expense Tracker || Add Item</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/datepicker3.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">
</head>
<body class="app-page">
<?php include_once('includes/header.php'); ?>
<?php include_once('includes/sidebar.php'); ?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
  <div class="row"><ol class="breadcrumb"><li><a href="#"><em class="fa fa-home"></em></a></li><li class="active">Items</li></ol></div>
  <div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">Add New Item</div><div class="panel-body">
    <p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
    <div class="col-md-12">
      <form role="form" method="post" action="">
        <?php echo csrf_input(); ?>
        <div class="form-group"><label>Item Name</label><input type="text" class="form-control" name="itemname" required="true" placeholder="e.g. Groceries"></div>
        <div class="form-group has-success"><button type="submit" class="btn btn-primary" name="submit">Add Item</button></div>
      </form>
    </div>
    <div class="col-md-12" style="margin-top:20px;">
      <div class="table-responsive"><table class="table table-bordered mg-b-0"><thead><tr><th>S.NO</th><th>Item Name</th></tr></thead><tbody>
      <?php $cnt = 1; while ($row = mysqli_fetch_assoc($itemList)) { ?>
        <tr><td><?php echo $cnt; ?></td><td><?php echo e($row['ItemName']); ?></td></tr>
      <?php $cnt++; } if ($cnt === 1) { ?>
        <tr><td colspan="2" class="text-center">No items added yet.</td></tr>
      <?php } ?>
      </tbody></table></div>
    </div>
  </div></div></div></div>
</div>
<?php include_once('includes/footer.php'); ?>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php mysqli_stmt_close($listStmt); ?>
