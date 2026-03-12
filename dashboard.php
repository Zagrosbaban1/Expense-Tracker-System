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
$topChartColors=array('#18c7b8', '#2563eb', '#f59e0b', '#fb7185', '#0f172a');
$topCategoryTotal=array_sum($topItemValues);
$topCategorySegments=array();
if($topCategoryTotal>0){
  $circumference=2*pi()*84;
  $offset=0;
  foreach($topItemValues as $index=>$value){
    $segmentLength=($value/$topCategoryTotal)*$circumference;
    $topCategorySegments[]=array(
      'label'=>$topItemLabels[$index],
      'value'=>$value,
      'color'=>$topChartColors[$index % count($topChartColors)],
      'dash'=>$segmentLength,
      'gap'=>$circumference-$segmentLength,
      'offset'=>-$offset
    );
    $offset+=$segmentLength;
  }
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
	<style>
		.dashboard-shell {
			padding-top: 22px;
			padding-bottom: 30px;
			background: linear-gradient(180deg, #f7fafc 0%, #eef3f8 100%);
			min-height: 100vh;
		}
		.dashboard-hero,
		.dashboard-card {
			background: #ffffff;
			border: 1px solid #dbe4ee;
			border-radius: 18px;
			box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
		}
		.dashboard-hero {
			padding: 28px;
			margin-bottom: 24px;
			background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
			color: #fff;
			border: 0;
		}
		.dashboard-hero h1 {
			margin: 0 0 8px;
			font-size: 30px;
			font-weight: 700;
			color: #ffffff;
		}
		.dashboard-hero p {
			margin: 0;
			color: rgba(255, 255, 255, 0.82);
			font-size: 14px;
		}
		.hero-meta {
			display: inline-block;
			margin-top: 16px;
			padding: 9px 14px;
			border-radius: 999px;
			background: rgba(255, 255, 255, 0.14);
			font-size: 12px;
			font-weight: 600;
			letter-spacing: .04em;
			text-transform: uppercase;
		}
		.currency-form {
			margin-top: 18px;
		}
		.currency-form .form-control,
		.currency-form .btn {
			height: 42px;
			border-radius: 12px;
			box-shadow: none;
			border: 0;
		}
		.currency-form .form-control {
			background: rgba(255, 255, 255, 0.94);
			color: #0f172a;
		}
		.currency-form .btn {
			background: #fff;
			color: #1d4ed8;
			font-weight: 700;
		}
		.metric-card {
			padding: 20px;
			margin-bottom: 20px;
		}
		.metric-label {
			margin: 0 0 8px;
			font-size: 12px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .08em;
			color: #64748b;
		}
		.metric-value {
			margin: 0;
			font-size: 28px;
			font-weight: 700;
			color: #0f172a;
		}
		.metric-note {
			margin-top: 10px;
			color: #64748b;
			font-size: 13px;
		}
		.dashboard-card {
			padding: 22px;
			margin-bottom: 22px;
		}
		.section-title {
			margin: 0 0 6px;
			font-size: 18px;
			font-weight: 700;
			color: #0f172a;
		}
		.section-copy {
			margin: 0 0 18px;
			color: #64748b;
			font-size: 13px;
		}
		.quick-grid {
			display: grid;
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 14px;
		}
		.quick-stat {
			padding: 16px;
			border-radius: 14px;
			background: #f8fafc;
			border: 1px solid #e2e8f0;
		}
		.quick-stat strong {
			display: block;
			font-size: 22px;
			color: #0f172a;
		}
		.quick-stat span {
			display: block;
			margin-bottom: 8px;
			font-size: 12px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .08em;
			color: #64748b;
		}
		.chart-box {
			position: relative;
			height: 320px;
			width: 100%;
			overflow: hidden;
		}
		.chart-box.chart-box-small {
			height: 260px;
		}
		.doughnut-panel {
			padding: 24px 22px 18px;
		}
		.doughnut-panel .section-copy {
			margin-bottom: 12px;
		}
		.donut-card {
			padding: 10px 8px 2px;
			border-radius: 18px;
			background: radial-gradient(circle at top, #f8fbff 0%, #ffffff 62%);
		}
		.doughnut-wrap {
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 4px 0 0;
		}
		.donut-chart {
			position: relative;
			width: 220px;
			height: 220px;
			margin: 0 auto;
		}
		.donut-chart svg {
			width: 100%;
			height: 100%;
			transform: rotate(-90deg);
			filter: drop-shadow(0 12px 20px rgba(15, 23, 42, 0.08));
		}
		.donut-track {
			fill: none;
			stroke: #e7eef7;
			stroke-width: 22;
		}
		.donut-segment {
			fill: none;
			stroke-width: 22;
			stroke-linecap: round;
			cursor: pointer;
			transition: opacity .2s ease, stroke-width .2s ease, filter .2s ease;
			animation: donut-grow .9s ease both;
		}
		.donut-chart:hover .donut-segment {
			opacity: .45;
		}
		.donut-chart .donut-segment:hover {
			opacity: 1;
			stroke-width: 26;
			filter: brightness(1.04);
		}
		.donut-center {
			position: absolute;
			left: 50%;
			top: 50%;
			width: 118px;
			height: 118px;
			margin-left: -59px;
			margin-top: -59px;
			border-radius: 50%;
			background: #ffffff;
			box-shadow: inset 0 0 0 1px #e2e8f0;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			text-align: center;
			padding: 12px;
		}
		.donut-center-label {
			font-size: 11px;
			font-weight: 700;
			letter-spacing: .08em;
			text-transform: uppercase;
			color: #64748b;
		}
		.donut-center-value {
			margin-top: 7px;
			font-size: 28px;
			font-weight: 700;
			line-height: 1;
			color: #0f172a;
		}
		.chart-box canvas {
			display: block;
			width: 100% !important;
			max-width: 100%;
			height: 100% !important;
		}
		.chart-legend {
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 8px 12px;
			margin: 16px auto 0;
			padding: 0;
			list-style: none;
			max-width: 320px;
		}
		.chart-legend li {
			display: inline-flex;
			align-items: center;
			color: #475569;
			font-size: 12px;
			line-height: 1.4;
			padding: 4px 0;
		}
		.chart-legend .legend-value {
			margin-left: 6px;
			font-weight: 700;
			color: #0f172a;
		}
		.chart-legend .legend-dot {
			display: inline-block;
			width: 10px;
			height: 10px;
			margin-right: 6px;
			border-radius: 50%;
			vertical-align: middle;
		}
		.latest-expense {
			padding: 18px;
			border-radius: 16px;
			background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
			border: 1px solid #dbeafe;
		}
		.latest-expense .item-name {
			margin: 0 0 8px;
			font-size: 22px;
			font-weight: 700;
			color: #0f172a;
		}
		.latest-expense .item-meta,
		.latest-expense .item-date {
			margin: 0;
			color: #475569;
		}
		.currency-table {
			margin-bottom: 0;
		}
		.currency-table > thead > tr > th,
		.currency-table > tbody > tr > td {
			border-top: 1px solid #e2e8f0;
			padding: 14px 12px;
		}
		.currency-table > thead > tr > th {
			border-top: 0;
			font-size: 12px;
			text-transform: uppercase;
			letter-spacing: .08em;
			color: #64748b;
		}
		.empty-state {
			margin: 0;
			padding: 22px 0;
			text-align: center;
			color: #64748b;
		}
		@keyframes donut-grow {
			from {
				stroke-dasharray: 0 528;
			}
		}
		@media (max-width: 767px) {
			.dashboard-hero {
				padding: 22px;
			}
			.dashboard-hero h1 {
				font-size: 24px;
			}
			.metric-value {
				font-size: 24px;
			}
			.quick-grid {
				grid-template-columns: 1fr;
			}
			.chart-box,
			.chart-box.chart-box-small {
				height: 240px;
			}
			.doughnut-panel {
				padding: 20px 18px 16px;
			}
			.donut-chart {
				width: 190px;
				height: 190px;
			}
			.donut-center {
				width: 102px;
				height: 102px;
				margin-left: -51px;
				margin-top: -51px;
			}
			.donut-center-value {
				font-size: 22px;
			}
			.chart-legend {
				max-width: 100%;
				gap: 6px 10px;
			}
		}
	</style>
	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="app-page dashboard-page">
	
	<?php include_once('includes/header.php');?>
	<?php include_once('includes/sidebar.php');?>
		
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main dashboard-shell">
		<div class="dashboard-hero">
			<div class="row">
				<div class="col-md-7">
					<h1>Expense overview</h1>
					<p>All in one dashboard</p>
					<div class="hero-meta"><?php echo $selectedMonthLabel; ?> • <?php echo $selectedCurrency; ?></div>
				</div>
				<div class="col-md-5">
					<form method="get" action="dashboard.php" class="currency-form form-inline text-right">
						<div class="form-group">
							<select name="cur" id="cur" class="form-control">
								<?php foreach($currencyOptions as $cur){ ?>
								<option value="<?php echo $cur; ?>" <?php if($selectedCurrency==$cur){ echo "selected"; } ?>><?php echo $cur; ?></option>
								<?php } ?>
							</select>
						</div>
						<button type="submit" class="btn">Change Currency</button>
					</form>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-6 col-lg-3">
				<div class="dashboard-card metric-card">
					<p class="metric-label">Today</p>
					<h3 class="metric-value"><?php echo money_after($sum_today_expense, $selectedCurrency); ?></h3>
					<p class="metric-note">Spent on <?php echo date('F j, Y'); ?></p>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="dashboard-card metric-card">
					<p class="metric-label">Last 7 Days</p>
					<h3 class="metric-value"><?php echo money_after($sum_weekly_expense, $selectedCurrency); ?></h3>
					<p class="metric-note">Rolling weekly total</p>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="dashboard-card metric-card">
					<p class="metric-label">This Month</p>
					<h3 class="metric-value"><?php echo money_after($sum_monthly_expense, $selectedCurrency); ?></h3>
					<p class="metric-note">Last 30 days of spending</p>
				</div>
			</div>
			<div class="col-sm-6 col-lg-3">
				<div class="dashboard-card metric-card">
					<p class="metric-label">Total</p>
					<h3 class="metric-value"><?php echo money_after($sum_total_expense, $selectedCurrency); ?></h3>
					<p class="metric-note">All records in <?php echo $selectedCurrency; ?></p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-8">
				<div class="dashboard-card">
					<h2 class="section-title">Daily trend</h2>
					<p class="section-copy">Your spending pattern across <?php echo $selectedMonthLabel; ?>.</p>
					<div class="chart-box">
						<canvas id="dailyTrendChart"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="dashboard-card">
					<h2 class="section-title">Quick stats</h2>
					<p class="section-copy">Small signals to read your month faster.</p>
					<div class="quick-grid">
						<div class="quick-stat">
							<span>Yesterday</span>
							<strong><?php echo money_after($sum_yesterday_expense, $selectedCurrency); ?></strong>
						</div>
						<div class="quick-stat">
							<span>This Year</span>
							<strong><?php echo money_after($sum_yearly_expense, $selectedCurrency); ?></strong>
						</div>
						<div class="quick-stat">
							<span>Active Days</span>
							<strong><?php echo $activeDays; ?></strong>
						</div>
						<div class="quick-stat">
							<span>Avg / Active Day</span>
							<strong><?php echo money_after($avgPerActiveDay, $selectedCurrency); ?></strong>
						</div>
					</div>
				</div>
				<div class="dashboard-card">
					<h2 class="section-title">Latest expense</h2>
					<p class="section-copy">Most recent entry in <?php echo $selectedCurrency; ?>.</p>
					<div class="latest-expense">
						<?php if($latestItem!=""){ ?>
						<p class="item-name"><?php echo htmlentities($latestItem); ?></p>
						<p class="item-meta"><?php echo money_after($latestCost, $selectedCurrency); ?></p>
						<p class="item-date"><?php echo date('F j, Y', strtotime($latestDate)); ?></p>
						<?php } else { ?>
						<p class="item-name">No expenses yet</p>
						<p class="item-meta">Add your first expense to start tracking.</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-5">
				<div class="dashboard-card doughnut-panel">
					<h2 class="section-title">Top categories</h2>
					<p class="section-copy">Highest spending categories this month.</p>
					<?php if(count($topItemLabels)>0){ ?>
					<div class="donut-card">
						<div class="doughnut-wrap">
							<div class="donut-chart">
								<svg viewBox="0 0 220 220" aria-hidden="true">
									<circle class="donut-track" cx="110" cy="110" r="84"></circle>
									<?php foreach($topCategorySegments as $segment){ ?>
									<circle class="donut-segment" cx="110" cy="110" r="84" stroke="<?php echo $segment['color']; ?>" stroke-dasharray="<?php echo $segment['dash']; ?> <?php echo $segment['gap']; ?>" stroke-dashoffset="<?php echo $segment['offset']; ?>">
										<title><?php echo htmlentities($segment['label']); ?>: <?php echo money_after($segment['value'], $selectedCurrency); ?></title>
									</circle>
									<?php } ?>
								</svg>
								<div class="donut-center">
									<span class="donut-center-label">Top Spend</span>
									<span class="donut-center-value"><?php echo number_format($topCategoryTotal, 0); ?></span>
								</div>
							</div>
						</div>
					</div>
					<ul class="chart-legend">
						<?php foreach($topCategorySegments as $segment){ ?>
						<li>
							<span class="legend-dot" style="background: <?php echo $segment['color']; ?>;"></span>
							<?php echo htmlentities($segment['label']); ?>
							<span class="legend-value"><?php echo money_after($segment['value'], $selectedCurrency); ?></span>
						</li>
						<?php } ?>
					</ul>
					<?php } else { ?>
					<p class="empty-state">No category data yet for this month.</p>
					<?php } ?>
				</div>
			</div>
			<div class="col-md-7">
				<div class="dashboard-card">
					<h2 class="section-title">Totals by currency</h2>
					<p class="section-copy">Useful if you track expenses in more than one currency.</p>
					<div class="table-responsive">
						<table class="table currency-table">
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

			var dailyCanvas = document.getElementById("dailyTrendChart");
			if (dailyCanvas) {
				var dailyData = {
					labels: dayLabels,
					datasets: [{
						fillColor: "rgba(37, 99, 235, 0.14)",
						strokeColor: "rgba(37, 99, 235, 1)",
						pointColor: "rgba(37, 99, 235, 1)",
						pointStrokeColor: "#fff",
						data: dayValues
					}]
				};
				new Chart(dailyCanvas.getContext("2d")).Line(dailyData, {
					responsive: true,
					bezierCurve: false,
					scaleGridLineColor: "rgba(148,163,184,.18)"
				});
			}

		})();
	</script>
		
</body>
</html>
<?php } ?>
