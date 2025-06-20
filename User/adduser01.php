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
    $rows = $db->query("SELECT `$idCol`, `$labelCol` FROM `$table` ORDER BY `$labelCol`");
    return $rows ? $rows->fetch_all(MYSQLI_ASSOC) : [];
}

$productOpts  = loadOptions($mysqli, 'product_group',   'product_id',  'product');
$teamOpts     = loadOptions($mysqli, 'team_catalog',    'team_id',     'team');
$companyOpts  = loadOptions($mysqli, 'company_catalog', 'company_id',  'company');
$priorityOpts = loadOptions($mysqli, 'priority_level',  'priority_id', 'priority');
$Source_budgeOpts = loadOptions($mysqli, 'source_of_the_budget',  'Source_budget_id', 'Source_budge');

/* map ชื่อ field สถานะตามตาราง */
/* ====== Process + วันที่ ====== */
$steps = [
    'present'  => ['label'=>'Present',  'date'=>null],
    'budgeted' => ['label'=>'Budget',  'date'=>null],
    'tor'      => ['label'=>'TOR',     'date'=>'tor_date'],
    'bidding'  => ['label'=>'Bidding', 'date'=>'bidding_date'],
    'win'      => ['label'=>'WIN',     'date'=>'win_date'],
    'lost'     => ['label'=>'LOST',    'date'=>'lost_date']
];

?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>เพิ่มรายละเอียดการขาย</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
<link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  

