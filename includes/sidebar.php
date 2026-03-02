<?php
$uid = (int)($_SESSION['detsuid'] ?? 0);
$name = 'User';

if ($uid > 0) {
    $stmt = mysqli_prepare($con, 'SELECT FullName FROM tbluser WHERE ID = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $uid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $name = $row['FullName'];
    }
    mysqli_stmt_close($stmt);
}
?>
<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
    <div class="profile-sidebar">
        <div class="profile-userpic">
            <img src="http://placehold.it/50/30a5ff/fff" class="img-responsive" alt="">
        </div>
        <div class="profile-usertitle">
            <div class="profile-usertitle-name"><?php echo e($name); ?></div>
            <div class="profile-usertitle-status"><span class="indicator label-success"></span>Online</div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="divider"></div>

    <ul class="nav menu">
        <li class="active"><a href="dashboard.php"><em class="fa fa-dashboard">&nbsp;</em> Dashboard</a></li>
        <li class="parent "><a data-toggle="collapse" href="#sub-item-1">
                <em class="fa fa-navicon">&nbsp;</em>Expenses <span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-1">
                <li><a class="" href="add-item.php"><span class="fa fa-arrow-right">&nbsp;</span> Add Items</a></li>
                <li><a class="" href="add-expense.php"><span class="fa fa-arrow-right">&nbsp;</span> Add Expenses</a></li>
                <li><a class="" href="manage-expense.php"><span class="fa fa-arrow-right">&nbsp;</span> Manage Expenses</a></li>
            </ul>
        </li>
        <li class="parent "><a data-toggle="collapse" href="#sub-item-2">
                <em class="fa fa-navicon">&nbsp;</em>Expense Report <span data-toggle="collapse" href="#sub-item-1" class="icon pull-right"><em class="fa fa-plus"></em></span>
            </a>
            <ul class="children collapse" id="sub-item-2">
                <li><a class="" href="expense-datewise-reports.php"><span class="fa fa-arrow-right">&nbsp;</span> Daywise Expenses</a></li>
                <li><a class="" href="expense-monthwise-reports.php"><span class="fa fa-arrow-right">&nbsp;</span> Monthwise Expenses</a></li>
                <li><a class="" href="expense-yearwise-reports.php"><span class="fa fa-arrow-right">&nbsp;</span> Yearwise Expenses</a></li>
            </ul>
        </li>
        <li><a href="user-profile.php"><em class="fa fa-user">&nbsp;</em> Profile</a></li>
        <li><a href="change-password.php"><em class="fa fa-clone">&nbsp;</em> Change Password</a></li>
        <li><a href="logout.php"><em class="fa fa-power-off">&nbsp;</em> Logout</a></li>
    </ul>
</div>
