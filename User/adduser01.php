<?php
/* ------------------------------------------------------------------
   adduser01.php – ฟอร์มเพิ่มรายละเอียดการขาย (ปรับให้ตรงคอลัมน์ DB)
   *** ตาราง:   transaction (ภาพที่ส่งมา) ***
   ------------------------------------------------------------------ */
require_once '../functions.php';
session_start();

// ===== 1) Auth – เฉพาะผู้ใช้ role_id = 2 =====
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$mysqli = connectDb();
$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
$nname  = htmlspecialchars($_SESSION['nname'] ?? '', ENT_QUOTES, 'UTF-8');

/* -------- helper ดึง option (id => label) -------- */
function loadOptions(mysqli $db, string $table, string $idCol, string $labelCol): array {
    // ดึง option ทั่วไป (ORDER BY label ตามเดิม)
    $rows = $db->query("SELECT `$idCol`, `$labelCol` FROM `$table` ORDER BY `$labelCol`");
    return $rows ? $rows->fetch_all(MYSQLI_ASSOC) : [];
}

$productOpts  = loadOptions($mysqli, 'product_group',   'product_id',  'product');
$teamOpts     = loadOptions($mysqli, 'team_catalog',    'team_id',     'team');
$companyOpts  = loadOptions($mysqli, 'company_catalog', 'company_id',  'company');
$priorityOpts = loadOptions($mysqli, 'priority_level',  'priority_id', 'priority');
$Source_budgeOpts = loadOptions($mysqli, 'source_of_the_budget',  'Source_budget_id', 'Source_budge');
// ดึง step โดยเรียง orderlv
$stepOpts = [];
$res = $mysqli->query("SELECT level_id, level FROM step ORDER BY orderlv ASC");
if ($res) $stepOpts = $res->fetch_all(MYSQLI_ASSOC);

/* map ชื่อ field สถานะตามตาราง */
// mapping ระหว่าง level กับชื่อ field สถานะ (แก้ไขตาม field จริงใน DB)



// mapping ระหว่าง level กับชื่อ field วันที่ (แก้ไขตาม field จริงใน DB)

$steps = [];
foreach ($stepOpts as $step) {
    $level = $step['level'];
    $key = strtolower($level); // เช่น 'present', 'budgeted', 'tor', ...
    $steps[$key] = [
        'label' => $level,
        'date'  => $key . '_date',
        'level_id' => $step['level_id']
    ];
}

$row = []; // หน้า add ให้เป็น array ว่าง

?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>เพิ่มรายละเอียดการขาย</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="../plugins_v3/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist_v3/css/adminlte.min.css">

