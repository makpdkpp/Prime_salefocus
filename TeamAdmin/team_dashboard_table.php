<?php
require_once '../functions.php';
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: ../index.php'); exit;
}
$mysqli = connectDb();
$userId = (int)$_SESSION['user_id']; // User ID ของ Team Head ที่ Login
$email  = htmlspecialchars($_SESSION['email']);
$nname  = htmlspecialchars($_SESSION['nname'] ?? '', ENT_QUOTES, 'UTF-8');
// ดึง team_id ทั้งหมดของ user นี้
$teamIDS = [];
$q = "SELECT team_id FROM transactional_team WHERE user_id = $userId";
$res = $mysqli->query($q);
while ($row = $res->fetch_assoc()) {
    $teamIDS[] = (int)$row['team_id'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prime Forecast | Team Dashboard Table</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../dist_v3/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-dark navbar-success">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="../dist_v3/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
                    <span class="d-none d-md-inline"><?= $email ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-success">
                        <img src="../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                        <p><?= $email ?><small>Team Head</small></p>
                    </li>
                    <li class="user-footer">
                        <a href="../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-success elevation-4">
        <a href="../home_admin_team.php" class="brand-link">
             <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image"><a href="../User/edit_profile.php"><img src="../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"></a></div>
                <div class="info"><a href="#" class="d-block"><?= $email ?></a></div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">MAIN NAVIGATION</li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>My Dashboard<i class="right fas fa-angle-left"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="../home_admin_team.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (กราฟ)</p></a></li>
                            <li class="nav-item"><a href="home_admin_team_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (ตาราง)</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active"><i class="nav-icon fas fa-users"></i><p>Team Dashboard<i class="right fas fa-angle-left"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="team_dashboard_graph.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (กราฟ)</p></a></li>
                            <li class="nav-item"><a href="team_dashboard_table.php" class="nav-link active"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (ตาราง)</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>เพิ่มข้อมูล<i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="add_infoteamadmin.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>เพิ่มรายละเอียดการขาย</p></a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1>Team Dashboard</h1></div></div></div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="card">
                     <div class="card-header"><h3 class="card-title">Team's Forecast Data</h3></div>
                    <div class="card-body">
                        <table id="salesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                  <th>ชื่อโครงการ</th><th>หน่วยงาน/บริษัท</th><th>มูลค่า (฿)</th><th>แหล่งงบประมาณ</th><th>ปีงบประมาณ</th><th>กลุ่มสินค้า</th><th>ทีม</th><th>พนักงานขาย</th><th>โอกาสชนะ</th><th>วันที่เริ่ม</th><th>วันยื่น Bidding</th><th>วันเซ็นสัญญา</th><th>สถานะ</th><th>หมายเหตุ</th><th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php
                                // --- CHANGE 1: START - แก้ไข SQL QUERY ---
                                // Query นี้จะดึงข้อมูลการขายทั้งหมดของ User ที่อยู่ในทีมเดียวกันกับ Team Head ($userId)
                                if ($teamIDS) {
                                    $teamIdList = implode(',', $teamIDS);
                                    $q = " SELECT t.*, pg.product, cc.company, pl.priority, tc.team, u.nname, so.Source_budge,
                                      (
                                        SELECT s2.level
                                        FROM transactional_step ts2
                                        JOIN step s2 ON s2.level_id = ts2.level_id
                                        WHERE ts2.transac_id = t.transac_id
                                        ORDER BY ts2.date DESC, ts2.transacstep_id DESC
                                        LIMIT 1
                                      ) AS level
                                    FROM transactional t
                                    LEFT JOIN product_group   pg ON t.Product_id = pg.product_id
                                    LEFT JOIN company_catalog cc ON t.company_id = cc.company_id
                                    LEFT JOIN priority_level  pl ON t.priority_id = pl.priority_id
                                    LEFT JOIN team_catalog    tc ON t.team_id     = tc.team_id
                                    LEFT JOIN user            u  ON t.user_id     = u.user_id
                                    LEFT JOIN source_of_the_budget so ON so.Source_budget_id = t.Source_budget_id
                                    WHERE t.team_id IN ($teamIdList)";
                                    $rs = $mysqli->query($q);
                                } else {
                                    $rs = false;
                                }
                                // --- CHANGE 1: END - แก้ไข SQL QUERY ---
                                if ($rs && $rs->num_rows):
                                    while($r=$rs->fetch_assoc()):
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
                                    <td class="text-center"><a href="edit_admin.php?id=<?= $r['transac_id']?>" class="btn btn-sm btn-info"><i class="fas fa-pencil-alt"></i></a></td>
                                </tr>
                              <?php
                                    endwhile;
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
<script src="../dist_v3/js/adminlte.min.js"></script>

<script>
  $(function () {
    $("#salesTable").DataTable({
      "responsive": true, "lengthChange": true, "autoWidth": false,
      "order": [[ 0, "desc" ]], // จัดเรียงตามคอลัมน์แรก (ชื่อโครงการ) จากใหม่ไปเก่า
      "language": { "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json" }
    });
  });
</script>
</body>
</html>