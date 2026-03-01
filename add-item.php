<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['detsuid'] == 0)) {
  header('location:logout.php');
} else {
  $userid = $_SESSION['detsuid'];
  $msg = "";

  mysqli_query($con, "CREATE TABLE IF NOT EXISTS tblitemmaster (
    ID INT(11) NOT NULL AUTO_INCREMENT,
    UserId INT(11) NOT NULL,
    ItemName VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (ID),
    UNIQUE KEY uniq_user_item (UserId, ItemName)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

  if (isset($_POST['submit'])) {
    $itemname = trim($_POST['itemname']);
    $itemname = mysqli_real_escape_string($con, $itemname);

    if ($itemname == "") {
      $msg = "Item name is required.";
    } else {
      $insertQuery = mysqli_query($con, "insert into tblitemmaster(UserId, ItemName) values('$userid', '$itemname')");
      if ($insertQuery) {
        echo "<script>alert('Item has been added');</script>";
        echo "<script>window.location.href='add-item.php'</script>";
      } else {
        $exists = mysqli_query($con, "select ID from tblitemmaster where UserId='$userid' and ItemName='$itemname' limit 1");
        if ($exists && mysqli_num_rows($exists) > 0) {
          $msg = "This item already exists.";
        } else {
          $msg = "Something went wrong. Please try again.";
        }
      }
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daily Expense Tracker || Add Item</title>
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
  <?php include_once('includes/header.php'); ?>
  <?php include_once('includes/sidebar.php'); ?>

  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
      <ol class="breadcrumb">
        <li><a href="#"><em class="fa fa-home"></em></a></li>
        <li class="active">Items</li>
      </ol>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading">Add New Item</div>
          <div class="panel-body">
            <p style="font-size:16px; color:red" align="center"><?php if ($msg) { echo $msg; } ?></p>
            <div class="col-md-12">
              <form role="form" method="post" action="">
                <div class="form-group">
                  <label>Item Name</label>
                  <input type="text" class="form-control" name="itemname" required="true" placeholder="e.g. Groceries">
                </div>
                <div class="form-group has-success">
                  <button type="submit" class="btn btn-primary" name="submit">Add Item</button>
                </div>
              </form>
            </div>

            <div class="col-md-12" style="margin-top:20px;">
              <div class="table-responsive">
                <table class="table table-bordered mg-b-0">
                  <thead>
                    <tr>
                      <th>S.NO</th>
                      <th>Item Name</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $itemList = mysqli_query($con, "select ItemName from tblitemmaster where UserId='$userid' order by ItemName asc");
                    $cnt = 1;
                    while ($row = mysqli_fetch_array($itemList)) {
                    ?>
                    <tr>
                      <td><?php echo $cnt; ?></td>
                      <td><?php echo htmlspecialchars($row['ItemName']); ?></td>
                    </tr>
                    <?php
                      $cnt = $cnt + 1;
                    }
                    if ($cnt == 1) {
                    ?>
                    <tr>
                      <td colspan="2" class="text-center">No items added yet.</td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php include_once('includes/footer.php'); ?>
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
<?php } ?>
