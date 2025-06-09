<?php
require_once '../functions.php';
session_start();

// Auth
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$mysqli = connectDb();
$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email'], ENT_QUOTES);

// Helper to load options from a table
function loadOptions($mysqli, $table, $valueCol, $labelCol) {
    $opts = [];
    $sql = "SELECT `$valueCol`, `$labelCol` FROM `$table`";
    $res = $mysqli->query($sql);
    while ($row = $res->fetch_assoc()) {
        $opts[] = $row;
    }
    return $opts;
}

$productOptions  = loadOptions($mysqli, 'product_group',   'product_id',  'product');
$teamOptions     = loadOptions($mysqli, 'team_catalog',    'team_id',     'team');
$companyOptions  = loadOptions($mysqli, 'company_catalog', 'company_id',  'company');
$priorityOptions = loadOptions($mysqli, 'priority_level',  'priority_id', 'priority');
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>เพิ่มรายละเอียดการขาย</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Bootstrap + AdminLTE CSS -->
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../dist/css/AdminLTE.min.css" rel="stylesheet">
  <link href="../dist/css/skins/_all-skins.min.css" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

  <style>
    .form-container { max-width: 900px; margin: 50px auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
    .form-row { display:flex; gap:30px; flex-wrap:wrap; }
    .form-col { flex:1; min-width:200px; }
    .form-group { margin-bottom:25px; }
    label { display:block; margin-bottom:6px; font-weight:500; }
    input, select, textarea { width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-size:16px; }
    button[type="submit"] { width:100%; padding:12px; background:#640000; color:#fff; font-size:16px; border:none; border-radius:4px; cursor:pointer; }
    button[type="submit"]:hover { background:#500000; }
    .step-row { display:flex; gap:1rem; align-items:center; margin-bottom:15px; }
    .step-row input[type="date"] { width:auto; flex-shrink:0; }
    .step-row label { display:flex; align-items:center; gap:.25rem; cursor:pointer; }
    .step-row input[type="date"][readonly] { background:#f0f0f0; cursor:not-allowed; }
  </style>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

  <!-- HEADER -->
  <header class="main-header">
    <a href="../home_user.php" class="logo"><b>Prime</b> Focus</a>
    <nav class="navbar navbar-static-top">
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="">
            <span class="hidden-xs"><?= $email ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="">
              <p><?= $email ?><small>Sales</small></p>
            </li>
            <li class="user-footer">
              <div class="pull-right">
                <a href="../logout.php" class="btn btn-default btn-flat">Sign out</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <!-- SIDEBAR -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li class="treeview">
          <a href="#"><i class="fa fa-dashboard"></i><span>Dashboard</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="../home_user.php"><i class="fa fa-circle-o"></i> Dashboard</a></li>
          </ul>
        </li>
        <li class="active treeview">
          <a href="#"><i class="fa fa-files-o"></i><span>เพิ่มข้อมูล....</span>
            <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
          </a>
          <ul class="treeview-menu">
            <li><a href="adduser01.php"><i class="fa fa-circle-o"></i> เพิ่มรายละเอียดการขาย</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>

  <!-- CONTENT -->
  <div class="content-wrapper">
    <section class="content">
      <div class="form-container">
        <form action="add_user.php" method="POST" class="form-horizontal">
          <div class="form-row">
            <div class="form-col form-group">
              <label for="Product">กลุ่มสินค้า</label>
              <select id="Product" name="Product" required>
                <option value="">-- เลือกลุ่มสินค้า --</option>
                <?php foreach ($productOptions as $o): ?>
                  <option value="<?= htmlspecialchars($o['product'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($o['product'], ENT_QUOTES) ?>
                  </option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-col form-group">
              <label for="Product_detail">รายการสินค้า</label>
              <input id="Product_detail" name="Product_detail" type="text" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-col form-group">
              <label for="Contact_start_date">วันที่ติดต่อ</label>
              <input id="Contact_start_date" name="Contact_start_date" type="date" required>
            </div>
            <div class="form-col form-group">
              <label>ขั้นตอน</label>
              <?php
                $steps = ['present','budgeted','tor','bidding','win','lost'];
                foreach ($steps as $i => $key):
              ?>
                <div class="step-row">
                  <input type="hidden" name="<?= $key ?>" value="0">
                  <label>
                    <input
                      id="<?= $key ?>" name="<?= $key ?>" type="checkbox"
                      value="1" <?= $i>0?'disabled':'' ?>
                    >
                    <span><?= ucfirst($key) ?></span>
                  </label>
                  <input
                    id="<?= $key ?>_date" name="<?= $key ?>_date" type="date" readonly
                  >
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-row">
            <div class="form-col form-group">
              <label>พนักงานขาย</label>
              <p class="form-control-static"><?= htmlspecialchars($_SESSION['nname'] ?? '') ?></p>
              <input type="hidden" name="userId" value="<?= $userId ?>">
            </div>
            <div class="form-col form-group">
              <label for="Team">ทีมขาย</label>
              <select id="Team" name="Team" required>
                <option value="">-- เลือกทีม --</option>
                <?php foreach ($teamOptions as $o): ?>
                  <option value="<?= htmlspecialchars($o['team'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($o['team'], ENT_QUOTES) ?>
                  </option>
                <?php endforeach ?>
              </select>
            </div>
          </div>

          <!-- next rows: Product_value, Company, dates, priority, remark -->
          <div class="form-row">
            <div class="form-col form-group">
              <label for="Product_value">มูลค่า</label>
              <input id="Product_value" name="Product_value" type="text" required>
            </div>
            <div class="form-col form-group">
              <label for="company">หน่วยงาน/บริษัท</label>
              <select id="company" name="company" required>
                <option value="">-- เลือกบริษัท/หน่วยงาน --</option>
                <?php foreach ($companyOptions as $o): ?>
                  <option value="<?= htmlspecialchars($o['company'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($o['company'], ENT_QUOTES) ?>
                  </option>
                <?php endforeach ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-col form-group">
              <label for="date_of_closing_of_sale">คาดว่าจะปิดการขาย</label>
              <input id="date_of_closing_of_sale" name="date_of_closing_of_sale" type="date">
            </div>
            <div class="form-col form-group">
              <label for="sales_can_be_closed">วันที่ปิดการขายได้</label>
              <input id="sales_can_be_closed" name="sales_can_be_closed" type="date">
            </div>
          </div>

          <div class="form-row">
            <div class="form-col form-group">
              <label for="priority">ระดับความสำคัญ</label>
              <select id="priority" name="priority">
                <option value="">-- เลือกระดับความสำคัญ --</option>
                <?php foreach ($priorityOptions as $o): ?>
                  <option value="<?= htmlspecialchars($o['priority'], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($o['priority'], ENT_QUOTES) ?>
                  </option>
                <?php endforeach ?>
              </select>
            </div>
            <div class="form-col form-group">
              <label for="remark">หมายเหตุ</label>
              <textarea id="remark" name="remark" rows="1"></textarea>
            </div>
          </div>

          <button type="submit" name="submit">บันทึก</button>
        </form>
      </div>
    </section>
  </div>
</div>

<!-- Scripts -->
<script src="../plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../dist/js/app.min.js"></script>
<script>
(() => {
  const steps = ['present','budgeted','tor','bidding','win','lost'];
  steps.forEach((key, i) => {
    const cb = document.getElementById(key);
    const dt = document.getElementById(key + '_date');
    const prevKey = steps[i-1];
    cb.addEventListener('change', () => {
      // unlock next
      if (i>0) {
        document.getElementById(prevKey).checked
          ? cb.removeAttribute('disabled')
          : (cb.checked = false);
      }
      dt.readOnly = !cb.checked;
      if (dt.readOnly) dt.value = '';
    });
    // init disable logic
    if (i>0) cb.disabled = !document.getElementById(prevKey).checked;
    dt.readOnly = !cb.checked;
  });
})();
</script>
</body>
</html>
