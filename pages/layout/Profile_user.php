<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

$sql = "SELECT user_id, nname, surename, email FROM user WHERE role_id = 2";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ผู้ใช้งาน | PrimeFocus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    body { background: #e9f2f9; }
    .container1 {
      max-width: 1100px;
      margin: 40px auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #0056b3; color: white; }
    tr:hover { background-color: #f5f5f5; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed"><body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
<header class="main-header">
  <a href="../../home_admin.php" class="logo"><b>Prime</b>Focus</a>
  <nav class="navbar navbar-static-top">
    <div class="navbar-custom-menu ml-auto d-flex justify-content-end w-100">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="../../dist/img/user2-160x160.jpg" class="user-image img-circle elevation-2">
            <span class="hidden-xs text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" style="width:60px;height:60px">
              <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
            </li>
            <li class="user-footer">
              <div class="d-flex justify-content-end w-100">
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
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
      </div>
      <div class="info pl-2">
        <p class="mb-0 text-white font-weight-bold" style="font-size:14px;">
          <?php echo $_SESSION['email'] ?? ''; ?> <span class="text-muted" style="font-size:12px;">(Admin)</span>
        </p>
        <a href="#" class="d-block text-success"><i class="fa fa-circle"></i> Online</a>
      </div>
    </div>
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li><a href="../../home_admin.php"><i class="fa fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
      <li class="treeview active">
        <a href="#">
          <i class="fa fa-folder-open"></i> <span>เพิ่มข้อมูล....</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="../layout/top-nav.php"><i class="fas fa-building"></i> เพิ่มข้อมูลบริษัท</a></li>
            <li><a href="../layout/boxed.php"><i class="fas fa-boxes"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
            <li><a href="../layout/fixed.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
            <li><a href="../layout/collapsed-sidebar.php"><i class="fas fa-tasks"></i> ขั้นตอนการขาย</a></li>
            <li><a href="../layout/of_winning.php"><i class="fas fa-trophy"></i> โอกาสการชนะ</a></li>
            <li><a href="../layout/Saleteam.php"><i class="fas fa-users"></i> ทีมขาย</a></li>
            <li><a href="../layout/position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
            <li class="active"><a href="../layout/Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content">
      <div class="container1">
        <h3>ข้อมูลผู้ใช้งาน</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ชื่อ</th>
              <th>นามสกุล</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['nname']) ?></td>
                <td><?= htmlspecialchars($row['surename']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                  <a href='delete_Pro.php?user_id=<?= $row['user_id'] ?>' onclick="return confirm('คุณต้องการลบหรือไม่?')" class='btn btn-sm btn-danger'>
                    <i class='fa fa-trash'></i> ลบ
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if ($result->num_rows === 0): ?>
              <tr><td colspan='4' class='text-center'>-- ไม่พบข้อมูลในระบบ --</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../dist/js/app.min.js"></script>
</body>
</html>
