<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else{
if(!function_exists('currency_symbol')){
  function currency_symbol($currency){
    $symbols=array(
      'USD'=>'$',
      'EUR'=>'€',
      'IQD'=>'IQD',
      'GBP'=>'£',
      'AED'=>'AED',
      'SAR'=>'SAR'
    );
    return isset($symbols[$currency]) ? $symbols[$currency] : $currency;
  }
}
if(!function_exists('money_after')){
  function money_after($amount,$currency){
    return number_format((float)$amount, 2).' '.currency_symbol($currency);
  }
}
$userid=$_SESSION['detsuid'];
$currencyOptions=array('USD','EUR','IQD','GBP','AED','SAR');
$selectedCurrency=isset($_GET['cur']) ? strtoupper($_GET['cur']) : 'USD';
if(!in_array($selectedCurrency,$currencyOptions)){
  $selectedCurrency='USD';
}

$currencyColumn=mysqli_query($con,"SHOW COLUMNS FROM tblexpense LIKE 'Currency'");
if(mysqli_num_rows($currencyColumn)==0){
  mysqli_query($con,"ALTER TABLE tblexpense ADD COLUMN Currency varchar(10) NOT NULL DEFAULT 'USD' AFTER ExpenseCost");
}

// Today Expense
$tdate=date('Y-m-d');
$query=mysqli_query($con,"select sum(ExpenseCost) as todaysexpense from tblexpense where (ExpenseDate)='$tdate' && (UserId='$userid') && (Currency='$selectedCurrency');");
$result=mysqli_fetch_array($query);
$sum_today_expense=$result['todaysexpense'];

// Yesterday Expense
$ydate=date('Y-m-d',strtotime("-1 days"));
$query1=mysqli_query($con,"select sum(ExpenseCost) as yesterdayexpense from tblexpense where (ExpenseDate)='$ydate' && (UserId='$userid') && (Currency='$selectedCurrency');");
$result1=mysqli_fetch_array($query1);
$sum_yesterday_expense=$result1['yesterdayexpense'];

// Weekly Expense
$pastdate=date("Y-m-d", strtotime("-1 week"));
$crrntdte=date("Y-m-d");
$query2=mysqli_query($con,"select sum(ExpenseCost) as weeklyexpense from tblexpense where ((ExpenseDate) between '$pastdate' and '$crrntdte') && (UserId='$userid') && (Currency='$selectedCurrency');");
$result2=mysqli_fetch_array($query2);
$sum_weekly_expense=$result2['weeklyexpense'];

// Monthly Expense
$monthdate=date("Y-m-d", strtotime("-1 month"));
$query3=mysqli_query($con,"select sum(ExpenseCost) as monthlyexpense from tblexpense where ((ExpenseDate) between '$monthdate' and '$crrntdte') && (UserId='$userid') && (Currency='$selectedCurrency');");
$result3=mysqli_fetch_array($query3);
$sum_monthly_expense=$result3['monthlyexpense'];

// Yearly Expense
$cyear=date("Y");
$query4=mysqli_query($con,"select sum(ExpenseCost) as yearlyexpense from tblexpense where (year(ExpenseDate)='$cyear') && (UserId='$userid') && (Currency='$selectedCurrency');");
$result4=mysqli_fetch_array($query4);
$sum_yearly_expense=$result4['yearlyexpense'];

// Total Expense
$query5=mysqli_query($con,"select sum(ExpenseCost) as totalexpense from tblexpense where UserId='$userid' && Currency='$selectedCurrency';");
$result5=mysqli_fetch_array($query5);
$sum_total_expense=$result5['totalexpense'];

// Totals by currency
$currencyTotals=array();
foreach($currencyOptions as $cur){
  $currencyTotals[$cur]=0;
}
$currencyTotalsQuery=mysqli_query($con,"SELECT Currency, SUM(ExpenseCost) as total FROM tblexpense WHERE UserId='$userid' GROUP BY Currency");
while($crow=mysqli_fetch_array($currencyTotalsQuery)){
  $curCode=strtoupper(trim($crow['Currency']));
  $curTotal=(float)$crow['total'];
  $currencyTotals[$curCode]=$curTotal;
}

// Dashboard analytics: current month only
$selectedPeriodStart=date('Y-m-01');
$selectedPeriodEnd=date('Y-m-t',strtotime($selectedPeriodStart));
$selectedMonthLabel=date('F Y',strtotime($selectedPeriodStart));

$dayMap=array();
$dayLabels=array();
$dayValues=array();
$dayQuery=mysqli_query($con,"SELECT DAY(ExpenseDate) as d, SUM(ExpenseCost) as total FROM tblexpense WHERE UserId='$userid' AND Currency='$selectedCurrency' AND ExpenseDate BETWEEN '$selectedPeriodStart' AND '$selectedPeriodEnd' GROUP BY DAY(ExpenseDate) ORDER BY DAY(ExpenseDate) ASC");
while($drow=mysqli_fetch_array($dayQuery)){
  $dayMap[(int)$drow['d']]=(float)$drow['total'];
}
$daysInSelectedMonth=(int)date('t',strtotime($selectedPeriodStart));
for($d=1;$d<=$daysInSelectedMonth;$d++){
  $dayLabels[]=(string)$d;
  $dayValues[]=isset($dayMap[$d]) ? $dayMap[$d] : 0;
}

$topItemLabels=array();
$topItemValues=array();
$topItemsQuery=mysqli_query($con,"SELECT ExpenseItem, SUM(ExpenseCost) as total FROM tblexpense WHERE UserId='$userid' AND Currency='$selectedCurrency' AND ExpenseDate BETWEEN '$selectedPeriodStart' AND '$selectedPeriodEnd' GROUP BY ExpenseItem ORDER BY total DESC LIMIT 5");
while($irow=mysqli_fetch_array($topItemsQuery)){
  $topItemLabels[]=$irow['ExpenseItem'];
  $topItemValues[]=(float)$irow['total'];
}

$activeDaysQuery=mysqli_query($con,"SELECT COUNT(DISTINCT ExpenseDate) as activeDays FROM tblexpense WHERE UserId='$userid' AND Currency='$selectedCurrency' AND ExpenseDate BETWEEN '$selectedPeriodStart' AND '$selectedPeriodEnd'");
$activeDaysResult=mysqli_fetch_array($activeDaysQuery);
$activeDays=(int)$activeDaysResult['activeDays'];
$selectedMonthTotal=array_sum($dayValues);
$avgPerActiveDay=$activeDays>0 ? ($selectedMonthTotal/$activeDays) : 0;

$maxSingleQuery=mysqli_query($con,"SELECT MAX(ExpenseCost) as maxsingle FROM tblexpense WHERE UserId='$userid' AND Currency='$selectedCurrency'");
$maxSingleResult=mysqli_fetch_array($maxSingleQuery);
$maxSingleExpense=(float)$maxSingleResult['maxsingle'];

$latestQuery=mysqli_query($con,"SELECT ExpenseItem,ExpenseCost,ExpenseDate FROM tblexpense WHERE UserId='$userid' AND Currency='$selectedCurrency' ORDER BY ExpenseDate DESC, ID DESC LIMIT 1");
$latestResult=mysqli_fetch_array($latestQuery);
$latestItem=$latestResult['ExpenseItem'];
$latestCost=$latestResult['ExpenseCost'];
$latestDate=$latestResult['ExpenseDate'];

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
<body class="app-page dashboard-page">
	
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
		
		
		
		
		<div class="row stats-row">
			<div class="col-xs-12 col-sm-6 col-lg-4">
				
				<div class="panel panel-default stat-panel stat-panel-blue">
					<div class="panel-body easypiechart-panel">
						<h4 class="stat-title">Today's Expense</h4>
						<div class="stat-number"><?php if($sum_today_expense==""){ echo money_after(0,$selectedCurrency); } else { echo money_after($sum_today_expense,$selectedCurrency); } ?></div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-4">
				<div class="panel panel-default stat-panel stat-panel-orange">
					<div class="panel-body easypiechart-panel">
						<h4 class="stat-title">Yesterday's Expense</h4>
						<div class="stat-number"><?php if($sum_yesterday_expense==""){ echo money_after(0,$selectedCurrency); } else { echo money_after($sum_yesterday_expense,$selectedCurrency); } ?></div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-4">
				<div class="panel panel-default stat-panel stat-panel-teal">
					<div class="panel-body easypiechart-panel">
						<h4 class="stat-title">Last 7day's Expense</h4>
						<div class="stat-number"><?php if($sum_weekly_expense==""){ echo money_after(0,$selectedCurrency); } else { echo money_after($sum_weekly_expense,$selectedCurrency); } ?></div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-4">
				<div class="panel panel-default stat-panel stat-panel-red">
					<div class="panel-body easypiechart-panel">
						<h4 class="stat-title">Last 30day's Expenses</h4>
						<div class="stat-number"><?php if($sum_monthly_expense==""){ echo money_after(0,$selectedCurrency); } else { echo money_after($sum_monthly_expense,$selectedCurrency); } ?></div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-4">
				<div class="panel panel-default stat-panel stat-panel-violet">
					<div class="panel-body easypiechart-panel">
						<h4 class="stat-title">Current Year Expenses</h4>
						<div class="stat-number"><?php if($sum_yearly_expense==""){ echo money_after(0,$selectedCurrency); } else { echo money_after($sum_yearly_expense,$selectedCurrency); } ?></div>


					</div>
				
				</div>

			</div>
			<div class="col-xs-12 col-sm-6 col-lg-4">
				<div class="panel panel-default stat-panel stat-panel-cyan">
					<div class="panel-body easypiechart-panel">
						<h4 class="stat-title">Total Expenses</h4>
						<div class="stat-number"><?php if($sum_total_expense==""){ echo money_after(0,$selectedCurrency); } else { echo money_after($sum_total_expense,$selectedCurrency); } ?></div>


					</div>
				
				</div>

			</div>


		</div><!--/.row-->

		<div class="row analytics-row">
			<div class="col-md-8">
				<div class="panel panel-default analytics-panel">
					<div class="panel-heading">Day by Day - <?php echo $selectedMonthLabel; ?> (<?php echo $selectedCurrency; ?>)</div>
					<div class="panel-body">
						<form method="get" action="dashboard.php" class="analytics-filter form-inline">
							<div class="form-group">
								<label for="cur">Currency</label>
								<select name="cur" id="cur" class="form-control">
									<?php foreach($currencyOptions as $cur){ ?>
									<option value="<?php echo $cur; ?>" <?php if($selectedCurrency==$cur){ echo "selected"; } ?>><?php echo $cur; ?></option>
									<?php } ?>
								</select>
							</div>
							<button type="submit" class="btn btn-primary btn-sm">Apply</button>
						</form>
						<div class="analytics-chart-wrap">
							<canvas id="dailyTrendChart" class="trend-chart" height="110"></canvas>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-default analytics-panel">
					<div class="panel-heading">Top Spend By Categories - <?php echo $selectedMonthLabel; ?> (<?php echo $selectedCurrency; ?>)</div>
					<div class="panel-body">
						<?php if(count($topItemLabels)>0){ ?>
						<div class="analytics-chart-wrap">
							<canvas id="topItemsBarChart" class="category-chart" height="235"></canvas>
						</div>
						<?php } else { ?>
						<p class="text-muted">No expense data yet to show category analytics.</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="row analytics-row">
			<div class="col-sm-4">
				<div class="panel panel-default insight-card">
					<div class="panel-body">
						<p class="insight-label">Monthly Active Days</p>
						<h3 class="insight-value"><?php echo $activeDays; ?></h3>
						<p class="text-muted">Days you logged expenses in <?php echo $selectedMonthLabel; ?></p>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="panel panel-default insight-card">
					<div class="panel-body">
						<p class="insight-label">Avg Per Active Day</p>
						<h3 class="insight-value"><?php echo money_after($avgPerActiveDay,$selectedCurrency); ?></h3>
						<p class="text-muted">Average spend on active days in <?php echo $selectedMonthLabel; ?></p>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="panel panel-default insight-card">
					<div class="panel-body">
						<p class="insight-label">Highest Single Expense</p>
						<h3 class="insight-value"><?php echo money_after($maxSingleExpense,$selectedCurrency); ?></h3>
						<p class="text-muted"><?php if($latestItem!=""){ echo "Latest: ".$latestItem." (".money_after($latestCost,$selectedCurrency).") on ".$latestDate; } else { echo "No expenses yet"; } ?></p>
					</div>
				</div>
			</div>
		</div>

		<div class="row analytics-row">
			<div class="col-md-12">
				<div class="panel panel-default analytics-panel">
					<div class="panel-heading">Totals by Currency</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Currency</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($currencyTotals as $cur=>$total){ ?>
									<tr>
										<td><?php echo $cur; ?></td>
										<td><?php echo money_after($total,$cur); ?></td>
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
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/custom.js"></script>
	<script>
		(function () {
			var dayLabels = <?php echo json_encode($dayLabels); ?>;
			var dayValues = <?php echo json_encode($dayValues); ?>;
			var topItemLabels = <?php echo json_encode($topItemLabels); ?>;
			var topItemValues = <?php echo json_encode($topItemValues); ?>;

			var dailyCanvas = document.getElementById("dailyTrendChart");
			if (dailyCanvas) {
				var dailyData = {
					labels: dayLabels,
					datasets: [{
						fillColor: "rgba(13,143,255,0.18)",
						strokeColor: "rgba(13,143,255,1)",
						pointColor: "rgba(13,143,255,1)",
						pointStrokeColor: "#fff",
						data: dayValues
					}]
				};
				new Chart(dailyCanvas.getContext("2d")).Line(dailyData, {
					responsive: true,
					bezierCurve: false,
					scaleGridLineColor: "rgba(0,0,0,.06)"
				});
			}

			var itemsCanvas = document.getElementById("topItemsBarChart");
			if (itemsCanvas && topItemLabels.length) {
				var itemsData = {
					labels: topItemLabels,
					datasets: [{
						fillColor: "rgba(31,185,129,0.78)",
						strokeColor: "rgba(31,185,129,1)",
						highlightFill: "rgba(31,185,129,1)",
						highlightStroke: "rgba(31,185,129,1)",
						data: topItemValues
					}]
				};
				new Chart(itemsCanvas.getContext("2d")).Bar(itemsData, {
					responsive: true,
					barShowStroke: false
				});
			}
		})();
	</script>
		
</body>
</html>
<?php } ?>
