<?php
require_once '../../functions.php';
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php'); exit;
}

$mysqli = connectDb();
$email  = htmlspecialchars($_SESSION['email'] ?? '');

$q = "SELECT t.*, pg.product, cc.company, pl.priority, tc.team, s.level, u.nname, so.Source_budge
        FROM transactional t
        LEFT JOIN product_group   pg ON t.Product_id = pg.product_id
        LEFT JOIN company_catalog cc ON t.company_id = cc.company_id
        LEFT JOIN priority_level  pl ON t.priority_id = pl.priority_id
        LEFT JOIN team_catalog    tc ON t.team_id     = tc.team_id
        LEFT JOIN step            s  ON t.Step_id     = s.level_id
        LEFT JOIN user            u  ON t.user_id     = u.user_id
        LEFT JOIN source_of_the_budget so ON so.Source_budget_id = t.Source_budget_id";

$rs = $mysqli->query($q);
$all_data = [];
if ($rs) {
    $all_data = $rs->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard (ตาราง) | PrimeForecast</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../../dist_v3/css/adminlte.min.css">
    <style>
      .main-header.navbar {
          border-bottom: none;
      }
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
          <img src="../../dist_v3/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?= $email ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="../../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            <p><?= $email ?><small>Administrator</small></p>
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
          <a href="#" class="d-block"><?= $email ?></a>
          <a href="#" class="d-block" style="color: #c2c7d0; font-size: 0.9em;"><i class="fa fa-circle text-success" style="font-size: 0.7em;"></i> Online</a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN NAVIGATION</li>
          <li class="nav-item menu-is-opening menu-open">
            <a href="#" class="nav-link active">
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
                <a href="super_admin_table.php" class="nav-link active">
                  <i class="fas fa-table nav-icon"></i>
                  <p>Dashboard (ตาราง)</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-folder-open"></i><p>เพิ่มข้อมูล....<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="top-nav.php" class="nav-link"><i class="fas fa-building nav-icon"></i><p>เพิ่มข้อมูลบริษัท</p></a></li>
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
            <div class="container-fluid">
              <div class="row mb-2">
                <div class="col-sm-6"><h1>Sales Dashboard (ตาราง)</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../../home_admin.php">หน้าหลัก</a></li>
                        <li class="breadcrumb-item active">Dashboard (ตาราง)</li>
                    </ol>
                </div>
              </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Forecast Data Table (All Users)</h3></div>
                    <div class="card-body">
                        <table id="salesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                  <th>ชื่อโครงการ</th><th>หน่วยงาน/บริษัท</th><th>มูลค่า (฿)</th><th>แหล่งงบประมาณ</th><th>ปีงบประมาณ</th><th>กลุ่มสินค้า</th>
                                  <th>ทีม</th><th>ชื่อผู้ใช้</th><th>โอกาสชนะ</th><th>วันที่เริ่ม</th><th>วันยื่น Bidding</th><th>วันเซ็นสัญญา</th><th>สถานะ</th><th>หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php
                                if (!empty($all_data)):
                                    foreach($all_data as $r):
                              ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['Product_detail']) ?></td>
                                    <td><?= htmlspecialchars($r['company']) ?></td>
                                    <td><?= number_format($r['product_value']) ?></td>
                                    <td><?= htmlspecialchars($r['Source_budge']) ?></td>
                                    <td><?= htmlspecialchars($r['fiscalyear']) ?></td>
                                    <td><?= htmlspecialchars($r['product']) ?></td>
                                    <td><?= htmlspecialchars($r['team']) ?></td>
                                    <td><?= htmlspecialchars($r['nname']) ?></td>
                                    <td><?= htmlspecialchars($r['priority']) ?></td>
                                    <td><?= htmlspecialchars($r['contact_start_date']) ?></td>
                                    <td><?= htmlspecialchars($r['date_of_closing_of_sale']) ?></td>
                                    <td><?= htmlspecialchars($r['sales_can_be_close']) ?></td>
                                    <td><?= htmlspecialchars($r['level']) ?></td>
                                    <td><?= htmlspecialchars($r['remark']) ?></td>
                                </tr>
                              <?php
                                    endforeach;
                                endif;
                              ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>

<script>
  $(function () {
    console.log('Script has been loaded and is ready.');
    const salesDataFromPHP = <?php echo json_encode($all_data); ?>;
    console.log('Data from Server:', salesDataFromPHP);
    $("#salesTable").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json" }
    });
  });
</script>
</body>
</html>