<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

$totalQuery = $mysqli->query("SELECT COUNT(*) as total FROM position");
$totalRow = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRow / $limit);

$positions = $mysqli->query("SELECT position_id, position FROM position LIMIT $start, $limit");
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ตำแหน่ง | PrimeForecast</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../../plugins_v3/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../dist_v3/css/adminlte.min.css">

  <style>
    /* CSS หลักสำหรับทุกหน้า */
    .content-wrapper { background-color: #b3d6e4; }
    .container1 {
      max-width: 1100px;
      margin: 20px auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .btn-add {
      position: fixed; bottom: 30px; right: 30px; background: #0056b3;
      color: #fff; border-radius: 50%; width: 56px; height: 56px;
      font-size: 24px; border: none; z-index: 1040;
    }
    .modal-content { border-radius: 10px; padding: 20px; }
    .table thead { background: #0056b3; color: white; }
    .pagination .page-item.active .page-link { background-color: #0056b3; border-color: #0056b3; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  
  <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background-color: #0056b3;">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <img src="../../dist/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
          </li>
          <li class="user-footer">
            <a href="../../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../../home_admin.php" class="brand-link" style="background-color: #0056b3; text-align: center;">
        <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>
    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></a>
          <span class="d-block" style="color: #c2c7d0; font-size: 0.9em;">(Admin)</span>
          <a href="#" class="d-block"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN NAVIGATION</li>
          <li class="nav-item">
            <a href="../../home_admin.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item menu-is-opening menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-folder-open"></i><p>เพิ่มข้อมูล....<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="../layout/top-nav.php" class="nav-link"><i class="fas fa-building nav-icon"></i><p>เพิ่มข้อมูลบริษัท</p></a></li>
              <li class="nav-item"><a href="../layout/boxed.php" class="nav-link"><i class="fas fa-boxes nav-icon"></i><p>เพิ่มข้อมูลกลุ่มสินค้า</p></a></li>
              <li class="nav-item"><a href="../layout/fixed.php" class="nav-link"><i class="fas fa-industry nav-icon"></i><p>เพิ่มข้อมูลอุตสาหกรรม</p></a></li>
              <li class="nav-item"><a href="../layout/Source_of_the_budget.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>เพิ่มข้อมูลที่มาของงบประมาณ</p></a></li>
              <li class="nav-item"><a href="../layout/collapsed-sidebar.php" class="nav-link"><i class="fas fa-tasks nav-icon"></i><p>ขั้นตอนการขาย</p></a></li>
              <li class="nav-item"><a href="../layout/of_winning.php" class="nav-link"><i class="fas fa-trophy nav-icon"></i><p>โอกาสการชนะ</p></a></li>
              <li class="nav-item"><a href="../layout/Saleteam.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>ทีมขาย</p></a></li>
              <li class="nav-item"><a href="../layout/position_u.php" class="nav-link active"><i class="fas fa-user-tag nav-icon"></i><p>ตำแหน่ง</p></a></li>
              <li class="nav-item"><a href="../layout/Profile_user.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>รายละเอียดผู้ใช้งาน</p></a></li>
              <li class="nav-item"><a href="../layout/newuser.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../../home_admin.php">หน้าหลัก</a></li>
              <li class="breadcrumb-item active">ข้อมูลตำแหน่ง</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container1">
        <h3>ตำแหน่ง</h3>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>ตำแหน่ง</th>
              <th style="width:160px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $positions->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['position']) ?></td>
                <td>
                  <button class='btn btn-sm btn-info btn-edit' data-id='<?= $row['position_id'] ?>' data-name='<?= htmlspecialchars($row['position'], ENT_QUOTES) ?>'>
                    <i class='fas fa-edit'></i> แก้ไข
                  </button>
                  <a href='delete_position.php?position_id=<?= $row['position_id'] ?>' onclick="return confirm('คุณต้องการลบหรือไม่?')" class='btn btn-sm btn-danger'>
                    <i class='fas fa-trash'></i> ลบ
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
            <?php if ($positions->num_rows === 0): ?>
              <tr><td colspan='2' class='text-center'>-- ไม่พบข้อมูลในระบบ --</td></tr>
            <?php endif; ?>
          </tbody>
        </table>

        <nav>
          <ul class="pagination justify-content-center" style="margin-top: 20px;">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page - 1 ?>">&laquo;</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $page + 1 ?>">&raquo;</a>
            </li>
          </ul>
        </nav>
      </div>

      <button class="btn-add" data-toggle="modal" data-target="#addModal">
        <i class="fas fa-plus"></i>
      </button>

      <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form action="add_position.php" method="POST">
              <div class="modal-header">
                <h5 class="modal-title">เพิ่มชื่อตำแหน่ง</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                 <input type="text" name="position" class="form-control" placeholder="กรอกชื่อตำแหน่ง" required autofocus>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-block">บันทึก</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <form id="editForm" action="add_position.php" method="POST">
              <div class="modal-header">
                <h5 class="modal-title">แก้ไขชื่อตำแหน่ง</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="position_id" id="edit_position_id">
                <input type="text" name="position" id="edit_position_name" class="form-control" required>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<script src="../../plugins_v3/jquery/jquery.min.js"></script>
<script src="../../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>

<script>
  $(document).ready(function () {
    $('.btn-edit').on('click', function () {
      $('#edit_position_id').val($(this).data('id'));
      $('#edit_position_name').val($(this).data('name'));
      $('#editModal').modal('show');
    });
  });
</script>
</body>
</html>