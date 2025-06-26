<?php
session_start();
include("../../functions.php");
$mysqli = connectDb();

// กำหนดตัวเลือกจำนวนแถว
$limitOptions = [10, 25, 50, 100];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limitOptions) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// รับค่ากรอง Industry
$filterIndustry = isset($_GET['industry_filter']) && $_GET['industry_filter'] !== '' ? (int)$_GET['industry_filter'] : '';
$where = $filterIndustry ? "WHERE cc.Industry_id = {$filterIndustry}" : '';

// คำนวณ pagination
$totalQuery = $mysqli->query("SELECT COUNT(*) as total FROM company_catalog cc $where");
$totalRow = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRow / $limit);

// จัดเรียง A–Z ก่อน ก–ฮ
$collationLatin = 'utf8mb4_unicode_ci';
$orderExpr = "CASE WHEN cc.company REGEXP '^[A-Za-z]' THEN 0 ELSE 1 END, cc.company COLLATE $collationLatin ASC";

// ดึงข้อมูล
$sql = "SELECT cc.company_id, cc.company, cc.Industry_id, ig.Industry
        FROM company_catalog cc
        LEFT JOIN industry_group ig ON cc.Industry_id = ig.Industry_id
        $where
        ORDER BY $orderExpr
        LIMIT $start, $limit";
$companies = $mysqli->query($sql);

