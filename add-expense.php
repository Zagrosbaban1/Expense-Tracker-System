<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else{

$userid = $_SESSION['detsuid'];
$itemOptions = array();

mysqli_query($con, "CREATE TABLE IF NOT EXISTS tblitemmaster (
  ID INT(11) NOT NULL AUTO_INCREMENT,
  UserId INT(11) NOT NULL,
  ItemName VARCHAR(255) NOT NULL,
  CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  UNIQUE KEY uniq_user_item (UserId, ItemName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$itemQuery = mysqli_query($con, "select ItemName from tblitemmaster where UserId='$userid' order by ItemName asc");
if ($itemQuery && mysqli_num_rows($itemQuery) > 0) {
  while ($itemRow = mysqli_fetch_assoc($itemQuery)) {
    $itemOptions[] = $itemRow['ItemName'];
  }
} else {
  $legacyItemQuery = mysqli_query($con, "select distinct ExpenseItem from tblexpense where UserId='$userid' and ExpenseItem<>'' order by ExpenseItem asc");
  if ($legacyItemQuery) {
    while ($legacyItemRow = mysqli_fetch_assoc($legacyItemQuery)) {
      $itemOptions[] = $legacyItemRow['ExpenseItem'];
    }
  }
}

if(isset($_POST['submit']))
  {
  	$userid=$_SESSION['detsuid'];
    $dateexpense=$_POST['dateexpense'];
     $item=$_POST['item'];
     $costitem=$_POST['costitem'];
    $query=mysqli_query($con, "insert into tblexpense(UserId,ExpenseDate,ExpenseItem,ExpenseCost) value('$userid','$dateexpense','$item','$costitem')");
if($query){
echo "<script>alert('Expense has been added');</script>";
echo "<script>window.location.href='manage-expense.php'</script>";
} else {
echo "<script>alert('Something went wrong. Please try again');</script>";

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
				<li class="active">Expense</li>
			</ol>
		</div><!--/.row-->
		
		
				
		
		<div class="row">
			<div class="col-lg-12">
			
				
				
				<div class="panel panel-default">
					<div class="panel-heading">Expense</div>
					<div class="panel-body">
						<p style="font-size:16px; color:red" align="center"> <?php if($msg){
    echo $msg;
  }  ?> </p>
						<div class="col-md-12">
							
							<form role="form" method="post" action="">
								<div class="form-group">
									<label>Date of Expense</label>
									<input class="form-control" type="date" value="" name="dateexpense" required="true">
								</div>
								<div class="form-group">
									<label>Item</label>
									<select class="form-control" name="item" required="true">
										<option value="">Select Item</option>
<?php
if(!empty($itemOptions)) {
foreach($itemOptions as $itemOption) {
?>
										<option value="<?php echo htmlspecialchars($itemOption); ?>"><?php echo htmlspecialchars($itemOption); ?></option>
<?php } } ?>
									</select>
<?php if(empty($itemOptions)) { ?>
									<p style="margin-top:8px; color:#777;">No items found. Please add from <a href="add-item.php">Add Items</a>.</p>
<?php } ?>
								</div>
								
								<div class="form-group">
									<label>Cost of Item</label>
									<input class="form-control" type="text" value="" required="true" name="costitem">
								</div>
																
								<div class="form-group has-success">
									<button type="submit" class="btn btn-primary" name="submit">Add</button>
								</div>
								
								
								</div>
								
							</form>
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
	
</body>
</html>
<?php }  ?>
