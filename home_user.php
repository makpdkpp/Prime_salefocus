<?php
require_once 'functions.php';
session_start();

// 1) Auth check
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>Prime Focus 25 V1 (User Dashboard)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- jQuery -->
<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<!-- Bootstrap -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/app.min.js"></script>


  <!-- CSS -->
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="dist/css/AdminLTE.min.css" rel="stylesheet">
  <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet">
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
  <style>
    .box { margin-bottom: 1.5rem; }
    canvas { width: 100% !important; height: auto !important; }
  </style>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">



  <!-- Main Header -->
  <header class="main-header">
    <a href="home_user.php" class="logo"><b>Prime</b>Focus</a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas"><span class="sr-only">Toggle navigation</span></a>
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
                <p>
                  <?= $email ?> <small>User</small>
                </p>
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

  <!-- Sidebar -->
  <aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      <div class="pull-left image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
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
              <a href="home_user.php">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="active"><a href="home_user.php"><i class="fa fa-circle-o"></i>Dashboard (กราฟ)</a></li>
                <li class="active"><a href="home_user_01.php"><i class="fa fa-circle-o"></i>Dashboard (ตาราง)</a></li>
              </ul>
            </li>
            <li class="treeview">
              <a href="#">
                <i class="fa fa-files-o"></i>
                <span>เพิ่มข้อมูล</span>
                <!--<span class="label label-primary pull-right">4</span> -->
              </a>
              <ul class="treeview-menu">
                <li><a href="User/adduser01.php"><i class="fa fa-circle-o"></i>เพิ่มรายละเอียดการขาย</a></li>
              </ul>
            </li>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>


  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content">
      <div class="row">
        <!-- Step Chart -->
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header"><h3 class="box-title">สถานะการขายในแต่ละขั้นตอน</h3></div>
            <div class="box-body">
              <canvas id="stepChart"></canvas>
            </div>
          </div>
        </div>
        <!-- Forecast Chart -->
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header"><h3 class="box-title">Cumulative Win vs Forecast</h3></div>
            <div class="box-body">
              <canvas id="winForecastChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<!-- JS Libraries -->
<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>

<!-- Page Script -->
<script>
(async () => {
  const userId = <?= $userId ?>;
  try {
    const res  = await fetch('user_data.php?user_id=' + userId);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const json = await res.json();

    drawStepChart(json.salestep);
    drawWinForecastChart(json.winforecast);
  } catch (err) {
    console.error(err);
    alert('ไม่สามารถโหลดข้อมูลกราฟได้');
  }

  function drawStepChart(raw) {
    console.log('salestep raw =', raw);
    if (!Array.isArray(raw) || raw.length === 0) {
      // ถ้าไม่มีข้อมูล ให้แสดงข้อความแทน
      const canvas = document.getElementById('stepChart');
      canvas.parentNode.replaceChild(
        document.createTextNode('ไม่มีข้อมูลแสดงกราฟขั้นตอน'),
        canvas
      );
      return;
    }

    // สร้าง labels เป็นเดือน
    const labels = raw.map(r => r.month);

    // แปลงค่าสถานะแต่ละอันเป็น array ตัวเลข
    const present  = raw.map(r => Number(r.present_value));
    const budgeted = raw.map(r => Number(r.budgeted_value));
    const tor      = raw.map(r => Number(r.tor_value));
    const bidding  = raw.map(r => Number(r.bidding_value));
    const win      = raw.map(r => Number(r.win_value));
    const lost     = raw.map(r => Number(r.lost_value));

    // เตรียม datasets แบบ stacked bar
    const datasets = [
      { label: 'Present',   data: present,  backgroundColor: 'rgba(75,192,192,0.7)', stack: 'stack1' },
      { label: 'Budgeted',  data: budgeted, backgroundColor: 'rgba(54,162,235,0.7)', stack: 'stack1' },
      { label: 'TOR',       data: tor,      backgroundColor: 'rgba(255,206,86,0.7)', stack: 'stack1' },
      { label: 'Bidding',   data: bidding,  backgroundColor: 'rgba(255,99,132,0.7)', stack: 'stack1' },
      { label: 'Win',       data: win,      backgroundColor: 'rgba(153,102,255,0.7)', stack: 'stack1' },
      { label: 'Lost',      data: lost,     backgroundColor: 'rgba(255,159,64,0.7)', stack: 'stack1' }
    ];

    // ดึง context ของ canvas จริงๆ
    const ctx = document.getElementById('stepChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Salestatus per Month (Stacked)'
          },
          legend: {
            position: 'top'
          }
        },
        scales: {
          x: {
            stacked: true,
            title: { display: true, text: 'เดือน (YYYY-MM)' }
          },
          y: {
            stacked: true,
            beginAtZero: true,
            title: { display: true, text: 'จำนวนครั้ง' }
          }
        }
      }
    });
  }
  function drawWinForecastChart(raw) {
    if (!raw || raw.length === 0) {
      $('#winForecastChart').replaceWith('<p class="text-center">ไม่มีข้อมูลแสดงกราฟ Forecast</p>');
      return;
    }
    const labels = raw.map(r => r.month);
    // cumulative win
    const cumWin = [];
    raw.reduce((sum, r, i) => (cumWin[i] = sum + Number(r.win_value), sum + Number(r.win_value)), 0);
    const forecast = raw.map(r => Number(r.forecast));

    new Chart($('#winForecastChart'), {
      data: {
        labels,
        datasets: [
          {
            type: 'bar',
            label: 'ยอด สะสม',
            data: cumWin,
            backgroundColor: 'rgba(153,102,255,0.7)',
            stack: 'stack0'
          },
          {
            type: 'bar',
            label: 'Forecast',
            data: forecast,
            borderColor: 'rgba(54,162,235,0.9)',
            backgroundColor: 'rgba(54,162,235,0.3)',
            fill: false,
            yAxisID: 'y'
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          title: { display: true, text: 'Cumulative Win vs Forecast per Month' },
          legend: { position: 'top' }
        },
        scales: {
          x: { stacked: true, title: { display: true, text: 'เดือน (YYYY-MM)' } },
          y: {
            stacked: true,
            beginAtZero: true,
            title: { display: true, text: 'มูลค่า (บาท)' },
            ticks: { callback: v => v.toLocaleString('th-TH') }
          }
        }
      }
    });

    // celebration
    if (cumWin[cumWin.length-1] >= forecast[forecast.length-1]) {
      confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
    }
  }
})();
</script>
<!-- jQuery 2.1.3 -->
<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
    <!-- jQuery UI 1.11.2 -->
    <script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>    
    <!-- Morris.js charts -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="plugins/morris/morris.min.js" type="text/javascript"></script>
    <!-- Sparkline -->
    <script src="plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
    <!-- jvectormap -->
    <script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
    <script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/knob/jquery.knob.js" type="text/javascript"></script>
    <!-- daterangepicker -->
    <script src="plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
    <!-- datepicker -->
    <script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <!-- Slimscroll -->
    <script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='plugins/fastclick/fastclick.min.js'></script>
    <!-- AdminLTE App -->
    <script src="dist/js/app.min.js" type="text/javascript"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js" type="text/javascript"></script>

    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js" type="text/javascript"></script>
