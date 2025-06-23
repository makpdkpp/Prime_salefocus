<?php
session_start();

/* ---------- เชื่อมต่อฐานข้อมูล ---------- */
require_once '../../functions.php';   // หรือ include '../../connect.php';
$conn = connectDb();                  // ถ้าใช้ functions.php

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

$totalQuery = $conn->query("SELECT COUNT(*) as total FROM priority_level");
$totalRow = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRow / $limit);

$sql = "SELECT priority_id, priority FROM priority_level LIMIT $start, $limit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>โอกาสการชนะ | PrimeForecast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- ✅ Bootstrap 3 -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="../../dist/js/app.min.js"></script>
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
    input {
      width: 100%; padding: 10px; margin-bottom: 20px;
      border: 1px solid #ccc; border-radius: 5px; font-size: 16px;
    }
    .btn-add {
      position: fixed; bottom: 30px; right: 30px; background: #0056b3;
      color: #fff; border-radius: 50%; width: 56px; height: 56px;
      font-size: 24px; border: none; z-index: 999;
    }
    .modal-content { border-radius: 10px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #0056b3; color: white; }
    tr:hover { background-color: #f5f5f5; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
<header class="main-header">

  <!-- โลโก้ -->
  <a href="../../home_admin.php" class="logo">
    <span class="logo-lg"><b>Prime</b>Forecast</span>
  </a>

  <!-- Navbar -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- ✅ ปุ่ม 3 ขีด -->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>

    <!-- ✅ เมนูโปรไฟล์ด้านขวา -->
    <div class="navbar-custom-menu">

      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="../../dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
            <span class="hidden-xs text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
          </a>
          <ul class="dropdown-menu">
            <!-- user image -->
            <li class="user-header">
              <img src="../../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
              <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
            </li>
            <!-- Menu Footer-->
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
            <li><a href="../layout/Source_of_the_budget.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลที่มาของงบประมาณ</a></li>
            <li><a href="../layout/collapsed-sidebar.php"><i class="fas fa-tasks"></i> ขั้นตอนการขาย</a></li>
            <li class="active"><a href="../layout/of_winning.php"><i class="fas fa-trophy"></i> โอกาสการชนะ</a></li>
            <li><a href="../layout/Saleteam.php"><i class="fas fa-users"></i> ทีมขาย</a></li>
            <li><a href="../layout/position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
            <li><a href="../layout/Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
            <li><a href="../layout/newuser.php"><i class="fas fa-user-plus"></i> เพิ่มผู้ใช้งาน</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content">
      <div class="container1">
        <h3>ข้อมูลโอกาสการชนะ</h3>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ชื่อโอกาสการชนะ</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['priority']) ?></td>
                <td>
                  <button class='btn btn-sm btn-info btn-edit' data-id='<?= $row['priority_id'] ?>' data-name="<?= htmlspecialchars($row['priority']) ?>" data-toggle='modal' data-target='#editModal'>
                    <i class='fa fa-edit'></i> Edit
                  </button>
                  <a href='delete_win.php?priority_id=<?= $row['priority_id'] ?>' onclick="return confirm('คุณต้องการลบหรือไม่?')" class='btn btn-sm btn-danger'>
                    <i class='fa fa-trash'></i> Delete
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <nav>
          <ul class="pagination justify-content-center mt-3">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">ก่อนหน้า</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">ถัดไป</a>
            </li>
          </ul>
        </nav>
      </div>

      <button class="btn-add" data-toggle="modal" data-target="#addModal">
        <i class="fa fa-plus"></i>
      </button>

      <!-- Add Modal -->
      <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <form action="priority_level1.php" method="POST">
              <div class="modal-header">
                <h4 class="modal-title">เพิ่มโอกาสการชนะ</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="priority">ชื่อโอกาสการชนะ:</label>
                  <input type="text" name="priority" id="priority" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-block">บันทึกข้อมูล</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <form action="priority_level1.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="priority_id" id="edit_id">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">แก้ไขโอกาสการชนะ</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <label for="edit_priority">ชื่อโอกาสการชนะ:</label>
                  <input type="text" name="priority" id="edit_priority" class="form-control" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">บันทึก</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</div>
<script>
  $(document).on('click', '.btn-edit', function () {
    $('#edit_id').val($(this).data('id'));
    $('#edit_priority').val($(this).data('name'));
  });
</script>
</body>
</html>
