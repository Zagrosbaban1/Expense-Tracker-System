<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
  } else{
  $userid=$_SESSION['detsuid'];
$currencyOptions=array('USD','EUR','IQD','GBP','AED','SAR');

$currencyColumn=mysqli_query($con,"SHOW COLUMNS FROM tblexpense LIKE 'Currency'");
if(mysqli_num_rows($currencyColumn)==0){
  mysqli_query($con,"ALTER TABLE tblexpense ADD COLUMN Currency varchar(10) NOT NULL DEFAULT 'USD' AFTER ExpenseCost");
}

mysqli_query($con, "CREATE TABLE IF NOT EXISTS tblitems (
  ID int(11) NOT NULL AUTO_INCREMENT,
  UserId int(11) NOT NULL,
  ItemName varchar(150) NOT NULL,
  CreatedAt timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ID),
  KEY idx_userid (UserId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if(isset($_POST['submit']))
  {
    $dateexpense=$_POST['dateexpense'];
     $item=$_POST['item'];
     $costitem=$_POST['costitem'];
     $currency=$_POST['currency'];
     if(!in_array($currency,$currencyOptions)){
      $currency='USD';
     }
    $query=mysqli_query($con, "insert into tblexpense(UserId,ExpenseDate,ExpenseItem,ExpenseCost,Currency) value('$userid','$dateexpense','$item','$costitem','$currency')");
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
<body>
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
										$itemret=mysqli_query($con,"select ItemName from tblitems where UserId='$userid' order by ItemName asc");
										while($itemrow=mysqli_fetch_array($itemret)){
										?>
										<option value="<?php echo $itemrow['ItemName'];?>"><?php echo $itemrow['ItemName'];?></option>
										<?php } ?>
									</select>
									<small><a href="add-item.php">Add new item</a> if you do not see your item here.</small>
								</div>
								
								<div class="form-group">
									<label>Cost of Item</label>
									<input class="form-control" type="text" value="" required="true" name="costitem">
								</div>
								<div class="form-group">
									<label>Currency</label>
									<select class="form-control" name="currency" required="true">
										<?php foreach($currencyOptions as $currency){ ?>
										<option value="<?php echo $currency; ?>"><?php echo $currency; ?></option>
										<?php } ?>
									</select>
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
