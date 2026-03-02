<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();
verify_csrf_or_die();
$userid = (int)$_SESSION['detsuid'];
$fdate = normalize_date($_POST['fromdate'] ?? '');
$tdate = normalize_date($_POST['todate'] ?? '');
$rows = [];
$totalsexp = 0;
if ($fdate && $tdate && $fdate <= $tdate) {
    $stmt = mysqli_prepare($con, 'SELECT YEAR(ExpenseDate) as rptyear, SUM(ExpenseCost) as totalyear FROM tblexpense WHERE ExpenseDate BETWEEN ? AND ? AND UserId = ? GROUP BY YEAR(ExpenseDate) ORDER BY YEAR(ExpenseDate)');
    mysqli_stmt_bind_param($stmt, 'ssi', $fdate, $tdate, $userid);
    mysqli_stmt_execute($stmt);
    $ret = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($ret)) { $rows[] = $row; $totalsexp += (float)$row['totalyear']; }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Yearwise Expense Report</title><link href="css/bootstrap.min.css" rel="stylesheet"><link href="css/font-awesome.min.css" rel="stylesheet"><link href="css/datepicker3.css" rel="stylesheet"><link href="css/styles.css" rel="stylesheet"></head><body class="app-page"><?php include_once('includes/header.php');?><?php include_once('includes/sidebar.php');?><div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main"><div class="row"><ol class="breadcrumb"><li><a href="#"><em class="fa fa-home"></em></a></li><li class="active">Yearwise Expense Report</li></ol></div><div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">Yearwise Expense Report</div><div class="panel-body"><div class="col-md-12"><h5 align="center" style="color:blue">Yearwise Expense Report from <?php echo e($fdate); ?> to <?php echo e($tdate); ?></h5><hr /><table class="table table-bordered"><thead><tr><th>S.NO</th><th>Year</th><th>Expense Amount</th></tr></thead><tbody><?php $cnt = 1; foreach ($rows as $row) { ?><tr><td><?php echo $cnt; ?></td><td><?php echo e($row['rptyear']); ?></td><td><?php echo e($row['totalyear']); ?></td></tr><?php $cnt++; } ?><tr><th colspan="2" style="text-align:center">Grand Total</th><td><?php echo e($totalsexp); ?></td></tr></tbody></table></div></div></div></div></div></div><?php include_once('includes/footer.php');?><script src="js/jquery-1.11.1.min.js"></script><script src="js/bootstrap.min.js"></script></body></html>
