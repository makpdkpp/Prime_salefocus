<?php
require_once 'functions.php';
session_start();
$avatar = htmlspecialchars($_SESSION['avatar'] ?? 'dist/img/user2-160x160.jpg', ENT_QUOTES, 'UTF-8');

// ตรวจสอบว่าล็อกอินหรือไม่
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: index.php');
    exit;
}

$email = htmlspecialchars($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | PrimeForecast</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins_v3/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist_v3/css/adminlte.min.css">

  <style>
        /* ==== ปรับขนาดรูปใน sidebar ให้เท่ากันตอนยุบ/ขยาย ==== */
    body.sidebar-mini .main-sidebar .user-panel .image img,
    body:not(.sidebar-mini) .main-sidebar .user-panel .image img {
      width: 40px;
      height: 40px;
      object-fit: cover;
    }
    /* ✅ เพิ่มโค้ดสีพื้นหลังตรงนี้ */
    .content-wrapper { background-color: #b3d6e4; }

    /* สไตล์สำหรับ Summary Box ที่คุณกำหนดเอง */
    .summary-boxes {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 20px;
    }
    .summary-box {
      flex: 1;
      min-width: 220px;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      text-align: left;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      border-left: 5px solid #007bff;
    }
    .summary-box h4 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #555;
      font-weight: bold;
    }
    .summary-box p {
      font-size: 26px;
      font-weight: bold;
      margin: 0;
      color: #333;
    }
    .sidebar {padding-bottom: 30px; }
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
          <img src="<?= $avatar ?>" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?php echo $email; ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="<?= $avatar ?>" class="img-circle elevation-2" alt="User Image">
            <p><?php echo $email; ?> <small>Admin</small></p>
          </li>
          <li class="user-footer">
            <a href="logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="home_admin.php" class="brand-link" style="background-color: #0056b3; text-align: center;">
        <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
          <a href="pages/layout/adminedit_profile.php"> <img src="<?= $avatar ?>" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></a>
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
                <a href="home_admin.php" class="nav-link active">
                  <i class="far fa-chart-bar nav-icon"></i>
                  <p>Dashboard (กราฟ)</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/layout/super_admin_table.php" class="nav-link">
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
              <li class="nav-item"><a href="pages/layout/top-nav.php" class="nav-link"><i class="fas fa-building nav-icon"></i><p>เพิ่มข้อมูลบริษัท</p></a></li>
              <li class="nav-item"><a href="pages/layout/boxed.php" class="nav-link"><i class="fas fa-boxes nav-icon"></i><p>เพิ่มข้อมูลกลุ่มสินค้า</p></a></li>
              <li class="nav-item"><a href="pages/layout/fixed.php" class="nav-link"><i class="fas fa-industry nav-icon"></i><p>เพิ่มข้อมูลอุตสาหกรรม</p></a></li>
              <li class="nav-item"><a href="pages/layout/Source_of_the_budget.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>เพิ่มข้อมูลที่มาของงบประมาณ</p></a></li>
              <li class="nav-item"><a href="pages/layout/collapsed-sidebar.php" class="nav-link"><i class="fas fa-tasks nav-icon"></i><p>ขั้นตอนการขาย</p></a></li>
              <li class="nav-item"><a href="pages/layout/of_winning.php" class="nav-link"><i class="fas fa-trophy nav-icon"></i><p>โอกาสการชนะ</p></a></li>
              <li class="nav-item"><a href="pages/layout/Saleteam.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>ทีมขาย</p></a></li>
              <li class="nav-item"><a href="pages/layout/position_u.php" class="nav-link"><i class="fas fa-user-tag nav-icon"></i><p>ตำแหน่ง</p></a></li>
              <li class="nav-item"><a href="pages/layout/Profile_user.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>รายละเอียดผู้ใช้งาน</p></a></li>
              <li class="nav-item"><a href="pages/layout/newuser.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
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
          <div class="col-sm-6">
            <h1>Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="summary-boxes">
          <div class="summary-box">
            <h4>มูลค่า Forecast ทั้งหมด</h4>
            <p><span id="estimatevalue">Loading...</span> บาท</p>
          </div>
          <div class="summary-box">
            <h4>มูลค่าที่ WIN ทั้งหมด</h4>
            <p><span id="totalWinDisplay">Loading...</span> บาท</p>
          </div>
          <div class="summary-box">
            <h4>จำนวนโครงการที่ WIN</h4>
            <p><span id="wincount">Loading...</span> โครงการ</p>
          </div>
          <div class="summary-box">
            <h4>จำนวนโครงการที่ LOST</h4>
            <p><span id="lostcount">Loading...</span> โครงการ</p>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">ยอดขายรวม(มูลค่า)</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="winstatusValueChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">ยอดขายรายทีม(บาท)</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="teamSumChart" height="180"></canvas></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">ยอดขายรายคน(บาท)</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="personSumChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">สถานะการขายในแต่ละขั้นตอน</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="salestatusChart" height="180"></canvas></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">ประมาณการมูลค่าในแต่ละขั้นตอนการขาย</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="statusValueChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">กราฟเปรียบเทียบ Target/Forecast/Win</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="saleForecastChart" height="180"></canvas></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">TOP 10 ประเภทโซลูชั่น</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="topProductsChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header d-flex align-items-center">
                <h3 class="card-title">ยอดขาย Top 10 ของลูกค้า</h3>
                <button class="btn btn-tool btn-fullscreen ms-auto float-end" style="margin-left:auto;" title="ขยายเต็มจอ" type="button"><i class="fas fa-expand"></i></button>
              </div>
              <div class="card-body"><canvas id="topCustomerChart" height="180"></canvas></div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>
</div>

<script src="plugins_v3/jquery/jquery.min.js"></script>
<script src="plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist_v3/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // สคริปต์สำหรับดึงข้อมูลและวาดกราฟ (เหมือนเดิม)
    async function loadDashboardData() {
        try {
            const response = await fetch('admin_data.php');
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            const data = await response.json();

            // Populate Summary Boxes
            document.getElementById('estimatevalue').innerText = Number(data.estimatevalue).toLocaleString('th-TH');
            document.getElementById('totalWinDisplay').innerText = Number(data.winvalue).toLocaleString('th-TH');
            document.getElementById('wincount').innerText = Number(data.wincount).toLocaleString('th-TH');
            document.getElementById('lostcount').innerText = Number(data.lostcount).toLocaleString('th-TH');

            // Render all charts
            renderWinStatusValueChart(data.salestatusvalue || []);
            renderTeamSumChart(data.sumbyperteam || []);
            renderPersonSumChart(data.sumbyperson || []);
            renderSaleStatusChart(data.salestatus || []);
            renderStatusValueChart(data.salestatusvalue || []);
            renderSaleForecastChart(data.saleforecast || []);
            renderTopProductsChart(data.TopProductGroup || []);
            renderTopCustomerChart(data.TopCustopmer || []);

        } catch (error) {
            console.error('There has been a problem with your fetch operation:', error);
        }
    }

    // ฟังก์ชันสำหรับวาดกราฟแต่ละตัว
    function renderWinStatusValueChart(rawData) {
        if (!document.getElementById('winstatusValueChart')) return;
        const labels = rawData.map(r => r.month);
        const winData = rawData.map(r => Number(r.win_value));
        const ctx = document.getElementById('winstatusValueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'Win', data: winData, backgroundColor: 'rgba(34, 139, 34, 1)' }]
            },
            options: { scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    function renderTeamSumChart(rawData) {
        if (!document.getElementById('teamSumChart')) return;
        const labels = rawData.map(item => item.team);
        const values = rawData.map(item => Number(item.sumvalue));
        const colors = values.map(() => `rgba(${Math.floor(Math.random()*256)}, ${Math.floor(Math.random()*256)}, ${Math.floor(Math.random()*256)}, 0.7)`);
        const ctx = document.getElementById('teamSumChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'ยอดรวม (มูลค่า)', data: values, backgroundColor: colors }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    function renderPersonSumChart(rawData) {
        if (!document.getElementById('personSumChart')) return;
        const labels = rawData.map(item => item.nname);
        const values = rawData.map(item => Number(item.total_value));
        const ctx = document.getElementById('personSumChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{ label: 'ยอดรวม (มูลค่า)', data: values, backgroundColor: 'rgba(54, 162, 235, 0.7)' }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    function renderSaleStatusChart(rawData) {
        if (!document.getElementById('salestatusChart')) return;
         const labels   = rawData.map(r => r.month);
        const datasets = [
            { label: 'Present', data: rawData.map(r => +r.present_count), backgroundColor: 'rgba(128, 81, 255, 1)'},
            { label: 'Budget', data: rawData.map(r => +r.budgeted_count), backgroundColor: 'rgba(255, 0, 144, 1)'},
            { label: 'TOR', data: rawData.map(r => +r.tor_count), backgroundColor: 'rgba(230, 180, 40, 1)'},
            { label: 'Bidding', data: rawData.map(r => +r.bidding_count), backgroundColor: 'rgba(230, 120, 40, 1)'},
            { label: 'Win', data: rawData.map(r => +r.win_count), backgroundColor: 'rgba(34, 139, 34, 1)'},
            { label: 'Lost', data: rawData.map(r => +r.lost_count), backgroundColor: 'rgba(178, 34, 34, 1)'}
        ];
        const ctx = document.getElementById('salestatusChart').getContext('2d');
        new Chart(ctx, { type: 'bar', data: { labels, datasets }, options: { scales: { y: { ticks: { precision: 0 } } } } });
    }

    function renderStatusValueChart(rawData) {
        if (!document.getElementById('statusValueChart')) return;
        const labels = rawData.map(r => r.month);
        const datasets = [
            { label: 'Present', data: rawData.map(r => +r.present_value), backgroundColor: 'rgba(128, 81, 255, 1)' },
            { label: 'Budget', data: rawData.map(r => +r.budgeted_value), backgroundColor: 'rgba(255, 0, 144, 1)'},
            { label: 'TOR', data: rawData.map(r => +r.tor_value), backgroundColor: 'rgba(230, 180, 40, 1)' },
            { label: 'Bidding', data: rawData.map(r => +r.bidding_value), backgroundColor: 'rgba(230, 120, 40, 1)' },
            { label: 'Win', data: rawData.map(r => +r.win_value), backgroundColor: 'rgba(34, 139, 34, 1)' },
            { label: 'Lost', data: rawData.map(r => +r.lost_value), backgroundColor: 'rgba(178, 34, 34, 1)' }
        ];
        const ctx = document.getElementById('statusValueChart').getContext('2d');
        new Chart(ctx, { type: 'bar', data: { labels, datasets }, options: { scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } } });
    }

    function renderSaleForecastChart(rawData) {
        if (!document.getElementById('saleForecastChart')) return;
        const labels = rawData.map(item => item.nname);
        const datasets = [
            { label: 'Target', data: rawData.map(item => +item.Target), backgroundColor: 'rgba(153,102,255,0.7)' },
            { label: 'Forecast', data: rawData.map(item => +item.Forecast), backgroundColor: 'rgba(54,162,235,0.7)' },
            { label: 'Win', data: rawData.map(item => +item.Win), backgroundColor: 'rgba(34, 139, 34, 1)' }
        ];
        const ctx = document.getElementById('saleForecastChart').getContext('2d');
        new Chart(ctx, { type: 'bar', data: { labels, datasets }, options: { scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } } });
    }

    function renderTopProductsChart(rawData) {
        if (!document.getElementById('topProductsChart')) return;
        const labels = rawData.map(r => r.product);
        const data = rawData.map(r => Number(r.sum_value));
        const ctx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ label: 'ยอดรวม', data, backgroundColor: 'rgba(255, 99, 132, 0.7)' }] },
            options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    function renderTopCustomerChart(rawData) {
        if (!document.getElementById('topCustomerChart')) return;
        const labels = rawData.map(r => r.company);
        const data = rawData.map(r => Number(r.sum_value));
        const ctx = document.getElementById('topCustomerChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ label: 'ยอดรวม', data, backgroundColor: 'rgba(54, 162, 235, 0.7)' }] },
            options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    document.addEventListener('DOMContentLoaded', loadDashboardData);

    // Fullscreen button logic for all chart cards
    $(document).on('click', '.btn-fullscreen', function() {
      var card = $(this).closest('.card')[0];
      if (card.requestFullscreen) {
        card.requestFullscreen();
      } else if (card.webkitRequestFullscreen) {
        card.webkitRequestFullscreen();
      } else if (card.msRequestFullscreen) {
        card.msRequestFullscreen();
      }
    });
</script>

</body>
</html>