<?php
require_once 'functions.php';
session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: index.php'); exit;
}
$mysqli = connectDb();
$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email']);
$nname  = htmlspecialchars($_SESSION['nname'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Prime Forecast 25 • User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <style>
      table.dataTable thead th {
        background: #a40000 !important;
        color: #fff !important;
      }
      .dataTables_filter input {
        border-radius: 4px;
        border: 1px solid #ccc;
        padding: 4px 8px;
      }
      body { background: #f4f6f9; }
      .content { padding: 25px; }
      .deal-table { background: #fff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
      .deal-table th { background: #a40000; color: #fff; border: none; }
      .deal-table td { vertical-align: middle; }
      .deal-table tbody tr:hover { background: #fafafa; }
      .no-data { padding: 40px 0; text-align: center; font-size: 18px; color: #c00; }
    </style>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="dist/js/app.min.js"></script>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

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

<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            <div class="pull-left image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p><?= $email ?> (User)</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
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

<div class="content-wrapper">
<section class="content">

<?php
$q = "SELECT t.*, pg.product, cc.company, pl.priority, tc.team, s.level, u.nname, so.Source_budge
      FROM transactional t
      LEFT JOIN product_group pg ON t.Product_id = pg.product_id
      LEFT JOIN company_catalog cc ON t.company_id = cc.company_id
      LEFT JOIN priority_level pl ON t.priority_id = pl.priority_id
      LEFT JOIN team_catalog tc ON t.team_id = tc.team_id
      LEFT JOIN step s ON t.Step_id = s.level_id
      LEFT JOIN user u ON t.user_id = u.user_id
      LEFT JOIN source_of_the_budget so ON so.Source_budget_id = t.Source_budget_id
      WHERE t.user_id = $userId";
$rs = $mysqli->query($q);

if ($rs && $rs->num_rows):
?>
  <div class="table-responsive">
    <h2><?= $nname ?: 'Sales' ?></h2>
    <table class="table table-bordered table-hover deal-table">
      <thead>
        <tr>
          <th>ชื่อโครงการ</th><th>หน่ยงาน/บริษัท</th><th>มูลค่า (฿)</th>
          <th>เเหล่งที่มาของงบประมาณ</th><th>ปีงบประมาณ</th><th>กลุ่มสินค้า</th>
          <th>ทีม</th><th>โอกาสชนะ</th>
          <th>วันที่เริ่มโครงการ</th><th>วันที่คาดจะยืนBidding</th><th>วันที่คาดจะเซ็นสัญญา</th><th>ขั้นตอน</th><th>หมายเหตุ</th><th width="70"> </th>
        </tr>
      </thead>
      <tbody>
      <?php while($r=$rs->fetch_assoc()): ?>
        <tr>
        <td><?= $r['Product_detail'] ?></td>
        <td><?= $r['company'] ?></td>
        <td><?= number_format($r['product_value']) ?></td>
        <td><?= $r['Source_budge'] ?></td>
        <td><?= $r['fiscalyear'] ?></td>
        <td><?= $r['product'] ?></td>
        <td><?= $r['team'] ?></td>
        <td><?= $r['priority'] ?></td>
        <td><?= $r['contact_start_date'] ?></td>
        <td><?= $r['date_of_closing_of_sale'] ?></td>
        <td><?= $r['sales_can_be_close'] ?></td>
        <td><?= $r['level'] ?></td>
        <td><?= $r['remark'] ?></td>
        <td class="text-center">
          <a href="User/edit_adduser.php?id=<?= $r['transac_id']?>"><i class="fa fa-pencil-square-o" style="color:#4caf50"></i></a>
        </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="no-data">-- ไม่พบข้อมูลในระบบ --</div>
<?php endif; ?>

</section>
</div>
</div>

<script src="dist/js/app.min.js"></script>
<script>
$(function () {
    $('.sidebar-menu').tree();
    $('.deal-table').DataTable({
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json'
        },
        order: [],
        pageLength: 10,
        dom: 'lfrtip'
    });
});
</script>
</body>
</html>
