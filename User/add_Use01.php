<?php

require_once '../functions.php';
session_start();
// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 3) {
    header('Location: ../index.php');
    exit;
}
// ดึงข้อมูลของผู้ใช้จากเซสชัน
$email = $_SESSION['email'];

// ตรวจสอบว่าอีเมล์ในเซสชันมีอยู่หรือไม่
if (isset($email)) {
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล โดยการ JOIN ตาราง users และ profile_user
    $sql = "SELECT users.id, users.email, profile_user.team, profile_user.F_name FROM users 
            INNER JOIN profile_user ON users.email = profile_user.email 
            WHERE users.email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];
        $table_name = "user_" . $user_id; // ชื่อตารางของผู้ใช้
        $team = $row['team'];
        $F_name = $row['F_name'];

        // เช็คว่าเมื่อกี้มีการกดปุ่มส่งข้อมูลหรือไม่
        if (isset($_POST['submit'])) {
            // รับค่าจากฟอร์ม
            $Product_list = $_POST['Product_list'];
            $Contact_start_date = $_POST['Contact_start_date'];
            $Product_value = $_POST['Product_value'];
            $date_of_closing_of_sale = $_POST['date_of_closing_of_sale'];
            $sales_can_be_closed = $_POST['sales_can_be_closed'];
            $Product = $_POST['Product'];
            $Level = $_POST['Level'];
            $add_office = $_POST['add_office'];
            $Priority = $_POST['Priority'];

            // สร้างคำสั่ง SQL สำหรับการเพิ่มข้อมูล
            $query = mysqli_query($conn, "INSERT INTO $table_name (Product_group, Product_list, Contact_start_date, step, salesperson, Sales_team, Product_value, company, date_of_closing_of_sale, sales_can_be_closed, Priority_level) 
            VALUES ('$Product', '$Product_list', '$Contact_start_date', '$Level', '$F_name', '$team', '$Product_value', '$add_office', '$date_of_closing_of_sale', '$sales_can_be_closed', '$Priority')");

            // ตรวจสอบการเพิ่มข้อมูล
            if ($query) {
                echo "<script>alert('ข้อมูลถูกเพิ่มสำเร็จ'); window.location.href='add_Use.php';</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    } else {
        echo "ไม่พบข้อมูลผู้ใช้";
    }
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>AdminLTE 2 | Fixed Layout</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
  <style>
.container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

        }

        input[type="text"], input[type="email"] textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #4b03a4;
            color: #fff;
            padding: 20px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 45px;
        }

        button[type="submit"]:hover {
            background-color: #e6d5f8;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #e6d5f8;
            font-size: 16px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
.container1 {
            width: 100%;
            max-width: 950px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        table {
            width: 100%;
            margin: 20px auto;
            border-collapse: collapse;
            border: 1px solid #ddd;
            background-color: white;
            text-align: center;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4f0597;
            color: white;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

        select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
}
</style>

  <body class="skin-red fixed">
    <!-- Site wrapper -->
    <div class="wrapper">
      
      <header class="main-header">
      <a href="../home_user.php" class="logo"><b>Prime</b> Solution </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
             
              <!-- Tasks: style can be found in dropdown.less -->
              <li class="dropdown tasks-menu">
                <ul class="dropdown-menu">
                  <li class="footer">
                    <a href="#">View all tasks</a>
                  </li>
                </ul>
              </li>
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="User Image"/>
                  <span class="hidden-xs">
                  <?php 
                  if(isset($_SESSION['email'])){
                  $email=$_SESSION['email'];
                  $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
                  while($row=mysqli_fetch_array($query)){
                  echo $row['email'];
                  }
                  }
                  ?>
                  </span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
                    <p>
                    <?php 
                  if(isset($_SESSION['email'])){
                  $email=$_SESSION['email'];
                  $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
                  while($row=mysqli_fetch_array($query)){
                  echo $row['email'];
                  }
                  }
                  ?>
                      <small>-----</small>
                    </p>
                  </li>
                  
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="pro_file.php" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="../logout.php" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </nav>
      </header>

      <!-- =============================================== -->

      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
              <p><?php 
                  if(isset($_SESSION['email'])){
                  $email=$_SESSION['email'];
                  $query=mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
                  while($row=mysqli_fetch_array($query)){
                  echo $row['email'];
                  }
                  }
                  ?></p>

              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>

          
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="../home_user.php"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
              </ul>
            </li>
            <li class="treeview active">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span>เพิ่มข้อมูล....</span>
              </a>
              <ul class="treeview-menu">
              <li><a href="add_use.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลบริษัท</a></li>
              </ul>
            </li>
            <li>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- =============================================== -->

      <!-- Right side column. Contains the navbar and content of the page -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            
            <small></small>
          </h1>
          <ol class="breadcrumb">
            <li ><a href="../home_user.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="../home_user.php">ข้อมูล...</a></li>
            <li class="active">เพิ่มรายละเอียดพนักงาน</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Main content -->
          <div class="container">
            <h2>เพิ่มรายละเอียด...</h2>
            <form method="POST">
                <!-- ฟอร์มต่าง ๆ ที่แสดงข้อมูล -->
                <label>กลุ่มสินค้า :  </label>
                <select name="Product">
                <?php
                    $product_group = mysqli_query($conn, "SELECT product_id, product FROM product_group");
                    echo '<option value=""> -- เลือกลุ่มสินค้า -- </option>';
                    while($c = mysqli_fetch_array($product_group)) {
                ?>
                    <option value="<?php echo $c['product']?>"><?php echo $c['product']?></option>
                <?php } ?>
                </select><br/>

                <label>รายการสินค้า :  </label>
                <input type="text" name="Product_list"?><br/>

                <label> วันที่ติดต่อ :  </label>
                <input type="date" name="Contact_start_date"?><br/>

                <label>ขั้นตอน :  </label>
                <select name="Level">
                <?php
                    $step = mysqli_query($conn, "SELECT id, Level FROM step");
                    echo '<option value=""> -- เลือกขั้นตอน -- </option>';
                    while($c = mysqli_fetch_array($step)) {
                ?>
                    <option value="<?php echo $c['Level']?>"><?php echo $c['Level']?></option>
                <?php } ?>
                </select><br/>

                <label>พนักงานขาย :  </label>
                <p style="display: inline; font-size: 16px;"><?php echo $F_name; ?></p><br/>


                <label>ทีมขาย :  </label>
                <p style="display: inline; font-size: 16px;"><?php echo $team; ?></p><br/>


                <label>มูลค่า :  </label>
                <input type="text" name="Product_value"?><br/>

                <label>บริษัท :  </label>
                <select name="add_office">
                <?php
                    $office = mysqli_query($conn, "SELECT id, add_office FROM office");
                    echo '<option value=""> -- Select บริษัท -- </option>';
                    while($c = mysqli_fetch_array($office)) {
                ?>
                    <option value="<?php echo $c['add_office']?>"><?php echo $c['add_office']?></option>
                <?php } ?>
                </select><br/>

                <label>คาดว่าจะปิดการขาย :  </label>
                <input type="date" name="date_of_closing_of_sale"?><br/><br/>

                <label> วันที่ปิดการขายได้ :  </label>
                <input type="date" name="sales_can_be_closed"?><br/><br/>

                <label>ระดับความสำคัญ :  </label>
                <select name="Priority">
                <?php
                    $Priority_level = mysqli_query($conn, "SELECT id, Priority FROM Priority_level");
                    echo '<option value=""> -- เลือกระดับความเสี่ยง -- </option>';
                    while($c = mysqli_fetch_array($Priority_level)) {
                ?>
                    <option value="<?php echo $c['Priority']?>"><?php echo $c['Priority']?></option>
                <?php } ?>
                </select><br/>

                <button type="submit" name="submit">Submit</button>
            </form>
        </div>

      </div><!-- /.content-wrapper -->

    </div><!-- ./wrapper -->
    <script>//สคริป
/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
      function myFunction() {
      document.getElementById("myDropdown").classList.toggle("show");
      }

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>

    <!-- jQuery 2.1.3 -->
    <script src="../plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="../plugins/slimScroll/jquery.slimScroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='../plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/app.min.js" type="text/javascript"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js" type="text/javascript"></script>
  </body>
</html>