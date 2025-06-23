<?php
require_once 'functions.php';
session_start();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$mysqli = connectDb();
$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8');
$nname  = htmlspecialchars($_SESSION['nname'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Prime Forecast 25 • User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===== CSS Dependencies ===== -->
    <!-- Bootstrap 3.3.7 CSS (เข้ากันกับ AdminLTE 2) -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

    <style>
      body { background: #f4f6f9; }
      .content { padding: 25px; }
      .table-responsive { background: #fff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.08); padding: 15px; }
      table.dataTable thead th, table.dataTable thead td {
        background: #a40000 !important;
        color: #fff !important;
        border-bottom: 1px solid #900000 !important;
      }
      table.dataTable.no-footer { border-bottom: 1px solid #ddd; }
      .dataTables_wrapper .dataTables_filter input { border-radius: 4px; border: 1px solid #ccc; padding: 5px; }
      .dataTables_wrapper .dataTables_length select { border-radius: 4px; border: 1px solid #ccc; padding: 5px; }
      .no-data { padding: 40px 0; text-align: center; font-size: 18px; color: #c00; background: #fff; border-radius: 8px; }
    </style>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

    <!-- =====================================================
         Header
    ====================================================== -->
    <header class="main-header">
        <a href="home_user.php" class="logo"><b>Prime</b>Forecast</a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?= $email ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                                <p><?= $email ?> <small>User</small></p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- =====================================================
         Sidebar
    ====================================================== -->
    <aside class="main-sidebar">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= $email ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="home_user.php"><i class="fa fa-circle-o"></i> Dashboard (กราฟ)</a></li>
                        <li class="active"><a href="home_user_01.php"><i class="fa fa-circle-o"></i> Dashboard (ตาราง)</a></li>
                    </ul>
                </li>
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="User/adduser01.php"><i class="fa fa-circle-o"></i> เพิ่มรายละเอียดการขาย</a></li>
                    </ul>
                </li>
            </ul>
        </section>
    </aside>

    <!-- ===========================================================  CONTENT  -->
    <div class="content-wrapper">
        <section class="content">
            <?php
            $q = "SELECT t.*, pg.product, cc.company, pl.priority, tc.team, s.level, u.nname, so.Source_budge
                    FROM transactional t
                    LEFT JOIN product_group   pg ON t.Product_id = pg.product_id
                    LEFT JOIN company_catalog cc ON t.company_id = cc.company_id
                    LEFT JOIN priority_level  pl ON t.priority_id = pl.priority_id
                    LEFT JOIN team_catalog    tc ON t.team_id     = tc.team_id
                    LEFT JOIN step            s  ON t.Step_id     = s.level_id
                    LEFT JOIN user            u  ON t.user_id     = u.user_id
                    LEFT JOIN source_of_the_budget so ON so.Source_budget_id = t.Source_budget_id
                    WHERE t.user_id = ?";
            
            $stmt = $mysqli->prepare($q);
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $rs = $stmt->get_result();

            if ($rs && $rs->num_rows > 0):
            ?>
              <div class="table-responsive">
                <h2>ข้อมูลการขายของ: <?= $nname ?: 'Sales' ?></h2>
                <table id="salesTable" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>ชื่อโครงการ</th><th>หน่วยงาน/บริษัท</th><th>มูลค่า (฿)</th>
                      <th>แหล่งงบประมาณ</th><th>ปีงบประมาณ</th><th>กลุ่มสินค้า</th>
                      <th>ทีม</th><th>โอกาสชนะ</th>
                      <th>วันที่เริ่ม</th><th>วันที่ยื่น Bidding</th><th>วันที่เซ็นสัญญา</th><th>ขั้นตอน</th><th>หมายเหตุ</th><th width="50">แก้ไข</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php while($r = $rs->fetch_assoc()): ?>
                    <tr>
                      <td><?= htmlspecialchars($r['Product_detail']) ?></td>
                      <td><?= htmlspecialchars($r['company']) ?></td>
                      <td class="text-right"><?= number_format($r['product_value']) ?></td>
                      <td><?= htmlspecialchars($r['Source_budge']) ?></td>
                      <td><?= htmlspecialchars($r['fiscalyear']) ?></td>
                      <td><?= htmlspecialchars($r['product']) ?></td>
                      <td><?= htmlspecialchars($r['team']) ?></td>
                      <td><?= htmlspecialchars($r['priority']) ?></td>
                      <td><?= htmlspecialchars($r['contact_start_date']) ?></td>
                      <td><?= htmlspecialchars($r['date_of_closing_of_sale']) ?></td>
                      <td><?= htmlspecialchars($r['sales_can_be_close']) ?></td>
                      <td><?= htmlspecialchars($r['level']) ?></td>
                      <td><?= htmlspecialchars($r['remark']) ?></td>
                      <td class="text-center">
                        <a href="User/edit_adduser.php?id=<?= $r['transac_id']?>" class="btn btn-xs btn-warning">
                          <i class="fa fa-pencil"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <div class="no-data">-- ไม่พบข้อมูลการขายในระบบ --</div>
            <?php 
            endif;
            $stmt->close();
            $mysqli->close();
            ?>
        </section>
    </div><!-- /.content-wrapper -->
</div><!-- /.wrapper -->

<!-- ========== JS Dependencies (จัดเรียงใหม่ทั้งหมด) ========== -->
<!-- 1. jQuery 2.2.4 (เวอร์ชันที่เข้ากับ AdminLTE 2) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<!-- 2. Bootstrap 3.3.7 JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!-- 3. DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<!-- 4. AdminLTE App JS -->
<script src="dist/js/app.min.js"></script>

<script>
  $(document).ready(function() {
    // คำสั่ง .tree() ของ AdminLTE 2 จะกลับมาทำงานได้ปกติ
    // ไม่จำเป็นต้องเขียนโค้ดสำหรับเมนูเองแล้ว
    
    // เปิดใช้งาน DataTables
    $('#salesTable').DataTable({
      "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Thai.json"
      },
      "order": [], 
      "pageLength": 10
    });
  });
</script>

</body>
</html>