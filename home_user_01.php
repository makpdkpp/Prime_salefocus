<?php
require_once 'functions.php';
$mysqli = connectDb();
session_start();

// ตรวจสอบว่าล็อกอินหรือไม่
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$email = htmlspecialchars($_SESSION['email']);
$_id = $_SESSION['user_id'];




?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Prime Focus 25 V1 (sele)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="images/logo.png" href="images/logo.png">
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
  <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" />
  <link href="dist/css/AdminLTE.min.css" rel="stylesheet" />
  <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .user-data-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px auto;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .user-data-table th, .user-data-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .user-data-table th {
        background-color:rgb(100, 0, 0);
        color: white;
    }

    .user-data-table tr:hover {
        background-color: #f1f1f1;
    }

    .user-data-table td {
        font-size: 14px;
    }

    .no-data {
        text-align: center;
        font-size: 18px;
        color: #FF6347;
        margin-top: 20px;
    }

    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
    }

    /* ปรับให้ตารางพอดีกับขนาดหน้าจอ */
    @media (max-width: 768px) {
        .user-data-table th, .user-data-table td {
            padding: 8px;
        }

        .user-data-table {
            font-size: 12px;
        }
    }

</style>
</head>
<body class="skin-red">
<div class="wrapper">
<header class="main-header">
  <a href="home_user.php" class="logo"><b>Prime</b>Focus</a>
  <nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only">Toggle</span></a>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image" />
            <span class="hidden-xs"><?php echo $email; ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
              <p><?php echo $email; ?><small>User</small></p>
            </li>
            <li class="user-footer">
              <div class="pull-right"><a href="logout.php" class="btn btn-default btn-flat">Sign out</a></div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>

<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      <div class="pull-left image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
      </div>
      <div class="pull-left info">
        <p><?php echo $email; ?> (User)</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
              <a href="home_user.php">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="active"><a href="home_user.php"><i class="fa fa-circle-o"></i>Dashboard (กราฟ)</a></li>
                <li class="active"><a href="home_user_01.php"><i class="fa fa-circle-o"></i>Dashboard (ตาราง)</a></li>
              </ul>
            </li>
            <!-- Add data -->
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="active"><a href="User/adduser01.php"><i class="fa fa-circle-o"></i> เพิ่มรายละเอียดการขาย</a></li>
                    </ul>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        
        
        <?php
//กราฟ


//แถวข้อมูล
          // ดึงข้อมูลของผู้ใช้จากเซสชัน
$email = $_SESSION['email'];

// ดึงข้อมูลผู้ใช้จากฐานข้อมูล
$sql = "SELECT `transactional`.*, `product_group`.*, `company_catalog`.*, `priority_level`.*, `team_catalog`.*, `step`.*, user.nname\n"

    . "FROM `transactional` \n"

    . "	LEFT JOIN `product_group` ON `transactional`.`Product_id` = `product_group`.`product_id` \n"

    . "	LEFT JOIN `company_catalog` ON `transactional`.`company_id` = `company_catalog`.`company_id` \n"

    . "	LEFT JOIN `priority_level` ON `transactional`.`priority_id` = `priority_level`.`priority_id` \n"

    . "	LEFT JOIN `team_catalog` ON `transactional`.`team_id` = `team_catalog`.`team_id` \n"

    . "	LEFT JOIN `step` ON `transactional`.`Step_id` = `step`.`level_id`\n"

    . "    LEFT JOIN user ON transactional.user_id = user.user_id\n"

    . "	WHERE transactional.user_id = $_id;";
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row['user_id'];
    $table_name = "user_" . $user_id; // ชื่อตารางของผู้ใช้
    
    // ตรวจสอบว่ามีตารางของผู้ใช้หรือไม่
    $sql_check_table = "SELECT * FROM `transactional` WHERE `user_id` = $_id;";

    $check_table_result = $mysqli->query($sql_check_table);
    
    if ($check_table_result->num_rows > 0) {
        // ถ้ามีตารางแล้ว ดึงข้อมูลจากตารางผู้ใช้
        $sql_data = "SELECT `transactional`.*, `product_group`.*, `company_catalog`.*, `priority_level`.*, `team_catalog`.*, `step`.*, user.nname\n"

    . "FROM `transactional` \n"

    . "	LEFT JOIN `product_group` ON `transactional`.`Product_id` = `product_group`.`product_id` \n"

    . "	LEFT JOIN `company_catalog` ON `transactional`.`company_id` = `company_catalog`.`company_id` \n"

    . "	LEFT JOIN `priority_level` ON `transactional`.`priority_id` = `priority_level`.`priority_id` \n"

    . "	LEFT JOIN `team_catalog` ON `transactional`.`team_id` = `team_catalog`.`team_id` \n"

    . "	LEFT JOIN `step` ON `transactional`.`Step_id` = `step`.`level_id`\n"

    . "    LEFT JOIN user ON transactional.user_id = user.user_id\n"

    . "	WHERE transactional.user_id = $_id;";
        $data_result = $mysqli->query($sql_data);
        
        if ($data_result->num_rows > 0) {
            echo "<table class='user-data-table'>";
            echo "<tr>
		<th>กลุ่มสินค้า</th>
		<th>รายการสินค้า</th>
		<th>วันที่เริ่มติดต่อ</th>
		<th>ขั้นตอน</th>
		<th>พนักงานขาย</th>
		<th>ทีมขาย</th>
		<th>มูลค่าสินค้า</th>
		<th>บริษัท/หน่วยงาน</th>
		<th>คาดว่าจะปิดการขาย</th>
		<th>วันที่ปิดการขาย</th>
		<th>ระดับความสำคัญ</th>
    <th>Actions</th>
		</tr>";
            while ($data_row = $data_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $data_row['product'] . "</td>";
                echo "<td>" . $data_row['Product_detail'] . "</td>";
                echo "<td>" . $data_row['contact_start_date'] . "</td>";
                echo "<td>" . $data_row['level'] . "</td>";
                echo "<td>" . $data_row['nname'] . "</td>";
                echo "<td>" . $data_row['team'] . "</td>";
                echo "<td>" . $data_row['product_value'] . "</td>";
                echo "<td>" . $data_row['company'] . "</td>";
                echo "<td>" . $data_row['date_of_closing_of_sale'] . "</td>";
                echo "<td>" . $data_row['sales_can_be_close'] . "</td>";
                echo "<td>" . $data_row['priority'] . "</td>";
                echo "<td>
                        <a href='User/edit_adduser.php?id=" . $data_row['transac_id'] . "'>
                                <i class='fa fa-pencil-square-o' style='color: #4CAF50;'></i> Edit 
                            </a>
                      </td>";

                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='no-data'>-- ไม่พบข้อมูลในระบบ --</div>";
        }
    } else {
        echo "<div class='no-data'>ไม่พบตารางของผู้ใช้</div>";
    }
} else {
    echo "<div class='no-data'>ไม่พบผู้ใช้นี้ในระบบ</div>";
}
?>


        <!-- Main content -->     




        
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    <!-- jQuery 2.1.3 -->
    <script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- jQuery UI 1.11.2 -->
    <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>    
    <!-- Morris.js charts -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="plugins/morris/morris.min.js" type="text/javascript"></script>
    <!-- Sparkline -->
    <script src="plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
    <!-- jvectormap -->
    <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
    <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/knob/jquery.knob.js" type="text/javascript"></script>
    <!-- daterangepicker -->
    <script src="plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <!-- datepicker -->
    <script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js" type="text/javascript"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js" type="text/javascript"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js" type="text/javascript"></script>
  </body>
</html>
