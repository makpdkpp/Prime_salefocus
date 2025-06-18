<?php
require_once 'functions.php';
session_start();

// ---------------------------------------------------
// 1) Authentication (user‑only dashboard)
// ---------------------------------------------------
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Prime Focus 25 • User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===== CSS Dependencies ===== -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ===== chartjs-plugin-datalabels ===== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>


    <style>
        .box   { margin-bottom: 1.5rem; }
        canvas { width: 100% !important; height: auto !important; }
    </style>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

    <!-- =====================================================
         Header
    ====================================================== -->
    <header class="main-header">
        <a href="home_user.php" class="logo"><b>Prime</b>Focus</a>

        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button -->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account -->
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
                    <!-- /.user-menu -->
                </ul>
            </div>
        </nav>
    </header>

    <!-- =====================================================
         Sidebar
    ====================================================== -->
    <aside class="main-sidebar">
        <section class="sidebar">
            <!-- User panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                </div>
                <div class="pull-left info">
                    <p><?= $email ?> (User)</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">MAIN NAVIGATION</li>

                <!-- Dashboard -->
                <li class="active treeview">
                    <a href="#">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li class="active"><a href="home_user.php"><i class="fa fa-circle-o"></i> Dashboard (กราฟ)</a></li>
                        <li><a href="home_user_01.php"><i class="fa fa-circle-o"></i> Dashboard (ตาราง)</a></li>
                    </ul>
                </li>

                <!-- Add data -->
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
            <!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- =====================================================
         Content Wrapper
    ====================================================== -->
    <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <!-- Step Chart -------------------------------------------------->
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title">สถานะโครงการในแต่ละขั้นตอน</h3></div>
                        <div class="box-body"><canvas id="stepChart"></canvas></div>
                    </div>
                </div>

                <!-- Win vs Forecast Chart -------------------------------------->
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title">กราฟเปรียบเทียบยอดขาย/เป้าหมาย/Forecast</h3></div>
                        <div class="box-body"><canvas id="winForecastChart"></canvas></div>
                      
                    </div>
                </div>
            </div>
        </section>
    </div><!-- /.content-wrapper -->


 <div class="content-wrapper">
        <section class="content">
            <div class="row">
                <!-- Step Chart -------------------------------------------------->
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title">sumValuePercentChart</h3></div>
                        <div class="box-body"><canvas id="sumValuePercentChart" height="180"></canvas></div>
                    </div>
                </div>

            
            </div>
        </section>
    </div><!-- /.content-wrapper -->
</div><!-- /.wrapper -->
<!-- =====================================================
     JS Dependencies (Load once at the bottom)
====================================================== -->
<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
<script src="dist/js/app.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
<!-- jQuery UI (tooltip conflict fix) -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<script>$.widget.bridge('uibutton', $.ui.button);</script>

<!-- =====================================================
     Page‑Specific JS (charts & data fetch)
====================================================== -->

<script>
 
