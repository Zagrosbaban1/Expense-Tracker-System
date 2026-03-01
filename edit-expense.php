<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid']==0)) {
  header('location:logout.php');
} else {
  $userid=$_SESSION['detsuid'];
  $editid=intval($_GET['editid']);

  if($editid==0){
    header('location:manage-expense.php');
    exit();
  }

  if(isset($_POST['submit'])) {
    $dateexpense=$_POST['dateexpense'];
    $item=$_POST['item'];
    $costitem=$_POST['costitem'];
    $query=mysqli_query($con, "update tblexpense set ExpenseDate='$dateexpense',ExpenseItem='$item',ExpenseCost='$costitem' where ID='$editid' && UserId='$userid'");
    if($query){
      echo "<script>alert('Expense has been updated');</script>";
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
  <title>Daily Expense Tracker || Edit Expense</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/font-awesome.min.css" rel="stylesheet">
  <link href="css/datepicker3.css" rel="stylesheet">
  <link href="css/styles.css" rel="stylesheet">

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
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading">Edit Expense</div>
          <div class="panel-body">
            <p style="font-size:16px; color:red" align="center"> <?php if($msg){ echo $msg; }  ?> </p>
            <div class="col-md-12">
              <?php
              $ret=mysqli_query($con,"select * from tblexpense where ID='$editid' && UserId='$userid'");
              $cnt=1;
              if($row=mysqli_fetch_array($ret)) {
              ?>
              <form role="form" method="post" action="">
                <div class="form-group">
                  <label>Date of Expense</label>
                  <input class="form-control" type="date" name="dateexpense" value="<?php echo $row['ExpenseDate'];?>" required="true">
                </div>
                <div class="form-group">
                  <label>Item</label>
                  <input type="text" class="form-control" name="item" value="<?php echo $row['ExpenseItem'];?>" required="true">
                </div>
                <div class="form-group">
                  <label>Cost of Item</label>
                  <input class="form-control" type="text" name="costitem" value="<?php echo $row['ExpenseCost'];?>" required="true">
                </div>
                <div class="form-group has-success">
                  <button type="submit" class="btn btn-primary btn-modern-submit" name="submit">
                    <em class="fa fa-save"></em> Update Expense
                  </button>
                </div>
              </form>
              <?php } else { ?>
                <div class="alert alert-danger">Expense not found.</div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <?php include_once('includes/footer.php');?>
    </div>
  </div>

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