<style>
    .sales-card{max-width:750px;margin:40px auto;background:#fff;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,.08);padding:32px 40px}
    .sales-card h2{font-weight:600;margin-bottom:30px}
    label{font-weight:500}
    #product_value{text-align:right}
    .btn-back{background:#888;color:#fff;border:none}
    .btn-back:hover{background:#6e6e6e}
    .btn-save{background:#c82333;color:#fff;border:none}
    .btn-save:hover{background:#a51e29}
    .process-group {
  display: flex;
  flex-wrap: wrap;
  gap: 16px; /* ระยะห่างระหว่างกล่อง */
  align-items: center;
}

.process-item {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f9f9f9;
  padding: 6px 12px;
  border-radius: 6px;
  border: 1px solid #ddd;
}

.process-item input[type="checkbox"] {
  margin: 0;
}

.process-item input[type="date"] {
  height: 32px;
  font-size: 14px;
}

</style>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">
<header class="main-header">
  <a href="../home_user.php" class="logo"><b>Prime</b>Forecast</a>
  <nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only">Toggle</span></a>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="User Image" />
            <span class="hidden-xs"><?php echo $email; ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
              <p><?php echo $email; ?><small>User</small></p>
            </li>
            <li class="user-footer">
              <div class="pull-right"><a href="../logout.php" class="btn btn-default btn-flat">Sign out</a></div>
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
        <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
      </div>
      <div class="pull-left info">
        <p><?php echo $email; ?> (User)</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            <li class="active treeview">
              <a href="../home_user.php">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="active"><a href="../home_user.php"><i class="fa fa-circle-o"></i>Dashboard (กราฟ)</a></li>
                <li class="active"><a href="../home_user_01.php"><i class="fa fa-circle-o"></i>Dashboard (ตาราง)</a></li>
              </ul>
            </li>
            <!-- Add data -->
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="active"><a href="../User/adduser01.php"><i class="fa fa-circle-o"></i> เพิ่มรายละเอียดการขาย</a></li>
                    </ul>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

<!-- ========== CONTENT ========== -->
<div class="content-wrapper"><section class="content">
<div class="sales-card">
  <h2><?= $nname ?: 'Sales' ?></h2>

  <form action="add_user.php" method="POST" id="salesForm" autocomplete="off">
    <input type="hidden" name="user_id" value="<?= $userId ?>">

    <!-- ====== Row 1 : โครงการ / บริษัท / มูลค่า ====== -->
    <div class="row">
      <div class="col-sm-12 form-group">
        <label for="Product_detail">ชื่อโครงการ</label>
        <input type="text" name="Product_detail" id="Product_detail" class="form-control" required>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 form-group">
        <label for="company_id">หน่วยงาน / บริษัท</label>
        <select name="company_id" id="company_id" class="form-control" required>
          <option value="">-- เลือกบริษัท/หน่วยงาน --</option>
          <?php foreach($companyOpts as $o): ?>
            <option value="<?= $o['company_id'] ?>"><?= htmlspecialchars($o['company']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-6 form-group">
        <label for="product_value">มูลค่า (บาท)</label>
        <input type="text" name="product_value" id="product_value" class="form-control" placeholder="0" required>
      </div>
    </div>

    <!-- ====== Row 2 : กลุ่มสินค้า / ทีม / Priority ====== -->
    <div class="row">
      <div class="col-sm-6 form-group">
        <label for="Source_budget_id ">เเหล่งที่มาของงบประมาณ</label>
        <select name="Source_budget_id" id="Source_budget_id" class="form-control" required>
          <option value="">-- เลือกแหล่งที่มาของงบประมาณ --</option>
          <?php foreach($Source_budgeOpts as $o): ?>
            <option value="<?= $o['Source_budget_id'] ?>"><?= htmlspecialchars($o['Source_budge']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="row">
      <div class="col-sm-6 form-group">
        <label for="fiscalyear">ปีงบประมาณ:</label>  
          <select name="fiscalyear" id="fiscalyear" class="form-control" required>
              <option value="2568">2568</option>
              <option value="2569">2569</option>
              <option value="2570">2570</option>
              <option value="2571">2571</option>
</select>
      </div>
    </div>


      <div class="col-sm-4 form-group">
        <label for="Product_id">กลุ่มสินค้า</label>
        <select name="Product_id" id="Product_id" class="form-control" required>
          <option value="">-- เลือกสินค้า --</option>
          <?php foreach($productOpts as $o): ?>
            <option value="<?= $o['product_id'] ?>"><?= htmlspecialchars($o['product']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-4 form-group">
        <label for="team_id">ทีมขาย</label>
        <select name="team_id" id="team_id" class="form-control" required>
          <option value="">-- เลือกทีม --</option>
          <?php foreach($teamOpts as $o): ?>
            <option value="<?= $o['team_id'] ?>"><?= htmlspecialchars($o['team']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-4 form-group">
        <label for="priority_id">โอกาสชนะ</label>
        <select name="priority_id" id="priority_id" class="form-control">
          <option value="">-- เลือกระดับ --</option>
          <?php foreach($priorityOpts as $o): ?>
            <option value="<?= $o['priority_id'] ?>"><?= htmlspecialchars($o['priority']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <!-- ====== Row 3 : วันที่ ====== -->
    <div class="row">
      <div class="col-sm-4 form-group">
        <label for="contact_start_date">วันที่เริ่มโครงการ</label>
        <input type="date" name="contact_start_date" id="contact_start_date" class="form-control" required>
      </div>
      <div class="col-sm-4 form-group">
        <label for="date_of_closing_of_sale">วันที่คาดจะยืนBidding</label>
        <input type="date" name="date_of_closing_of_sale" id="date_of_closing_of_sale" class="form-control">
      </div>
      <div class="col-sm-4 form-group">
        <label for="sales_can_be_close">วันที่คาดจะเซ็นสัญญา</label>
        <input type="date" name="sales_can_be_close" id="sales_can_be_close" class="form-control">
      </div>
    </div>

<div class="form-group">
  <label>สถานะ</label>
  <div class="process-group">
    <?php foreach ($steps as $field => $cfg): 
          $checked = !empty($row[$field]);
          $dateVal = $row[$cfg['date']] ?? '';
    ?>
      <div class="process-item">
        <!-- hidden เพื่อส่งค่า 0 ถ้าไม่ติ๊ก -->
        <input type="hidden" name="<?= $field ?>" value="0">

        <!-- checkbox -->
        <input type="checkbox"
               id="<?= $field ?>_cb"
               name="<?= $field ?>"
               value="1"
               <?= $checked ? 'checked' : '' ?>
               onchange="toggleDate('<?= $field ?>')">

        <label for="<?= $field ?>_cb" style="margin-bottom: 0;"><?= $cfg['label'] ?></label>

        <?php if ($cfg['date']): ?>
          <input type="date"
                 id="<?= $field ?>_date"
                 name="<?= $cfg['date'] ?>"
                 value="<?= htmlspecialchars($dateVal) ?>"
                 <?= $checked ? '' : 'disabled' ?>>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>


    <!-- ====== หมายเหตุ ====== -->
    <div class="row"><div class="col-sm-12 form-group"><label for="remark">หมายเหตุ</label><textarea name="remark" id="remark" rows="2" class="form-control"></textarea></div></div>

    <!-- ====== ปุ่ม ====== -->
    <div class="text-right mt-4">
      <a href="../home_user.php" class="btn btn-back">กลับหน้าหลัก</a>
      <button type="submit" class="btn btn-save">บันทึก</button>
    </div>
  </form>
</div>
</section></div><!-- /.content-wrapper -->
</div><!-- /.wrapper -->


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/app.min.js"></script>
<script>
  $(function () { $('.sidebar-menu').tree(); });
</script>

<script>
/* มูลค่า -> คอมมา */
(()=>{const f=document.getElementById('product_value');
const fmt=v=>{v=v.replace(/[^0-9.]/g,'');if(!v)return '';const[x,y]=v.split('.');return(+x).toLocaleString('en-US')+(y?'.'+y.slice(0,2):'');};
f.addEventListener('input',()=>{const p=f.selectionStart,l=f.value.length;f.value=fmt(f.value);f.setSelectionRange(p+(f.value.length-l),p+(f.value.length-l));});
$('#salesForm').on('submit',()=>f.value=f.value.replace(/,/g,''));})();
</script>
<script>
function toggleDate(field) {
    const checkbox = document.getElementById(field + '_cb');
    const dateInput = document.getElementById(field + '_date');

    if (!dateInput) return; // บางสถานะไม่มี date

    if (checkbox.checked) {
        dateInput.removeAttribute('disabled');
    } else {
        dateInput.setAttribute('disabled', 'disabled');
        dateInput.value = ''; // ล้างค่าถ้ายกเลิก
    }
}
</script>
</body></html>