(async () => {
    const userId = <?= $userId ?>;

    try {
        const res  = await fetch(`user_data.php?user_id=${userId}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        drawStepChart(data.salestep);
        drawWinForecastChart(data.winforecast);
        drawSumValuePercentChart(data.sumvaluepercent);
    } catch (err) {
        console.error(err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้');
    }

    /* ------------------------- Step Chart ------------------------- */
    function drawStepChart(rows) {
    if (!Array.isArray(rows) || rows.length === 0) {
        document.getElementById('stepChart').replaceWith(
            document.createTextNode('ไม่มีข้อมูลแสดงกราฟขั้นตอน')
        );
        return;
    }

    // แยกเอาแต่ละชุดข้อมูลออกมาเป็น array
    const labels    = rows.map(r => r.month);
    const present   = rows.map(r => +r.present_value);
    const budgeted  = rows.map(r => +r.budgeted_value);
    const tor       = rows.map(r => +r.tor_value);
    const bidding   = rows.map(r => +r.bidding_value);
    const win       = rows.map(r => +r.win_value);
    const lost      = rows.map(r => +r.lost_value);

    // สร้าง datasets โดยไม่กำหนด stack และกำหนดสีใหม่
    const datasets = [
        { label: 'Present',  data: present,   backgroundColor: 'rgba(153,102,255,0.7)' }, // ม่วง
        { label: 'Budgeted', data: budgeted,  backgroundColor: 'rgba(54,162,235,0.7)' },  // ฟ้า
        { label: 'TOR',      data: tor,       backgroundColor: 'rgba(255,206,86,0.7)' },  // เหลือง
        { label: 'Bidding',  data: bidding,   backgroundColor: 'rgba(255,159,64,0.7)' },  // ส้ม
        { label: 'Win',      data: win,       backgroundColor: 'rgba(75,192,192,0.7)' },  // เขียว
        { label: 'Lost',     data: lost,      backgroundColor: 'rgba(255,99,132,0.7)' }   // แดง
    ];

    new Chart(
        document.getElementById('stepChart').getContext('2d'),
        {
            type: 'bar',
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'Project Status per Month' },
                    legend: { position: 'top' }
                },
                scales: {
                    x: {
                        stacked: false,
                        title: { display: true, text: 'เดือน' }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        title: { display: true, text: 'มูลค่าโครงการ' }
                    }
                }
            }
        }
    );
}

    /* -------------- Cumulative Win vs Forecast Chart -------------- */
   function drawWinForecastChart(rows) {
  // 1) ตรวจสอบข้อมูลก่อน
  if (!Array.isArray(rows) || rows.length === 0) {
    const canvas = document.getElementById('winForecastChart');
    canvas.parentNode.replaceChild(
      document.createTextNode('ไม่มีข้อมูลแสดงกราฟ Forecast'),
      canvas
    );
    return;
  }

  // 2) ดึงค่าจาก row แรก (เพราะ user คนเดียว)
  const { Target, Forecast, Win } = rows[0];

  // 3) เตรียม labels, data และสี ตามลำดับ Win, Forecast, Target
  
  const labels = ['Forecast', 'Target', 'Win'];
  const data   = [ +Forecast, +Target, +Win ];
  const colors = [
    'rgba(75,192,192,0.7)',    // เขียว สำหรับ Win
    'rgba(54,162,235,0.7)',    // ฟ้า สำหรับ Forecast
    'rgba(153,102,255,0.7)'    // ม่วง สำหรับ Target
  ];

  // 4) หา context และสร้างกราฟ
  const ctx = document.getElementById('winForecastChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'หน่วย: จำนวนเงิน',
        data: data,
        backgroundColor: colors
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Actual vs Forecast'
        },
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: ctx => `${ctx.label}: ${ctx.parsed.y.toLocaleString()}`
          }
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'ประเภท'
          }
        },
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'จำนวนเงิน'
          }
        }
      }
    }
  });
}

    /* ------------------ Sum Value Percent Chart ------------------ */
function drawSumValuePercentChart(rows) {
  // ถ้าไม่มีข้อมูล ให้แสดงข้อความแทนกราฟ
  if (!Array.isArray(rows) || rows.length === 0) {
    const canvas = document.getElementById('sumValuePercentChart');
    canvas.parentNode.replaceChild(
      document.createTextNode('ไม่มีข้อมูลสำหรับแสดงกราฟวงกลม'),
      canvas
    );
    return;
  }

  // เตรียม labels และ values
  const labels = rows.map(r => r.product);
  const values = rows.map(r => +r.sum_value);

  // คำนวณผลรวมทั้งหมด เพื่อใช้คำนวณเปอร์เซ็นต์ใน tooltip
  const total = values.reduce((acc, v) => acc + v, 0);

  // สร้าง backgroundColor แบบวนลูปจาก palette ที่กำหนดล่วงหน้า
  const palette = [
    'rgba(255,99,132,0.7)',   // แดง
    'rgba(54,162,235,0.7)',   // ฟ้า
    'rgba(255,206,86,0.7)',   // เหลือง
    'rgba(75,192,192,0.7)',   // เขียว
    'rgba(153,102,255,0.7)',  // ม่วง
    'rgba(255,159,64,0.7)'    // ส้ม
  ];
  const backgroundColors = values.map((_, i) => palette[i % palette.length]);

  // วาดกราฟวงกลม (pie chart)
  const ctx = document.getElementById('sumValuePercentChart').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        data: values,
        backgroundColor: backgroundColors
      }]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'สัดส่วนมูลค่าตามสินค้า (เปอร์เซ็นต์)'
        },
        legend: {
          position: 'right'
        },
        tooltip: {
          callbacks: {
            label: context => {
              const value = context.parsed;
              const percent = total > 0
                ? (value / total * 100).toFixed(2)
                : '0.00';
              return `${context.label}: ${value.toLocaleString()} (${percent}%)`;
            }
          }
        }
      }
    }
    
  });
}
})();

 
</script>

</body>
</html>
