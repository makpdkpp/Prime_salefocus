<?php
require_once '../functions.php';
session_start();

// 1. ตรวจสอบสิทธิ์เฉพาะ role_id = 2 (Team Head)
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$mysqli = connectDb();
$userId = (int)$_SESSION['user_id']; // ID ของหัวหน้าที่ล็อกอินอยู่
$email  = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
$nname  = htmlspecialchars($_SESSION['nname'] ?? '', ENT_QUOTES, 'UTF-8');

// 2. หา "ทีมหลัก" ของหัวหน้าคนนี้ เพื่อใช้บันทึกข้อมูลโดยอัตโนมัติ
$primaryTeamId = 0;
$stmt_team = $mysqli->prepare("SELECT team_id FROM transactional_team WHERE user_id = ? LIMIT 1");
$stmt_team->bind_param('i', $userId);
$stmt_team->execute();
$result_team = $stmt_team->get_result();
if ($team_data = $result_team->fetch_assoc()) {
    $primaryTeamId = (int)$team_data['team_id'];
}
$stmt_team->close();

/* -------- ดึงข้อมูล Options สำหรับ Dropdown ต่างๆ -------- */
function loadOptions($mysqli, string $table, string $idCol, string $labelCol): array {
    $rows = $mysqli->query("SELECT `$idCol`, `$labelCol` FROM `$table` ORDER BY `$labelCol`");
    return $rows ? $rows->fetch_all(MYSQLI_ASSOC) : [];
}

$productOpts      = loadOptions($mysqli, 'product_group',   'product_id',  'product');
$companyOpts      = loadOptions($mysqli, 'company_catalog', 'company_id',  'company');
$priorityOpts     = loadOptions($mysqli, 'priority_level',  'priority_id', 'priority');
$Source_budgeOpts = loadOptions($mysqli, 'source_of_the_budget',  'Source_budget_id', 'Source_budge');

