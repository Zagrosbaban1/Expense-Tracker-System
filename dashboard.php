<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else{
  $userid=$_SESSION['detsuid'];
  function formatAmountByCurrency($amount, $currencyCode) {
    if ($amount === null || $amount === '') {
      return $currencyCode . ' 0';
    }
    return $currencyCode . ' ' . $amount;
  }

  $currencyColumnQuery = mysqli_query($con, "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tblexpense' AND COLUMN_NAME = 'ExpenseCurrency' LIMIT 1");
  if ($currencyColumnQuery && mysqli_num_rows($currencyColumnQuery) == 0) {
    mysqli_query($con, "ALTER TABLE tblexpense ADD COLUMN ExpenseCurrency VARCHAR(10) NOT NULL DEFAULT 'USD'");
  }

  $currencyOptions = array('USD');
  $currencyListQuery = mysqli_query($con, "select distinct ifnull(nullif(ExpenseCurrency,''),'USD') as Currency from tblexpense where UserId='$userid' order by Currency asc");
  if ($currencyListQuery) {
    while ($currencyListRow = mysqli_fetch_assoc($currencyListQuery)) {
      $currencyCode = strtoupper(trim($currencyListRow['Currency']));
      if ($currencyCode !== '' && !in_array($currencyCode, $currencyOptions, true)) {
        $currencyOptions[] = $currencyCode;
      }
    }
  }
  sort($currencyOptions);

  $selectedCurrency = 'USD';
  if (isset($_GET['currency'])) {
    $requestedCurrency = strtoupper(trim($_GET['currency']));
    if (in_array($requestedCurrency, $currencyOptions, true)) {
      $selectedCurrency = $requestedCurrency;
    }
  }

  $currencyFilterSql = " && (ifnull(nullif(ExpenseCurrency,''),'USD')='$selectedCurrency')";

  

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
	
	<!--Custom Font-->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="app-page">
	
	<?php include_once('includes/header.php');?>
	<?php include_once('includes/sidebar.php');?>
		
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#">
					<em class="fa fa-home"></em>
				</a></li>
				<li class="active">Dashboard</li>
			</ol>
		</div><!--/.row-->
		
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Dashboard</h1>
			</div>
		</div><!--/.row-->
		<div class="row">
			<div class="col-lg-4">
				<form method="get" action="">
					<div class="form-group">
						<label>Currency</label>
						<select class="form-control" name="currency" onchange="this.form.submit()">
<?php foreach($currencyOptions as $currencyOption) { ?>
							<option value="<?php echo htmlspecialchars($currencyOption); ?>" <?php if($selectedCurrency == $currencyOption) { echo "selected"; } ?>><?php echo htmlspecialchars($currencyOption); ?></option>
<?php } ?>
						</select>
					</div>
				</form>
			</div>
		</div>
		
		
		
		
		<div class="row">
			<div class="col-xs-6 col-md-3">
				
				<div class="panel panel-default">
					<div class="panel-body easypiechart-panel">
<?php
//Today Expense
$tdate=date('Y-m-d');
$query=mysqli_query($con,"select sum(ExpenseCost)  as todaysexpense from tblexpense where (ExpenseDate)='$tdate' && (UserId='$userid') $currencyFilterSql;");
$result=mysqli_fetch_array($query);
$sum_today_expense=$result['todaysexpense'];
 ?> 

						<h4>Today's Expense (<?php echo htmlspecialchars($selectedCurrency); ?>)</h4>
						<div class="easypiechart" id="easypiechart-blue" data-percent="<?php echo $sum_today_expense;?>" ><span class="percent"><?php echo formatAmountByCurrency($sum_today_expense, $selectedCurrency); ?></span></div>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<?php
//Yestreday Expense
$ydate=date('Y-m-d',strtotime("-1 days"));
$query1=mysqli_query($con,"select sum(ExpenseCost)  as yesterdayexpense from tblexpense where (ExpenseDate)='$ydate' && (UserId='$userid') $currencyFilterSql;");
$result1=mysqli_fetch_array($query1);
$sum_yesterday_expense=$result1['yesterdayexpense'];
 ?> 
					<div class="panel-body easypiechart-panel">
						<h4>Yesterday's Expense (<?php echo htmlspecialchars($selectedCurrency); ?>)</h4>
						<div class="easypiechart" id="easypiechart-orange" data-percent="<?php echo $sum_yesterday_expense;?>" ><span class="percent"><?php echo formatAmountByCurrency($sum_yesterday_expense, $selectedCurrency); ?></span></div>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<?php
//Weekly Expense
$pastdate=  date("Y-m-d", strtotime("-1 week")); 
$crrntdte=date("Y-m-d");
$query2=mysqli_query($con,"select sum(ExpenseCost)  as weeklyexpense from tblexpense where ((ExpenseDate) between '$pastdate' and '$crrntdte') && (UserId='$userid') $currencyFilterSql;");
$result2=mysqli_fetch_array($query2);
$sum_weekly_expense=$result2['weeklyexpense'];
 ?>
					<div class="panel-body easypiechart-panel">
						<h4>Last 7day's Expense (<?php echo htmlspecialchars($selectedCurrency); ?>)</h4>
						<div class="easypiechart" id="easypiechart-teal" data-percent="<?php echo $sum_weekly_expense;?>"><span class="percent"><?php echo formatAmountByCurrency($sum_weekly_expense, $selectedCurrency); ?></span></div>
					</div>
				</div>
			</div>
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<?php
//Monthly Expense
$monthdate=  date("Y-m-d", strtotime("-1 month")); 
$crrntdte=date("Y-m-d");
$query3=mysqli_query($con,"select sum(ExpenseCost)  as monthlyexpense from tblexpense where ((ExpenseDate) between '$monthdate' and '$crrntdte') && (UserId='$userid') $currencyFilterSql;");
$result3=mysqli_fetch_array($query3);
$sum_monthly_expense=$result3['monthlyexpense'];
 ?>
					<div class="panel-body easypiechart-panel">
						<h4>Last 30day's Expenses (<?php echo htmlspecialchars($selectedCurrency); ?>)</h4>
						<div class="easypiechart" id="easypiechart-red" data-percent="<?php echo $sum_monthly_expense;?>" ><span class="percent"><?php echo formatAmountByCurrency($sum_monthly_expense, $selectedCurrency); ?></span></div>
					</div>
				</div>
			</div>
		
		</div><!--/.row-->
			<div class="row">
			<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<?php
//Yearly Expense
 $cyear= date("Y");
$query4=mysqli_query($con,"select sum(ExpenseCost)  as yearlyexpense from tblexpense where (year(ExpenseDate)='$cyear') && (UserId='$userid') $currencyFilterSql;");
$result4=mysqli_fetch_array($query4);
$sum_yearly_expense=$result4['yearlyexpense'];
 ?>
					<div class="panel-body easypiechart-panel">
						<h4>Current Year Expenses (<?php echo htmlspecialchars($selectedCurrency); ?>)</h4>
						<div class="easypiechart" id="easypiechart-red" data-percent="<?php echo $sum_yearly_expense;?>" ><span class="percent"><?php echo formatAmountByCurrency($sum_yearly_expense, $selectedCurrency); ?></span></div>


					</div>
				
				</div>

			</div>

		<div class="col-xs-6 col-md-3">
				<div class="panel panel-default">
					<?php
//Yearly Expense
$query5=mysqli_query($con,"select sum(ExpenseCost)  as totalexpense from tblexpense where UserId='$userid' $currencyFilterSql;");
$result5=mysqli_fetch_array($query5);
$sum_total_expense=$result5['totalexpense'];
 ?>
					<div class="panel-body easypiechart-panel">
						<h4>Total Expenses (<?php echo htmlspecialchars($selectedCurrency); ?>)</h4>
						<div class="easypiechart" id="easypiechart-red" data-percent="<?php echo $sum_total_expense;?>" ><span class="percent"><?php echo formatAmountByCurrency($sum_total_expense, $selectedCurrency); ?></span></div>


					</div>
				
				</div>

			</div>


		</div>
		<div class="row">
			<div class="col-lg-7">
				<div class="panel panel-default">
					<div class="panel-heading">Total By Currency</div>
					<div class="panel-body">
						<p class="text-muted">Use these totals when you have mixed currencies (USD, IQD, etc.).</p>
						<div class="table-responsive">
							<table class="table table-bordered mg-b-0">
								<thead>
									<tr>
										<th>Currency</th>
										<th>Total Amount</th>
										<th>Entries</th>
									</tr>
								</thead>
								<tbody>
<?php
$currencyTotals = mysqli_query($con, "select ifnull(nullif(ExpenseCurrency,''),'USD') as Currency, sum(ExpenseCost) as CurrencyTotal, count(*) as EntryCount from tblexpense where UserId='$userid' group by ifnull(nullif(ExpenseCurrency,''),'USD') order by Currency asc");
if ($currencyTotals && mysqli_num_rows($currencyTotals) > 0) {
  while ($currencyRow = mysqli_fetch_array($currencyTotals)) {
?>
									<tr>
										<td><?php echo htmlspecialchars($currencyRow['Currency']); ?></td>
										<td><?php echo $currencyRow['CurrencyTotal']; ?></td>
										<td><?php echo $currencyRow['EntryCount']; ?></td>
									</tr>
<?php
  }
} else {
?>
									<tr>
										<td colspan="3" class="text-center">No expense records found.</td>
									</tr>
<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="panel panel-default">
					<div class="panel-heading">Recent Expenses With Currency (<?php echo htmlspecialchars($selectedCurrency); ?>)</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered mg-b-0">
								<thead>
									<tr>
										<th>Item</th>
										<th>Amount</th>
										<th>Currency</th>
									</tr>
								</thead>
								<tbody>
<?php
$recentExpenses = mysqli_query($con, "select ExpenseItem, ExpenseCost, ifnull(nullif(ExpenseCurrency,''),'USD') as Currency from tblexpense where UserId='$userid' $currencyFilterSql order by ID desc limit 8");
if ($recentExpenses && mysqli_num_rows($recentExpenses) > 0) {
  while ($recentRow = mysqli_fetch_array($recentExpenses)) {
?>
									<tr>
										<td><?php echo htmlspecialchars($recentRow['ExpenseItem']); ?></td>
										<td><?php echo $recentRow['ExpenseCost']; ?></td>
										<td><?php echo htmlspecialchars($recentRow['Currency']); ?></td>
									</tr>
<?php
  }
} else {
?>
									<tr>
										<td colspan="3" class="text-center">No expense records found.</td>
									</tr>
<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!--/.row-->
	</div>	<!--/.main-->
	<?php include_once('includes/footer.php');?>
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/chart.min.js"></script>
	<script src="js/chart-data.js"></script>
	<script src="js/easypiechart.js"></script>
	<script src="js/easypiechart-data.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/custom.js"></script>
	<script>
		window.onload = function () {
	var chart1 = document.getElementById("line-chart").getContext("2d");
	window.myLine = new Chart(chart1).Line(lineChartData, {
	responsive: true,
	scaleLineColor: "rgba(0,0,0,.2)",
	scaleGridLineColor: "rgba(0,0,0,.05)",
	scaleFontColor: "#c5c7cc"
	});
};
	</script>
		
</body>
</html>
<?php } ?>
