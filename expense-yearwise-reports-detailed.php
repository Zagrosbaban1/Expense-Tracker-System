<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else{
if(!function_exists('usd_after')){
  function usd_after($amount){
    return number_format((float)$amount, 2).' $';
  }
}

  

  ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Daily Expense Tracker || Yearwise Expense Report</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet">
	<link href="css/datepicker3.css" rel="stylesheet">
	<link href="css/styles.css" rel="stylesheet">
	
	<!--Custom Font-->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
	
</head>
<body>
	<?php include_once('includes/header.php');?>
	<?php include_once('includes/sidebar.php');?>
		
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#">
					<em class="fa fa-home"></em>
				</a></li>
				<li class="active">Yearwise Expense Report</li>
			</ol>
		</div><!--/.row-->
		
		
				
		
		<div class="row">
			<div class="col-lg-12">
			
				
				
				<div class="panel panel-default">
					<div class="panel-heading">Yearwise Expense Report</div>
					<div class="panel-body">

						<div class="col-md-12">
					
<?php
$fdate = $_POST['fromdate'];
$tdate = $_POST['todate'];
$labels = array();
$values = array();
$rows = array();
$totalsexp = 0;
$cnt = 1;
$userid = $_SESSION['detsuid'];

$ret = mysqli_query($con, "SELECT year(ExpenseDate) as rptyear,SUM(ExpenseCost) as totalyear FROM tblexpense where (ExpenseDate BETWEEN '$fdate' and '$tdate') && (UserId='$userid') group by year(ExpenseDate) order by year(ExpenseDate) asc");
while ($row = mysqli_fetch_array($ret)) {
  $rows[] = $row;
  $labels[] = $row['rptyear'];
  $values[] = (float)$row['totalyear'];
  $totalsexp += (float)$row['totalyear'];
}
$recordCount = count($rows);
?>
<div class="report-header">
  <h4 class="color-blue">Yearwise Expense Report</h4>
  <p>From <strong><?php echo $fdate; ?></strong> to <strong><?php echo $tdate; ?></strong></p>
</div>

<div class="row report-metrics">
  <div class="col-sm-4">
    <div class="panel panel-default metric-card">
      <div class="panel-body">
        <p>Total Expense</p>
        <h3><?php echo usd_after($totalsexp); ?></h3>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="panel panel-default metric-card">
      <div class="panel-body">
        <p>Years Count</p>
        <h3><?php echo $recordCount; ?></h3>
      </div>
    </div>
  </div>
  <div class="col-sm-4">
    <div class="panel panel-default metric-card">
      <div class="panel-body">
        <p>Average / Year</p>
        <h3><?php echo $recordCount > 0 ? usd_after($totalsexp / $recordCount) : usd_after(0); ?></h3>
      </div>
    </div>
  </div>
</div>

<?php if ($recordCount > 0) { ?>
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Expense Trend (Line)</div>
      <div class="panel-body">
        <canvas id="yearLineChart" height="140"></canvas>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Expense Comparison (Bar)</div>
      <div class="panel-body">
        <canvas id="yearBarChart" height="140"></canvas>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div class="table-responsive">
  <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
    <thead>
      <tr>
        <th>S.NO</th>
        <th>Year</th>
        <th>Expense Amount</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $row) { ?>
      <tr>
        <td><?php echo $cnt; ?></td>
        <td><?php echo $row['rptyear']; ?></td>
        <td><?php echo usd_after($row['totalyear']); ?></td>
      </tr>
      <?php $cnt = $cnt + 1; } ?>
      <tr>
        <th colspan="2" style="text-align:center">Grand Total</th>
        <td><?php echo usd_after($totalsexp); ?></td>
      </tr>
    </tbody>
  </table>
</div>




						</div>
					</div>
				</div><!-- /.panel-->
			</div><!-- /.col-->
			<?php include_once('includes/footer.php');?>
		</div><!-- /.row -->
	</div><!--/.main-->
	
<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/chart.min.js"></script>
	<script src="js/chart-data.js"></script>
	<script src="js/easypiechart.js"></script>
	<script src="js/easypiechart-data.js"></script>
	<script src="js/bootstrap-datepicker.js"></script>
	<script src="js/custom.js"></script>
	<script>
		(function() {
			var labels = <?php echo json_encode($labels); ?>;
			var values = <?php echo json_encode($values); ?>;
			if (!labels.length) return;

			var lineData = {
				labels: labels,
				datasets: [{
					fillColor: "rgba(13,143,255,0.18)",
					strokeColor: "rgba(13,143,255,1)",
					pointColor: "rgba(13,143,255,1)",
					pointStrokeColor: "#fff",
					data: values
				}]
			};

			var barData = {
				labels: labels,
				datasets: [{
					fillColor: "rgba(239,75,95,0.78)",
					strokeColor: "rgba(239,75,95,1)",
					highlightFill: "rgba(239,75,95,1)",
					highlightStroke: "rgba(239,75,95,1)",
					data: values
				}]
			};

			var lineCtx = document.getElementById("yearLineChart");
			var barCtx = document.getElementById("yearBarChart");
			if (lineCtx) {
				new Chart(lineCtx.getContext("2d")).Line(lineData, { responsive: true, bezierCurve: false });
			}
			if (barCtx) {
				new Chart(barCtx.getContext("2d")).Bar(barData, {
					responsive: true,
					barShowStroke: false,
					animation: true,
					animationSteps: 60,
					animationEasing: "easeOutQuart",
					onAnimationComplete: function () {
						var ctx = this.chart.ctx;
						ctx.font = "12px Arial";
						ctx.fillStyle = "#1b2b43";
						ctx.textAlign = "center";
						ctx.textBaseline = "bottom";
						this.datasets[0].bars.forEach(function (bar) {
							ctx.fillText(bar.value.toFixed(2) + " $", bar.x, bar.y - 4);
						});
					}
				});
			}
		})();
	</script>
	
</body>
</html>
<?php } ?>