// ▼▼▼ 1. เพิ่มการดึงข้อมูลสถานะจาก DB และเตรียมตัวแปร ▼▼▼
$stepQuery = $mysqli->query("SELECT level_id, level FROM step ORDER BY level_id ASC");
$stepOpts = $stepQuery ? $stepQuery->fetch_all(MYSQLI_ASSOC) : [];
$stepData = []; // สำหรับฟอร์ม "เพิ่ม" ข้อมูลจะยังไม่มีค่าเริ่มต้น
// ▲▲▲ จบส่วนที่เพิ่ม ▲▲▲
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>เพิ่มรายละเอียดการขาย</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="../plugins_v3/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../plugins_v3/icheck-bootstrap/icheck-bootstrap.min.css">
<link rel="stylesheet" href="../dist_v3/css/adminlte.min.css">
<style>
    .sales-card{max-width:750px;margin:20px auto;background:#fff;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,.1);padding:32px 40px}
    .sales-card h2{font-weight:600;margin-bottom:30px}
    label{font-weight:500 !important}
    #product_value{text-align:right}
    .btn-back{background:#6c757d;color:#fff;border:none}
    .btn-back:hover{background:#5a6268}
    .btn-save{background:#28a745;color:#fff;border:none}
    .btn-save:hover{background:#218838}
    .process-item { display: flex; align-items: center; gap: 8px; background: #f8f9fa; padding: 8px 12px; border-radius: 6px; border: 1px solid #dee2e6; width: 100%; }
    .process-item input[type="checkbox"] { margin: 0; }
    .process-item input[type="date"] { height: 32px; font-size: 14px; }
    .content-wrapper { padding-top: 20px; }
</style>
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
                <li class="user-header bg-success"><img src="../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"><p><?= $email ?><small>Team Head</small></p></li>
                <li class="user-footer"><a href="../logout.php" class="btn btn-default btn-flat float-right">Sign out</a></li>
            </ul>
        </li>
    </ul>
</nav>
<aside class="main-sidebar sidebar-dark-success elevation-4">
    <a href="../home_admin_team.php" class="brand-link"><span class="brand-text font-weight-light"><b>Prime</b>Forecast</span></a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex"><div class="image"><img src="../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"></div><div class="info"><a href="#" class="d-block"><?= $email ?></a></div></div>
        <nav class="mt-2">
             <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">MAIN NAVIGATION</li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>My Dashboard<i class="right fas fa-angle-left"></i></p></a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="../home_admin_team.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (กราฟ)</p></a></li>
                        <li class="nav-item"><a href="../TeamAdmin/home_admin_team_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (ตาราง)</p></a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Team Dashboard<i class="right fas fa-angle-left"></i></p></a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="../TeamAdmin/team_dashboard_graph.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (กราฟ)</p></a></li>
                        <li class="nav-item"><a href="../TeamAdmin/team_dashboard_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (ตาราง)</p></a></li>
                    </ul>
                </li>
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link active"><i class="nav-icon fas fa-edit"></i><p>เพิ่มข้อมูล<i class="fas fa-angle-left right"></i></p></a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="add_infoteamadmin.php" class="nav-link active"><i class="far fa-circle nav-icon"></i><p>เพิ่มรายละเอียดการขาย</p></a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        </div>
    </aside>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid"> <div class="sales-card">
                <div class="card-header text-center"> <h2>แบบฟอร์มเพิ่มรายละเอียดการขาย</h2>
                  <p class="lead">Sales: <?= $nname ?: 'N/A' ?> (Team Head)</p>
                </div>
                <div class="card-body">
                  <form action="add_info_team.php" method="POST" id="salesForm" autocomplete="off">
                    
                    <input type="hidden" name="user_id" value="<?= $userId ?>">
                    <input type="hidden" name="team_id" value="<?= $primaryTeamId ?>">

                    <div class="row"><div class="col-sm-12 form-group"><label for="Product_detail">ชื่อโครงการ</label><input type="text" name="Product_detail" id="Product_detail" class="form-control" required></div></div>
                    <div class="row">
                      <div class="col-md-6 form-group"><label for="company_id">หน่วยงาน / บริษัท</label><select name="company_id" id="company_id" class="form-control" required><option value="">-- เลือกบริษัท/หน่วยงาน --</option><?php foreach($companyOpts as $o): ?><option value="<?= $o['company_id'] ?>"><?= htmlspecialchars($o['company']) ?></option><?php endforeach; ?></select></div>
                      <div class="col-md-6 form-group"><label for="product_value">มูลค่า (บาท)</label><input type="text" name="product_value" id="product_value" class="form-control" placeholder="0" required></div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 form-group"><label for="Source_budget_id">แหล่งที่มาของงบประมาณ</label><select name="Source_budget_id" id="Source_budget_id" class="form-control" required><option value="">-- เลือกแหล่งที่มาของงบประมาณ --</option><?php foreach($Source_budgeOpts as $o): ?><option value="<?= $o['Source_budget_id'] ?>"><?= htmlspecialchars($o['Source_budge']) ?></option><?php endforeach; ?></select></div>
                      <div class="col-md-6 form-group"><label for="fiscalyear">ปีงบประมาณ</label><select name="fiscalyear" id="fiscalyear" class="form-control" required><option value="">-- เลือกปีงบประมาณ --</option><?php $cY = date('Y') + 543; for ($i = 0; $i < 5; $i++) { $y = $cY + $i; echo "<option value=\"$y\">$y</option>"; } ?></select></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="Product_id">กลุ่มสินค้า</label>
                            <select name="Product_id" id="Product_id" class="form-control" required>
                            <option value="">-- เลือกสินค้า --</option>
                            <?php foreach($productOpts as $o): ?><option value="<?= $o['product_id'] ?>"><?= htmlspecialchars($o['product']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="priority_id">โอกาสชนะ</label>
                            <select name="priority_id" id="priority_id" class="form-control">
                            <option value="">-- เลือกระดับ --</option>
                            <?php foreach($priorityOpts as $o): ?><option value="<?= $o['priority_id'] ?>"><?= htmlspecialchars($o['priority']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4 form-group"><label for="contact_start_date">วันที่เริ่มโครงการ</label><input type="date" name="contact_start_date" id="contact_start_date" class="form-control" required></div>
                      <div class="col-md-4 form-group"><label for="date_of_closing_of_sale">วันที่คาดว่าจะ Bidding</label><input type="date" name="date_of_closing_of_sale" id="date_of_closing_of_sale" class="form-control"></div>
                      <div class="col-md-4 form-group"><label for="sales_can_be_close">วันที่คาดจะเซ็นสัญญา</label><input type="date" name="sales_can_be_close" id="sales_can_be_close" class="form-control"></div>
                    </div>

                    <div class="form-group">
                      <label>สถานะ</label>
                      <div class="row">
                        <?php foreach ($stepOpts as $step):
                            $level_id = $step['level_id'];
                            $checked = isset($stepData[$level_id]);
                            $dateVal = $checked ? $stepData[$level_id] : '';
                        ?>
                          <div class="col-12 col-lg-6 mb-2">
                            <div class="process-item">
                              <input type="hidden" name="step[<?= $level_id ?>]" value="0">
                              <div class="icheck-primary d-inline">
                                  <input type="checkbox" id="step_cb_<?= $level_id ?>" name="step[<?= $level_id ?>]" value="<?= $level_id ?>" <?= $checked ? 'checked' : '' ?> onchange="toggleDate('<?= $level_id ?>')">
                                  <label for="step_cb_<?= $level_id ?>" style="margin-bottom: 0; font-weight: normal !important;"><?= htmlspecialchars($step['level']) ?></label>
                              </div>
                              <input type="date" class="form-control form-control-sm ml-2" id="step_date_<?= $level_id ?>" name="step_date[<?= $level_id ?>]" value="<?= htmlspecialchars($dateVal) ?>" style="width: auto;" <?= $checked ? '' : 'disabled' ?>>

                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                    <div class="row"><div class="col-sm-12 form-group"><label for="remark">หมายเหตุ</label><textarea name="remark" id="remark" rows="3" class="form-control"></textarea></div></div>
                    <div class="text-right mt-4"><a href="../home_admin_team.php" class="btn btn-back">กลับหน้าหลัก</a><button type="submit" class="btn btn-save">บันทึกข้อมูล</button></div>
                  </form>
                </div>
            </div>
        </div>
    </section>
</div>
</div>
<script src="../plugins_v3/jquery/jquery.min.js"></script>
<script src="../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist_v3/js/adminlte.min.js"></script>
<script>
/* จัดรูปแบบตัวเลข */
(()=>{const f=document.getElementById('product_value');const fmt=v=>{v=v.replace(/[^0-9.]/g,'');if(!v)return '';const[x,y]=v.split('.');return(+x).toLocaleString('en-US')+(y?'.'+y.slice(0,2):'');};f.addEventListener('input',()=>{const p=f.selectionStart,l=f.value.length;f.value=fmt(f.value);f.setSelectionRange(p+(f.value.length-l),p+(f.value.length-l));});$('#salesForm').on('submit',()=>f.value=f.value.replace(/,/g,''));})();

/* ▼▼▼ 3. แก้ไขฟังก์ชัน toggleDate ให้ทำงานกับ ID ใหม่ ▼▼▼ */
function toggleDate(levelId) {
    const checkbox = document.getElementById('step_cb_' + levelId);
    const dateInput = document.getElementById('step_date_' + levelId);
    if (dateInput) {
        dateInput.disabled = !checkbox.checked;
        if (!checkbox.checked) {
            dateInput.value = '';
        }
    }
}
</script>
</body>
</html>