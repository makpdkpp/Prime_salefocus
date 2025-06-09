<?php
session_start();
include("../connect.php");

if (isset($_POST['submit'])) {
    $F_name = $_POST['F_name'];
    $email = $_SESSION['email']; // ดึงอีเมล์จากเซสชัน
    $team = $_POST['team'];
    $Job_position = $_POST['Job_position'];

    // ตรวจสอบอีเมล์ซ้ำในฐานข้อมูล
    $email_check = mysqli_query($conn, "SELECT * FROM profile_user WHERE email='$email'");
    if (mysqli_num_rows($email_check) > 0) {
        echo "<script>alert('อีเมล์นี้มีผู้ใช้งานแล้ว'); window.location.href='pro_file.php';</script>";
    } else {
        // เพิ่มข้อมูลลงในฐานข้อมูล
        $query = mysqli_query($conn, "INSERT INTO profile_user (F_name, email, team, position) VALUES ('$F_name', '$email', '$team', '$Job_position')");
        
        if ($query) {
            echo "<script>alert('ข้อมูลถูกเพิ่มสำเร็จ'); window.location.href='pro_file.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
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

        input[type="text"], input[type="email"], textarea {
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

  <body class="skin-blue fixed">
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
                <li><a href="../user.php"><i class="fa fa-circle-o"></i> Dashboard</a></li>
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
            <li><a href="top-nav.php">ข้อมูล...</a></li>
            <li class="active">โปรไฟล์</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <!-- Main content -->
          <div class="container">
<h2>โปรไฟล์</h2>
<form method="POST">

<label>ชื่อ :  </label>
    <input type="text" name="F_name" required><br/>

    <label>E-mail :   <p style="display: inline"><?php 
        if(isset($_SESSION['email'])){
            echo $_SESSION['email']; // แสดงอีเมล์จากเซสชัน
        }
    ?></p></label>
    <br/>

    <label>ทีมขาย :  </label>
    <select name="team">
        <?php
            $team = mysqli_query($conn, "SELECT id, team FROM team_s");
            echo '<option value=""> -- เลือกทีมขาย -- </option>';
            while($c = mysqli_fetch_array($team)) {
        ?>
            <option value="<?php echo $c['team']?>"><?php echo $c['team']?></option>
        <?php } ?>
    </select><br/>

    <label>ตำแหน่ง :  </label>
    <select name="Job_position">
        <?php
            $Job_position = mysqli_query($conn, "SELECT id, Job_position FROM position");
            echo '<option value=""> --เลือกตำแหน่ง -- </option>';
            while($c = mysqli_fetch_array($Job_position)) {
        ?>
            <option value="<?php echo $c['Job_position']?>"><?php echo $c['Job_position']?></option>
        <?php } ?>
    </select><br/>

    <button type="submit" name="submit">Submit</button>

    </form>

</div>
<div class="container1">
        <h1>ข้อมูลบริษัท</h1>
        <table border="1">
        <thead>
            <tr>
                <th>บริษัท</th>
                <th>อุตสาหกรรม</th>
                <th>Actions</th>
            </tr>
        </thead>
        <?php
        $sql = "SELECT id, 	F_name, email,	team,position FROM profile_user"; // คำสั่ง SQL
        $result = $conn->query($sql); // รันคำสั่ง SQL

        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['team'] . "</td>";
                echo "<td>
                        <a href='edit_top.php?id=" . $row['id'] . "'>
                                <i class='fa fa-pencil-square-o' style='color: #4CAF50;'></i> Edit 
                            </a> | 
                            <a href='delete_Top.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete?\")'>
                                <i class='fa fa-trash' style='color: #f44336;'></i> Delete
                            </a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'> -- ไม่พบข้อมูลในระบบ -- </td></tr>";
        }
            ?>

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