// ดึง list อุตสาหกรรม สำหรับ dropdown กรอง
$industries = $mysqli->query("SELECT Industry_id, Industry FROM industry_group ORDER BY Industry COLLATE utf8mb4_unicode_ci");
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ข้อมูลบริษัท | PrimeForecast</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../../plugins_v3/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../dist_v3/css/adminlte.min.css">
  <style>
    body { background-color: #f4f6f9; }
    .content-wrapper { background-color: #b3d6e4; }
    .container1 { background: #fff; border-radius: 10px; padding: 25px; margin: 20px auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1); max-width: 1100px; }
    .table thead { background: #0056b3; color: #fff; }
    .btn-add { position: fixed; bottom: 30px; right: 30px; background: #0056b3; color: #fff; border-radius: 50%; width: 56px; height: 56px; font-size: 24px; border: none; z-index: 1040; }
    .modal-content { border-radius: 10px; padding: 20px; }
    .pagination .page-item.active .page-link { background-color: #0056b3; border-color: #0056b3; }
    .main-header.navbar { border-bottom: none; }
    .sidebar {
    padding-bottom: 50px; /* เพิ่มพื้นที่ว่างด้านล่างของ Sidebar */
  }
</style>
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
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
            <p>
              <?php echo $_SESSION['email'] ?? ''; ?>
              <small>Admin</small>
            </p>
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
    <img src="../../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
  </div>
  <div class="info">
    <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></a>
    <a href="#" class="d-block" style="color: #c2c7d0; font-size: 0.9em;"><i class="fa fa-circle text-success" style="font-size: 0.7em;"></i> Online</a>
  </div>
</div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN NAVIGATION</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../../home_admin.php" class="nav-link">
                  <i class="far fa-chart-bar nav-icon"></i>
                  <p>Dashboard (กราฟ)</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="super_admin_table.php" class="nav-link">
                  <i class="fas fa-table nav-icon"></i>
                  <p>Dashboard (ตาราง)</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item menu-is-opening menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-folder-open"></i><p>เพิ่มข้อมูล....<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="top-nav.php" class="nav-link active"><i class="fas fa-building nav-icon"></i><p>เพิ่มข้อมูลบริษัท</p></a></li>
              <li class="nav-item"><a href="boxed.php" class="nav-link"><i class="fas fa-boxes nav-icon"></i><p>เพิ่มข้อมูลกลุ่มสินค้า</p></a></li>
              <li class="nav-item"><a href="fixed.php" class="nav-link"><i class="fas fa-industry nav-icon"></i><p>เพิ่มข้อมูลอุตสาหกรรม</p></a></li>
              <li class="nav-item"><a href="Source_of_the_budget.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>เพิ่มข้อมูลที่มาของงบประมาณ</p></a></li>
              <li class="nav-item"><a href="collapsed-sidebar.php" class="nav-link"><i class="fas fa-tasks nav-icon"></i><p>ขั้นตอนการขาย</p></a></li>
              <li class="nav-item"><a href="of_winning.php" class="nav-link"><i class="fas fa-trophy nav-icon"></i><p>โอกาสการชนะ</p></a></li>
              <li class="nav-item"><a href="Saleteam.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>ทีมขาย</p></a></li>
              <li class="nav-item"><a href="position_u.php" class="nav-link"><i class="fas fa-user-tag nav-icon"></i><p>ตำแหน่ง</p></a></li>
              <li class="nav-item"><a href="Profile_user.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>รายละเอียดผู้ใช้งาน</p></a></li>
              <li class="nav-item"><a href="newuser.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
            </ul>
          </li>
        </ul>
      </nav>
      </div>
    </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"></div><div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="../../home_admin.php">หน้าหลัก</a></li><li class="breadcrumb-item active">ข้อมูลบริษัท</li></ol></div></div></div>
    </section>
    <section class="content">
      <div class="container1">
        <h1>ข้อมูลบริษัท</h1>
        <input id="searchInput" class="form-control mb-3" type="text" placeholder="ค้นหาบริษัท...">

        <!-- ฟอร์มกรองและจำนวนแถว -->
        <form method="get" class="form-inline mb-3">
          <label class="mr-2">กรองอุตสาหกรรม:</label>
          <select name="industry_filter" class="form-control mr-3" onchange="this.form.submit()">
            <option value="">-- ทุกกลุ่ม --</option>
            <?php while($rowI = $industries->fetch_assoc()): ?>
            <option value="<?= $rowI['Industry_id'] ?>" <?= $filterIndustry == $rowI['Industry_id'] ? 'selected' : '' ?>><?= htmlspecialchars($rowI['Industry']) ?></option>
            <?php endwhile; ?>
          </select>

          <label class="mr-2">จำนวนแถว:</label>
          <select name="limit" class="form-control mr-2" onchange="this.form.submit()">
            <?php foreach ($limitOptions as $opt): ?>
            <option value="<?= $opt ?>" <?= $opt == $limit ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="page" value="<?= $page ?>">
        </form>

        <!-- ตารางข้อมูลบริษัท -->
        <table class="table table-bordered table-hover">
          <thead>
            <tr><th>บริษัท</th><th>อุตสาหกรรม</th><th style="width:160px;">Actions</th></tr>
          </thead>
          <tbody id="companyTable">
            <?php while ($c = $companies->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($c['company']) ?></td>
              <td><?= htmlspecialchars($c['Industry']) ?></td>
              <td>
                <button class="btn btn-sm btn-info btn-edit" data-id="<?= $c['company_id'] ?>" data-name="<?= htmlspecialchars($c['company'], ENT_QUOTES) ?>" data-industry-id="<?= $c['Industry_id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                <a href="delete_Top.php?company_id=<?= $c['company_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')"><i class="fas fa-trash"></i> Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

        <!-- pagination -->
        <nav>
          <ul class="pagination justify-content-center mt-3">
            <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&limit=<?= $limit ?>&industry_filter=<?= $filterIndustry ?>">ก่อนหน้า</a></li>
            <?php for($i=1;$i<=$totalPages;$i++): ?>
            <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>&industry_filter=<?= $filterIndustry ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $page>=$totalPages?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&limit=<?= $limit ?>&industry_filter=<?= $filterIndustry ?>">ถัดไป</a></li>
          </ul>
        </nav>

        <button class="btn-add" data-toggle="modal" data-target="#addModal">
            <i class="fa fa-plus"></i>
        </button>

        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="addModalLabel">เพิ่มชื่อข้อมูลบริษัท</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="add_company.php" method="POST">
                            <div class="form-group">
                                <label>ชื่อบริษัท:</label>
                                <input type="text" name="company" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>กลุ่มอุตสาหกรรม:</label>
                                <select name="industry" class="form-control" required>
                                    <option value="">-- เลือกกลุ่มอุตสาหกรรม --</option>
                                    <?php
                                    $result = $mysqli->query("SELECT Industry_id, Industry FROM industry_group");
                                    while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['Industry_id']}'>" . htmlspecialchars($row['Industry']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">บันทึกข้อมูล</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">แก้ไขข้อมูลบริษัท</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editForm" action="edit_top.php" method="POST">
                            <input type="hidden" name="company_id" id="edit_company_id">
                            <div class="form-group">
                                <label>ชื่อบริษัท:</label>
                                <input type="text" name="company" id="edit_company_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>กลุ่มอุตสาหกรรม:</label>
                                <select name="industry" id="edit_industry" class="form-control" required>
                                    <option value="">-- เลือกกลุ่มอุตสาหกรรม --</option>
                                    <?php
                                    $result = $mysqli->query("SELECT Industry_id, Industry FROM industry_group");
                                    while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['Industry_id']}'>" . htmlspecialchars($row['Industry']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">บันทึกการแก้ไข</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
      </div>
    </section>
  </div>
</div>
<script src="../../plugins_v3/jquery/jquery.min.js"></script>
<script src="../../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>
<script>
$(function(){
  $('#searchInput').on('keyup',function(){var v=$(this).val().toLowerCase();$('#companyTable tr').filter(function(){$(this).toggle($(this).text().toLowerCase().indexOf(v)>-1);});});
  $('#companyTable').on('click','.btn-edit',function(){var id=$(this).data('id'),name=$(this).data('name'),indId=$(this).attr('data-industry-id');$('#edit_company_id').val(id);$('#edit_company_name').val(name);$('#edit_industry').val(indId);$('#editModal').modal('show');});
});
</script>
</body>
</html>
