<?php
/* ------------------------------------------------------------------
   fixed.php  –  แสดง/เพิ่ม/แก้ไข industry_group  (AdminLTE 2 + PHP 5/7)
------------------------------------------------------------------- */
session_start();
require_once '../../functions.php';
$mysqli = connectDb();
$mysqli->set_charset('utf8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ---------- Pagination ---------- */
$limit  = 5;
$page   = isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
$start  = ($page-1)*$limit;

$total  = $mysqli->query("SELECT COUNT(*) AS c FROM industry_group")->fetch_assoc()['c'];
$pages  = ceil($total/$limit);

$stmt   = $mysqli->prepare("SELECT industry_id, industry FROM industry_group ORDER BY industry LIMIT ?,?");
$stmt->bind_param('ii',$start,$limit);
$stmt->execute();
$listRs = $stmt->get_result();
?>
<!DOCTYPE html><html lang="th"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Industry | PrimeFocus</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
 body{background:#e9f2f9}
 .container1{max-width:1100px;margin:40px auto;background:#fff;padding:25px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.1)}
 .btn-add{position:fixed;bottom:30px;right:30px;width:56px;height:56px;border-radius:50%;font-size:24px;color:#fff;background:#0056b3;border:none;z-index:999}
 table th{background:#0056b3;color:#fff}
</style></head>
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
            <li ><a href="../layout/boxed.php"><i class="fas fa-boxes"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
            <li class="active"><a href="../layout/fixed.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
            <li><a href="../layout/collapsed-sidebar.php"><i class="fas fa-tasks"></i> ขั้นตอนการขาย</a></li>
            <li><a href="../layout/of_winning.php"><i class="fas fa-trophy"></i> โอกาสการชนะ</a></li>
            <li><a href="../layout/Saleteam.php"><i class="fas fa-users"></i> ทีมขาย</a></li>
            <li><a href="../layout/position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
            <li><a href="../layout/Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>

<div class="content-wrapper"><section class="content">
  <div class="container1">
    <h3 class="mb-3">ข้อมูลอุตสาหกรรม</h3>
    <table class="table table-bordered">
      <thead><tr><th>ชื่ออุตสาหกรรม</th><th width="180">Actions</th></tr></thead>
      <tbody>
        <?php while($r=$listRs->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($r['industry']) ?></td>
          <td>
            <button class="btn btn-sm btn-info btn-edit"
                    data-id="<?= $r['industry_id'] ?>"
                    data-name="<?= htmlspecialchars($r['industry']) ?>"
                    data-toggle="modal" data-target="#editModal">
              <i class="fa fa-edit"></i> Edit
            </button>
            <a class="btn btn-sm btn-danger"
               onclick="return confirm('ลบข้อมูลนี้ ?')"
               href="delete_fixed.php?industry_id=<?= $r['industry_id'] ?>">
              <i class="fa fa-trash"></i> Delete
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <!-- pagination -->
    <nav class="mt-3">
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page<=1?'disabled':'' ?>">
          <a class="page-link" href="?page=<?= $page-1 ?>">«</a>
        </li>
        <?php for($i=1;$i<=$pages;$i++): ?>
          <li class="page-item <?= $i==$page?'active':'' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page>=$pages?'disabled':'' ?>">
          <a class="page-link" href="?page=<?= $page+1 ?>">»</a>
        </li>
      </ul>
    </nav>
  </div>

  <!-- ปุ่ม Add -->
  <button class="btn-add" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i></button>

  <!-- Add Modal -->
  <div class="modal fade" id="addModal"><div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="add_Industry.php" method="post">
        <input type="hidden" name="action" value="add">
        <div class="modal-header"><h5 class="modal-title">เพิ่มอุตสาหกรรม</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body"><input class="form-control" name="Industry" placeholder="ชื่ออุตสาหกรรม" required></div>
        <div class="modal-footer"><button class="btn btn-primary btn-block">บันทึก</button></div>
      </form>
    </div></div></div>

  <!-- Edit Modal -->
  <div class="modal fade" id="editModal"><div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="add_Industry.php" method="post">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="Industry_id" id="edit_id">
        <div class="modal-header"><h5 class="modal-title">แก้ไขอุตสาหกรรม</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
        <div class="modal-body"><input class="form-control" name="Industry" id="edit_name" required></div>
        <div class="modal-footer"><button class="btn btn-primary">บันทึก</button></div>
      </form>
    </div></div></div>

</section></div><!-- /.content-wrapper -->
</div><!-- /.wrapper -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../dist/js/app.min.js"></script>
<script>$(function(){
  $('.sidebar-menu').tree();
  $('.btn-edit').click(function(){
      $('#edit_id').val($(this).data('id'));
      $('#edit_name').val($(this).data('name'));
  });
});</script>
</body></html>
