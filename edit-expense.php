<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$userid = (int)$_SESSION['detsuid'];
$editid = (int)($_GET['editid'] ?? 0);
$msg = '';

if ($editid <= 0) {
    header('Location: manage-expense.php');
    exit;
}

$currencyColumnQuery = mysqli_query($con, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tblexpense' AND COLUMN_NAME = 'ExpenseCurrency' LIMIT 1");
if ($currencyColumnQuery && mysqli_num_rows($currencyColumnQuery) === 0) {
    mysqli_query($con, "ALTER TABLE tblexpense ADD COLUMN ExpenseCurrency VARCHAR(10) NOT NULL DEFAULT 'USD'");
}

$defaultCurrencies = ['USD', 'EUR', 'GBP', 'IQD', 'INR', 'AED', 'SAR', 'TRY'];
$currencyOptions = $defaultCurrencies;
$curStmt = mysqli_prepare($con, "SELECT DISTINCT IFNULL(NULLIF(ExpenseCurrency,''),'USD') AS Currency FROM tblexpense WHERE UserId = ? ORDER BY Currency ASC");
mysqli_stmt_bind_param($curStmt, 'i', $userid);
mysqli_stmt_execute($curStmt);
$curRes = mysqli_stmt_get_result($curStmt);
while ($r = mysqli_fetch_assoc($curRes)) {
    $cc = normalize_currency($r['Currency']);
    if (!in_array($cc, $currencyOptions, true)) {
        $currencyOptions[] = $cc;
    }
}
mysqli_stmt_close($curStmt);
sort($currencyOptions);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $dateexpense = normalize_date($_POST['dateexpense'] ?? '');
    $item = trim((string)($_POST['item'] ?? ''));
    $costitem = (float)($_POST['costitem'] ?? 0);
    $currency = strtoupper(trim((string)($_POST['currency'] ?? 'USD')));

    if ($currency === 'OTHER') {
        $currency = normalize_currency($_POST['othercurrency'] ?? 'USD');
    } else {
        $currency = normalize_currency($currency);
    }

    if ($dateexpense === null || $item === '' || $costitem < 0) {
        $msg = 'Please provide valid expense details.';
    } else {
        $up = mysqli_prepare($con, 'UPDATE tblexpense SET ExpenseDate = ?, ExpenseItem = ?, ExpenseCost = ?, ExpenseCurrency = ? WHERE ID = ? AND UserId = ?');
        mysqli_stmt_bind_param($up, 'ssdsii', $dateexpense, $item, $costitem, $currency, $editid, $userid);
        $ok = mysqli_stmt_execute($up);
        mysqli_stmt_close($up);

        if ($ok) {
            header('Location: manage-expense.php');
            exit;
        }

        $msg = 'Something went wrong. Please try again.';
    }
}

$sel = mysqli_prepare($con, 'SELECT ExpenseDate, ExpenseItem, ExpenseCost, IFNULL(NULLIF(ExpenseCurrency,\'\'),\'USD\') AS ExpenseCurrency FROM tblexpense WHERE ID = ? AND UserId = ? LIMIT 1');
mysqli_stmt_bind_param($sel, 'ii', $editid, $userid);
mysqli_stmt_execute($sel);
$res = mysqli_stmt_get_result($sel);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($sel);

if (!$row) {
    header('Location: manage-expense.php');
    exit;
}

$selectedCurrency = normalize_currency($row['ExpenseCurrency']);
if (!in_array($selectedCurrency, $currencyOptions, true)) {
    $currencyOptions[] = $selectedCurrency;
    sort($currencyOptions);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daily Expense Tracker || Edit Expense</title>
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
<div class="row"><div class="col-lg-12"><div class="panel panel-default"><div class="panel-heading">Edit Expense</div><div class="panel-body">
<p style="font-size:16px; color:red" align="center"><?php echo e($msg); ?></p>
<div class="col-md-12">
<form role="form" method="post" action="">
<?php echo csrf_input(); ?>
<div class="form-group"><label>Date of Expense</label><input class="form-control" type="date" name="dateexpense" value="<?php echo e($row['ExpenseDate']); ?>" required="true"></div>
<div class="form-group"><label>Item</label><input type="text" class="form-control" name="item" value="<?php echo e($row['ExpenseItem']); ?>" required="true"></div>
<div class="form-group"><label>Cost of Item</label><input class="form-control" type="number" step="0.01" min="0" name="costitem" value="<?php echo e($row['ExpenseCost']); ?>" required="true"></div>
<div class="form-group"><label>Currency</label><select class="form-control" name="currency" id="currency-select" required="true" onchange="toggleOtherCurrency(this.value)"><?php foreach($currencyOptions as $currencyOption) { ?><option value="<?php echo e($currencyOption); ?>" <?php if($selectedCurrency === $currencyOption) { echo 'selected'; } ?>><?php echo e($currencyOption); ?></option><?php } ?><option value="OTHER">Other</option></select><input class="form-control" type="text" name="othercurrency" id="other-currency" placeholder="Enter currency code" maxlength="10" style="margin-top:8px; display:none;"></div>
<div class="form-group has-success"><button type="submit" class="btn btn-primary" name="submit">Update Expense</button></div>
</form>
</div>
</div></div></div></div>
</div>
<?php include_once('includes/footer.php');?>
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
function toggleOtherCurrency(value) {
    var otherInput = document.getElementById('other-currency');
    if (!otherInput) return;
    otherInput.style.display = (value === 'OTHER') ? 'block' : 'none';
}
</script>
</body>
</html>
