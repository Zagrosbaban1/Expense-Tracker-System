<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$userid = (int)$_SESSION['detsuid'];

function formatAmountByCurrency($amount, $currencyCode) {
    if ($amount === null || $amount === '') {
        return $currencyCode . ' 0';
    }
    return $currencyCode . ' ' . $amount;
}

$currencyColumnQuery = mysqli_query($con, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tblexpense' AND COLUMN_NAME = 'ExpenseCurrency' LIMIT 1");
if ($currencyColumnQuery && mysqli_num_rows($currencyColumnQuery) === 0) {
    mysqli_query($con, "ALTER TABLE tblexpense ADD COLUMN ExpenseCurrency VARCHAR(10) NOT NULL DEFAULT 'USD'");
}

$currencyOptions = ['USD'];
$curStmt = mysqli_prepare($con, "SELECT DISTINCT IFNULL(NULLIF(ExpenseCurrency,''),'USD') AS Currency FROM tblexpense WHERE UserId = ? ORDER BY Currency ASC");
mysqli_stmt_bind_param($curStmt, 'i', $userid);
mysqli_stmt_execute($curStmt);
$curRes = mysqli_stmt_get_result($curStmt);
while ($row = mysqli_fetch_assoc($curRes)) {
    $cc = normalize_currency($row['Currency']);
    if (!in_array($cc, $currencyOptions, true)) {
        $currencyOptions[] = $cc;
    }
}
mysqli_stmt_close($curStmt);
sort($currencyOptions);

$selectedCurrency = 'USD';
if (isset($_GET['currency'])) {
    $requested = normalize_currency($_GET['currency']);
    if (in_array($requested, $currencyOptions, true)) {
        $selectedCurrency = $requested;
    }
}

function sum_for_range($con, $userid, $selectedCurrency, $fromDate, $toDate)
{
    $stmt = mysqli_prepare($con, "SELECT SUM(ExpenseCost) AS total FROM tblexpense WHERE ExpenseDate BETWEEN ? AND ? AND UserId = ? AND IFNULL(NULLIF(ExpenseCurrency,''),'USD') = ?");
    mysqli_stmt_bind_param($stmt, 'ssis', $fromDate, $toDate, $userid, $selectedCurrency);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    return $row['total'] ?? 0;
}

$tdate = date('Y-m-d');
$ydate = date('Y-m-d', strtotime('-1 days'));
$pastdate = date('Y-m-d', strtotime('-1 week'));
$monthdate = date('Y-m-d', strtotime('-1 month'));
$yearStart = date('Y-01-01');
$yearEnd = date('Y-12-31');

$sum_today_expense = sum_for_range($con, $userid, $selectedCurrency, $tdate, $tdate);
$sum_yesterday_expense = sum_for_range($con, $userid, $selectedCurrency, $ydate, $ydate);
$sum_weekly_expense = sum_for_range($con, $userid, $selectedCurrency, $pastdate, $tdate);
$sum_monthly_expense = sum_for_range($con, $userid, $selectedCurrency, $monthdate, $tdate);
$sum_yearly_expense = sum_for_range($con, $userid, $selectedCurrency, $yearStart, $yearEnd);
$sum_total_expense = sum_for_range($con, $userid, $selectedCurrency, '1970-01-01', '2099-12-31');

$totalsStmt = mysqli_prepare($con, "SELECT IFNULL(NULLIF(ExpenseCurrency,''),'USD') AS Currency, SUM(ExpenseCost) AS CurrencyTotal, COUNT(*) AS EntryCount FROM tblexpense WHERE UserId = ? GROUP BY IFNULL(NULLIF(ExpenseCurrency,''),'USD') ORDER BY Currency ASC");
mysqli_stmt_bind_param($totalsStmt, 'i', $userid);
mysqli_stmt_execute($totalsStmt);
$currencyTotals = mysqli_stmt_get_result($totalsStmt);

$recentStmt = mysqli_prepare($con, "SELECT ExpenseItem, ExpenseCost, IFNULL(NULLIF(ExpenseCurrency,''),'USD') AS Currency FROM tblexpense WHERE UserId = ? AND IFNULL(NULLIF(ExpenseCurrency,''),'USD') = ? ORDER BY ID DESC LIMIT 8");
mysqli_stmt_bind_param($recentStmt, 'is', $userid, $selectedCurrency);
mysqli_stmt_execute($recentStmt);
$recentExpenses = mysqli_stmt_get_result($recentStmt);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker - Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="app-page">
<?php include_once('includes/header.php');?>
<?php include_once('includes/sidebar.php');?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
<div class="row"><ol class="breadcrumb"><li><a href="#"><em class="fa fa-home"></em></a></li><li class="active">Dashboard</li></ol></div>
<div class="row"><div class="col-lg-12"><h1 class="page-header">Dashboard</h1></div></div>
<div class="row"><div class="col-lg-4"><form method="get" action=""><div class="form-group"><label>Currency</label><select class="form-control" name="currency" onchange="this.form.submit()"><?php foreach($currencyOptions as $currencyOption) { ?><option value="<?php echo e($currencyOption); ?>" <?php if($selectedCurrency === $currencyOption) { echo 'selected'; } ?>><?php echo e($currencyOption); ?></option><?php } ?></select></div></form></div></div>
<div class="row">
<div class="col-xs-6 col-md-3"><div class="panel panel-default"><div class="panel-body easypiechart-panel"><h4>Today's Expense (<?php echo e($selectedCurrency); ?>)</h4><div class="easypiechart"><span class="percent"><?php echo e(formatAmountByCurrency($sum_today_expense, $selectedCurrency)); ?></span></div></div></div></div>
<div class="col-xs-6 col-md-3"><div class="panel panel-default"><div class="panel-body easypiechart-panel"><h4>Yesterday's Expense (<?php echo e($selectedCurrency); ?>)</h4><div class="easypiechart"><span class="percent"><?php echo e(formatAmountByCurrency($sum_yesterday_expense, $selectedCurrency)); ?></span></div></div></div></div>
<div class="col-xs-6 col-md-3"><div class="panel panel-default"><div class="panel-body easypiechart-panel"><h4>Last 7 Days (<?php echo e($selectedCurrency); ?>)</h4><div class="easypiechart"><span class="percent"><?php echo e(formatAmountByCurrency($sum_weekly_expense, $selectedCurrency)); ?></span></div></div></div></div>
<div class="col-xs-6 col-md-3"><div class="panel panel-default"><div class="panel-body easypiechart-panel"><h4>Last 30 Days (<?php echo e($selectedCurrency); ?>)</h4><div class="easypiechart"><span class="percent"><?php echo e(formatAmountByCurrency($sum_monthly_expense, $selectedCurrency)); ?></span></div></div></div></div>
</div>
<div class="row"><div class="col-xs-6 col-md-3"><div class="panel panel-default"><div class="panel-body easypiechart-panel"><h4>Current Year (<?php echo e($selectedCurrency); ?>)</h4><div class="easypiechart"><span class="percent"><?php echo e(formatAmountByCurrency($sum_yearly_expense, $selectedCurrency)); ?></span></div></div></div></div><div class="col-xs-6 col-md-3"><div class="panel panel-default"><div class="panel-body easypiechart-panel"><h4>Total (<?php echo e($selectedCurrency); ?>)</h4><div class="easypiechart"><span class="percent"><?php echo e(formatAmountByCurrency($sum_total_expense, $selectedCurrency)); ?></span></div></div></div></div></div>
<div class="row"><div class="col-lg-7"><div class="panel panel-default"><div class="panel-heading">Total By Currency</div><div class="panel-body"><div class="table-responsive"><table class="table table-bordered mg-b-0"><thead><tr><th>Currency</th><th>Total Amount</th><th>Entries</th></tr></thead><tbody><?php while ($row = mysqli_fetch_assoc($currencyTotals)) { ?><tr><td><?php echo e($row['Currency']); ?></td><td><?php echo e($row['CurrencyTotal']); ?></td><td><?php echo e($row['EntryCount']); ?></td></tr><?php } ?></tbody></table></div></div></div></div><div class="col-lg-5"><div class="panel panel-default"><div class="panel-heading">Recent Expenses (<?php echo e($selectedCurrency); ?>)</div><div class="panel-body"><div class="table-responsive"><table class="table table-bordered mg-b-0"><thead><tr><th>Item</th><th>Amount</th><th>Currency</th></tr></thead><tbody><?php while ($row = mysqli_fetch_assoc($recentExpenses)) { ?><tr><td><?php echo e($row['ExpenseItem']); ?></td><td><?php echo e($row['ExpenseCost']); ?></td><td><?php echo e($row['Currency']); ?></td></tr><?php } ?></tbody></table></div></div></div></div></div>
</div>
<?php include_once('includes/footer.php');?>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
<?php mysqli_stmt_close($totalsStmt); mysqli_stmt_close($recentStmt); ?>
