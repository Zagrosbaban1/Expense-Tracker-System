<?php
error_reporting(0);
require_once 'includes/security.php';
require_once 'includes/dbconnection.php';
require_login();

$userid = (int)$_SESSION['detsuid'];
$msg = '';
$itemOptions = [];
$defaultCurrencies = ['USD', 'EUR', 'GBP', 'IQD', 'INR', 'AED', 'SAR', 'TRY'];
$currencyOptions = $defaultCurrencies;
$selectedCurrencyValue = 'USD';
$otherCurrencyValue = '';

mysqli_query($con, "CREATE TABLE IF NOT EXISTS tblitemmaster (
  ID INT(11) NOT NULL AUTO_INCREMENT,
  UserId INT(11) NOT NULL,
  ItemName VARCHAR(255) NOT NULL,
  CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  UNIQUE KEY uniq_user_item (UserId, ItemName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$currencyColumnQuery = mysqli_query($con, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tblexpense' AND COLUMN_NAME = 'ExpenseCurrency' LIMIT 1");
if ($currencyColumnQuery && mysqli_num_rows($currencyColumnQuery) === 0) {
    mysqli_query($con, "ALTER TABLE tblexpense ADD COLUMN ExpenseCurrency VARCHAR(10) NOT NULL DEFAULT 'USD'");
}

$itemStmt = mysqli_prepare($con, 'SELECT ItemName FROM tblitemmaster WHERE UserId = ? ORDER BY ItemName ASC');
mysqli_stmt_bind_param($itemStmt, 'i', $userid);
mysqli_stmt_execute($itemStmt);
$itemResult = mysqli_stmt_get_result($itemStmt);
while ($row = mysqli_fetch_assoc($itemResult)) {
    $itemOptions[] = $row['ItemName'];
}
mysqli_stmt_close($itemStmt);

if (empty($itemOptions)) {
    $legacyStmt = mysqli_prepare($con, "SELECT DISTINCT ExpenseItem FROM tblexpense WHERE UserId = ? AND ExpenseItem <> '' ORDER BY ExpenseItem ASC");
    mysqli_stmt_bind_param($legacyStmt, 'i', $userid);
    mysqli_stmt_execute($legacyStmt);
    $legacyResult = mysqli_stmt_get_result($legacyStmt);
    while ($row = mysqli_fetch_assoc($legacyResult)) {
        $itemOptions[] = $row['ExpenseItem'];
    }
    mysqli_stmt_close($legacyStmt);
}

$currencyStmt = mysqli_prepare($con, "SELECT DISTINCT IFNULL(NULLIF(ExpenseCurrency,''),'USD') AS Currency FROM tblexpense WHERE UserId = ? ORDER BY Currency ASC");
mysqli_stmt_bind_param($currencyStmt, 'i', $userid);
mysqli_stmt_execute($currencyStmt);
$currencyResult = mysqli_stmt_get_result($currencyStmt);
while ($row = mysqli_fetch_assoc($currencyResult)) {
    $code = normalize_currency($row['Currency']);
    if (!in_array($code, $currencyOptions, true)) {
        $currencyOptions[] = $code;
    }
}
mysqli_stmt_close($currencyStmt);
sort($currencyOptions);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    verify_csrf_or_die();

    $dateexpense = normalize_date($_POST['dateexpense'] ?? '');
    $item = trim((string)($_POST['item'] ?? ''));
    $costitem = (float)($_POST['costitem'] ?? 0);
    $currency = strtoupper(trim((string)($_POST['currency'] ?? 'USD')));
    $selectedCurrencyValue = $currency;

    if ($currency === 'OTHER') {
        $otherCurrencyValue = (string)($_POST['othercurrency'] ?? '');
        $currency = normalize_currency($otherCurrencyValue);
    } else {
        $currency = normalize_currency($currency);
    }

    if ($dateexpense === null || $item === '' || $costitem < 0) {
        $msg = 'Please provide valid expense details.';
    } else {
        $ins = mysqli_prepare($con, 'INSERT INTO tblexpense(UserId, ExpenseDate, ExpenseItem, ExpenseCost, ExpenseCurrency) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($ins, 'issds', $userid, $dateexpense, $item, $costitem, $currency);
        $ok = mysqli_stmt_execute($ins);
        mysqli_stmt_close($ins);

        if ($ok) {
            header('Location: manage-expense.php');
            exit;
        }

        $msg = 'Something went wrong. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Expense Tracker || Add Expense</title>
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
        <div class="col-md-12">
            <form role="form" method="post" action="">
                <?php echo csrf_input(); ?>
                <div class="form-group"><label>Date of Expense</label><input class="form-control" type="date" name="dateexpense" required="true"></div>
                <div class="form-group"><label>Item</label><select class="form-control" name="item" required="true"><option value="">Select Item</option><?php foreach ($itemOptions as $itemOption) { ?><option value="<?php echo e($itemOption); ?>"><?php echo e($itemOption); ?></option><?php } ?></select></div>
                <div class="form-group"><label>Cost of Item</label><input class="form-control" type="number" step="0.01" min="0" name="costitem" required="true"></div>
                <div class="form-group"><label>Currency</label><select class="form-control" name="currency" id="currency-select" required="true" onchange="toggleOtherCurrency(this.value)"><?php foreach($currencyOptions as $currencyOption) { ?><option value="<?php echo e($currencyOption); ?>" <?php if($currencyOption === $selectedCurrencyValue) { echo 'selected'; } ?>><?php echo e($currencyOption); ?></option><?php } ?><option value="OTHER" <?php if($selectedCurrencyValue === 'OTHER') { echo 'selected'; } ?>>Other</option></select><input class="form-control" type="text" name="othercurrency" id="other-currency" value="<?php echo e($otherCurrencyValue); ?>" placeholder="Enter currency code" maxlength="10" style="margin-top:8px; display:none;"></div>
                <div class="form-group has-success"><button type="submit" class="btn btn-primary" name="submit">Add</button></div>
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
toggleOtherCurrency(document.getElementById('currency-select').value);
</script>
</body>
</html>
