<?php
session_start();
include("../../functions.php");
$conn = connectDb();
$sql = "SELECT priority FROM priority_level";

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Prime Focus 25 V1 (admin)</title>
  <link rel="icon" type="image/png" href="../../images/logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
  <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" />
  <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" />

  <style>
    .container {
      max-width: 500px;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    button[type="submit"], input[type="submit"] {
      background-color: #007bff;
      color: white;
      padding: 15px;
      font-size: 16px;
      border: none;
      border-radius: 4px;
      width: 100%;
      cursor: pointer;
      margin-top: 25px;
    }

    button[type="submit"]:hover, input[type='submit']:hover {
      background: #0056b3;
    }

    .container1 {
      max-width: 950px;
      margin: 50px auto 0;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #0056b3;
      color: white;
    }

    tr:hover {
      background-color: #f9f9f9;
    }
  </style>
</head>

<body class="skin-blue fixed">
<div class="wrapper">

  <header class="main-header">
    <a href="../../home_admin.php" class="logo"><b>Prime</b>Focus</a>
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../../dist/img/user2-160x160.jpg" class="user-image" alt="User Image" />
              <span class="hidden-xs"><?php echo $_SESSION['email'] ?? ''; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="../../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
                <p><?php echo $_SESSION['email'] ?? ''; ?><small>Admin</small></p>
              </li>
              <li class="user-footer">
                <div class="pull-right">
                  <a href="../../logout.php" class="btn btn-default btn-flat">Sign out</a>
                </div>
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
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
        </div>
        <div class="pull-left info">
          <p><?php echo $_SESSION['email'] ?? ''; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li><a href="../../home_admin.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="treeview active">
          <a href="#"><i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล....</span></a>
          <ul class="treeview-menu">
            <li><a href="../layout/top-nav.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลบริษัท</a></li>
            <li ><a href="../layout/boxed.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
            <li><a href="../layout/fixed.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
            <li><a href="../layout/collapsed-sidebar.php"><i class="fa fa-circle-o"></i> ขั้นตอนการขาย</a></li>
            <li class="active"><a href="../layout/of_winning.php"><i class="fa fa-circle-o"></i> โอกาสการชนะ</a></li>
            <li><a href="../layout/Saleteam.php"><i class="fa fa-circle-o"></i> ทีมขาย</a></li>
            <li><a href="../layout/position_u.php"><i class="fa fa-circle-o"></i> ตำแหน่ง</a></li>
            <li><a href="../layout/Profile_user.php"><i class="fa fa-circle-o"></i> รายละเอียดผู้ใช้งาน</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Content -->
  <div class="content-wrapper">
    <section class="content">

      <div class="container">
        <h2>เพิ่ม % โอกาสการชนะ</h2>
        <form action="priority_level1.php" method="POST">
          <label for="priority">โอกาสการ Win :</label>
          <input type="text" id="priority" name="priority" maxlength="50" required>
          <input type="submit" value="บันทึกข้อมูล">
        </form>
      </div>

      <div class="container1">
        <h2>รายการโอกาสการชนะ</h2>
        <table>
          <thead>
            <tr>
              <th>ชื่อโอกาสการชนะ</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php
            $sql = "SELECT priority_id, priority FROM priority_level";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['priority']}</td>
                        <td>
                          <a href='edit_of_winning.php?priority_id={$row['priority_id']}'><i class='fa fa-pencil-square-o' style='color: #4CAF50;'></i> Edit</a> |
                          <a href='delete_win.php?priority_id={$row['priority_id']}' onclick=\"return confirm('คุณต้องการลบหรือไม่?')\">
                            <i class='fa fa-trash' style='color: #f44336;'></i> Delete
                          </a>
                        </td>
                      </tr>";
              }
            } else {
              echo "<tr><td colspan='2'>-- ไม่พบข้อมูลในระบบ --</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

    </section>
  </div>

</div>

<!-- JS -->
<script src="../../plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<script src="../../dist/js/app.min.js"></script>
</body>
</html>