<style>
    .sales-card{max-width:750px;margin:20px auto;background:#fff;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,.1);padding:32px 40px}
    .sales-card h2{font-weight:600;margin-bottom:30px}
    label{font-weight:500 !important}
    #product_value{text-align:right}
    .btn-back{background:#6c757d;color:#fff;border:none}
    .btn-back:hover{background:#5a6268}
    .btn-save{background:#dc3545;color:#fff;border:none}
    .btn-save:hover{background:#c82333}
    .process-item {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #f8f9fa;
      padding: 8px 12px;
      border-radius: 6px;
      border: 1px solid #dee2e6;
      width: 100%;
    }
    .process-item input[type="checkbox"] { margin: 0; }
    .process-item input[type="date"] { height: 32px; font-size: 14px; }
    .content-wrapper { padding-top: 20px; }
</style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-dark bg-danger">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                <img src="../dist_v3/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
                <span class="d-none d-md-inline"><?= $email ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <li class="user-header bg-danger">
                    <img src="../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                    <p>
                        <?= $email ?>
                        <small>User</small>
                    </p>
                </li>
                <li class="user-footer">
                    <a href="../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
                </li>
            </ul>
        </li>
    </ul>
</nav>
<aside class="main-sidebar sidebar-dark-danger elevation-4">
    <a href="../home_user.php" class="brand-link">
        <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= $email ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-header">MAIN NAVIGATION</li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item"><a href="../home_user.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (กราฟ)</p></a></li>
                        <li class="nav-item"><a href="../home_user_01.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (ตาราง)</p></a></li>
                    </ul>
                </li>
                <li class="nav-item menu-open"> <a href="#" class="nav-link active"> <i class="nav-icon fas fa-edit"></i>
                        <p>เพิ่มข้อมูล<i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="../User/adduser01.php" class="nav-link active"> <i class="far fa-circle nav-icon"></i>
                                <p>เพิ่มรายละเอียดการขาย</p>
                            </a>
                        </li>
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
                  <p class="lead">Sales: <?= $nname ?: 'N/A' ?></p>
                </div>
                <div class="card-body">
                  <form action="add_user.php" method="POST" id="salesForm" autocomplete="off">
                    <input type="hidden" name="user_id" value="<?= $userId ?>">

                    <div class="row">
                      <div class="col-sm-12 form-group">
                        <label for="Product_detail">ชื่อโครงการ</label>
                        <input type="text" name="Product_detail" id="Product_detail" class="form-control" required>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="company_id">หน่วยงาน / บริษัท</label>
                        <select name="company_id" id="company_id" class="form-control" required>
                          <option value="">-- เลือกบริษัท/หน่วยงาน --</option>
                          <?php foreach($companyOpts as $o): ?>
                            <option value="<?= $o['company_id'] ?>"><?= htmlspecialchars($o['company']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="product_value">มูลค่า (บาท)</label>
                        <input type="text" name="product_value" id="product_value" class="form-control" placeholder="0" required>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 form-group">
                        <label for="Source_budget_id">แหล่งที่มาของงบประมาณ</label>
                        <select name="Source_budget_id" id="Source_budget_id" class="form-control" required>
                          <option value="">-- เลือกแหล่งที่มาของงบประมาณ --</option>
                          <?php foreach($Source_budgeOpts as $o): ?>
                            <option value="<?= $o['Source_budget_id'] ?>"><?= htmlspecialchars($o['Source_budge']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="col-md-6 form-group">
                        <label for="fiscalyear">ปีงบประมาณ</label>
                          <select name="fiscalyear" id="fiscalyear" class="form-control" required>
                              <option value="">-- เลือกปีงบประมาณ --</option>
                              <?php
                                  $currentBuddhistYear = date('Y') + 543;
                                  // แสดงปีปัจจุบัน และล่วงหน้าไปอีก 4 ปี (รวมเป็น 5 ปี)
                                  for ($i = 0; $i < 5; $i++) {
                                      $year = $currentBuddhistYear + $i;
                                      echo "<option value=\"$year\">$year</option>";
                                  }
                              ?>
                        </select>
                      </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="Product_id">กลุ่มสินค้า</label>
                            <select name="Product_id" id="Product_id" class="form-control" required>
                            <option value="">-- เลือกสินค้า --</option>
                            <?php foreach($productOpts as $o): ?>
                                <option value="<?= $o['product_id'] ?>"><?= htmlspecialchars($o['product']) ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="team_id">ทีมขาย</label>
                            <select name="team_id" id="team_id" class="form-control" required>
                            <option value="">-- เลือกทีม --</option>
                            <?php foreach($teamOpts as $o): ?>
                                <option value="<?= $o['team_id'] ?>"><?= htmlspecialchars($o['team']) ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="priority_id">โอกาสชนะ</label>
                            <select name="priority_id" id="priority_id" class="form-control">
                            <option value="">-- เลือกระดับ --</option>
                            <?php foreach($priorityOpts as $o): ?>
                                <option value="<?= $o['priority_id'] ?>"><?= htmlspecialchars($o['priority']) ?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                      <div class="col-md-4 form-group">
                        <label for="contact_start_date">วันที่เริ่มโครงการ</label>
                        <input type="date" name="contact_start_date" id="contact_start_date" class="form-control" required>
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="date_of_closing_of_sale">วันที่คาดว่าจะ Bidding</label>
                        <input type="date" name="date_of_closing_of_sale" id="date_of_closing_of_sale" class="form-control">
                      </div>
                      <div class="col-md-4 form-group">
                        <label for="sales_can_be_close">วันที่คาดจะเซ็นสัญญา</label>
                        <input type="date" name="sales_can_be_close" id="sales_can_be_close" class="form-control">
                      </div>
                    </div>

                    <div class="form-group">
                      <label>สถานะ</label>
                      <div class="row">
                        <?php foreach ($steps as $field => $cfg):
                              $checked = !empty($row[$field]);
                              $dateVal = $row[$cfg['date']] ?? '';
                        ?>
                          <div class="col-12 col-lg-6 mb-2">
                            <div class="process-item">
                              <input type="hidden" name="step[<?= $cfg['level_id'] ?>]" value="0">
                              <div class="icheck-primary d-inline">
                                  <input type="checkbox" id="step_cb_<?= $cfg['level_id'] ?>" name="step[<?= $cfg['level_id'] ?>]" value="<?= $cfg['level_id'] ?>" <?= $checked ? 'checked' : '' ?> onchange="toggleDate('<?= $cfg['level_id'] ?>')">
                                  <label for="step_cb_<?= $cfg['level_id'] ?>" style="margin-bottom: 0; font-weight: normal !important;"><?= $cfg['label'] ?></label>
                              </div>

                              <?php if ($cfg['date']): ?>
                                <input type="date" class="form-control form-control-sm ml-2" id="step_date_<?= $cfg['level_id'] ?>" name="step_date[<?= $cfg['level_id'] ?>]" value="<?= htmlspecialchars($dateVal) ?>" style="width: auto;" <?= $checked ? '' : 'disabled' ?>>
                              <?php endif; ?>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </div>


                    <div class="row"><div class="col-sm-12 form-group"><label for="remark">หมายเหตุ</label><textarea name="remark" id="remark" rows="3" class="form-control"></textarea></div></div>

                    <div class="text-right mt-4">
                      <a href="../home_user.php" class="btn btn-back">กลับหน้าหลัก</a>
                      <button type="submit" class="btn btn-save">บันทึกข้อมูล</button>
                    </div>
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
/* จัดรูปแบบตัวเลขใส่ comma */
(()=>{const f=document.getElementById('product_value');
const fmt=v=>{v=v.replace(/[^0-9.]/g,'');if(!v)return '';const[x,y]=v.split('.');return(+x).toLocaleString('en-US')+(y?'.'+y.slice(0,2):'');};
f.addEventListener('input',()=>{const p=f.selectionStart,l=f.value.length;f.value=fmt(f.value);f.setSelectionRange(p+(f.value.length-l),p+(f.value.length-l));});
$('#salesForm').on('submit',()=>f.value=f.value.replace(/,/g,''));})();
</script>
<script>
function toggleDate(levelId) {
    const checkbox = document.getElementById('step_cb_' + levelId);
    const dateInput = document.getElementById('step_date_' + levelId);
    if (!dateInput) return;
    if (checkbox.checked) {
        dateInput.removeAttribute('disabled');
    } else {
        dateInput.setAttribute('disabled', 'disabled');
        dateInput.value = '';
    }
}
</script>
</body>
</html>