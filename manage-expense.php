<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$userid = (int)$_SESSION['detsuid'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf_or_die();

    $rowid = (int)$_POST['delete_id'];
    if ($rowid > 0) {
        $del = mysqli_prepare($con, 'DELETE FROM tblexpense WHERE ID = ? AND UserId = ?');
        mysqli_stmt_bind_param($del, 'ii', $rowid, $userid);
        mysqli_stmt_execute($del);
        mysqli_stmt_close($del);
        $msg = 'Record successfully deleted.';
    }
}

$list = mysqli_prepare($con, 'SELECT ID, ExpenseItem, ExpenseCost, IFNULL(NULLIF(ExpenseCurrency,\'\'),\'USD\') AS ExpenseCurrency, ExpenseDate FROM tblexpense WHERE UserId = ? ORDER BY ID DESC');
mysqli_stmt_bind_param($list, 'i', $userid);
mysqli_stmt_execute($list);
$ret = mysqli_stmt_get_result($list);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker || Manage Expense</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="app-page">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
<div class="row"><ol class="breadcrumb"><li><a href="#"><em class="fa fa-home"></em></a></li><li class="active">Expense</li></ol></div>
<div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">Expense</div><div class="panel-body">
<p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
<div class="col-md-12"><div class="table-responsive">
<table class="table table-bordered mg-b-0">
<thead><tr><th>S.NO</th><th>Expense Item</th><th>Expense Cost</th><th>Currency</th><th>Expense Date</th><th>Action</th></tr></thead>
<tbody>
<?php $cnt = 1; while ($row = mysqli_fetch_assoc($ret)) { ?>
<tr>
<td><?php echo $cnt; ?></td>
<td><?php echo e($row['ExpenseItem']); ?></td>
<td><?php echo e($row['ExpenseCost']); ?></td>
<td><?php echo e($row['ExpenseCurrency']); ?></td>
<td><?php echo e($row['ExpenseDate']); ?></td>
<td class="expense-actions">
<a class="btn btn-success btn-xs" href="edit-expense.php?editid=<?php echo (int)$row['ID']; ?>"><em class="fa fa-pencil"></em> Edit</a>
<form method="post" action="" style="display:inline-block; margin-left:4px;" onsubmit="return confirm('Do you really want to delete this expense?');">
<?php echo csrf_input(); ?>
<input type="hidden" name="delete_id" value="<?php echo (int)$row['ID']; ?>">
<button class="btn btn-danger btn-xs" type="submit"><em class="fa fa-trash"></em> Delete</button>
</form>
</td>
</tr>
<?php $cnt++; } ?>
</tbody></table>
</div></div>
</div></div></div></div>
</div>
<?php include_once('includes/footer.php');?>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php mysqli_stmt_close($list); ?>
