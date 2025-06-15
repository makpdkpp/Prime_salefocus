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
  <meta charset="UTF-8">
  <title>ตำแหน่ง | PrimeFocus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    body { background: #b3d6e4; }
    .container1 { background: #fff; border-radius: 10px; padding: 25px; margin: 40px auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width: 1100px; }
    .table thead { background: #0056b3; color: #fff; }
    .btn-add { position: fixed; bottom: 30px; right: 30px; background: #0056b3; color: #fff; border-radius: 50%; width: 56px; height: 56px; font-size: 24px; border: none; z-index: 999; }
    .modal-content { border-radius: 10px; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
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
            <li class="active"><a href="../layout/position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
            <li><a href="../layout/Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>
<div class="wrapper">
  <div class="content-wrapper">
    <section class="content">
      <div class="container1">
        <h3>ตำแหน่ง</h3>

        <table class="table table-bordered">
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
                  <button class='btn btn-sm btn-info btn-edit' data-id='<?= $row['position_id'] ?>' data-name='<?= htmlspecialchars($row['position']) ?>'>
                    <i class='fa fa-edit'></i> แก้ไข
                  </button>
                  <a href='delete_position.php?position_id=<?= $row['position_id'] ?>' onclick="return confirm('คุณต้องการลบหรือไม่?')" class='btn btn-sm btn-danger'>
                    <i class='fa fa-trash'></i> ลบ
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
    </section>
  </div>
</div>

<!-- ปุ่มเพิ่มข้อมูล -->
<button class="btn-add" data-toggle="modal" data-target="#addModal">
  <i class="fa fa-plus"></i>
</button>

<!-- Modal เพิ่มตำแหน่ง -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="add_position.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">เพิ่มชื่อตำแหน่ง</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="text" name="position" class="form-control" placeholder="กรอกชื่อตำแหน่ง" required autofocus>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal แก้ไขตำแหน่ง -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="add_position.php" method="POST">
        <div class="modal-header">
          <h5 class="modal-title">แก้ไขชื่อตำแหน่ง</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- เพิ่ม input นี้ -->
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="position_id" id="edit_position_id">
          <input type="text" name="position" id="edit_position_name" class="form-control" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../dist/js/app.min.js"></script>
<script>
  $(document).ready(function () {
    $('.btn-edit').on('click', function () {
      const id = $(this).data('id');
      const name = $(this).data('name');
      $('#edit_position_id').val(id);
      $('#edit_position_name').val(name);
      $('#editModal').modal('show');
    });
  });
</script>
</body>
</html>
