<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
    <title>Prime Forecast 25 • User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===== CSS Dependencies ===== -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    
    <!-- ===== ไม่ต้องเรียก JavaScript ใน Head อีกต่อไป ===== -->

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
        <a href="home_user.php" class="logo"><b>Prime</b>Forecast</a>

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
                        <a href="User/edit_profile.php" style="display: inline-block;">
                            <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" style="width: 45px; height: 45px;">
                        </a>
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
                        <div class="box-header"><h3 class="box-title">กราฟเปรียบเทียบTarget/Forecast/Win</h3></div>
                        <div class="box-body"><canvas id="winForecastChart"></canvas></div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Sum Value Percent Chart ------------------------------------>
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title">กราฟเปรียบเทียบสัดส่วนของกลุ่มสินค้า</h3></div>
                        <div class="box-body"><canvas id="sumValuePercentChart" height="180"></canvas></div>
                    </div>
                </div>
            </div>
        </section>
    </div><!-- /.content-wrapper -->

    <!-- หมายเหตุ: ผมรวม .content-wrapper ที่ซ้ำกันให้เป็นอันเดียว -->
    
</div><!-- /.wrapper -->

<!-- =====================================================
     JS Dependencies (จัดระเบียบใหม่ทั้งหมด)
====================================================== -->

<!-- 1. jQuery (ต้องมาก่อนเสมอ) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- 2. jQuery UI (สำหรับ AdminLTE tooltip/button) -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>

<!-- 3. Bootstrap (ต้องใช้ bundle เพื่อให้ dropdown ทำงาน) -->
<!-- ใช้เวอร์ชั่น 3.3.7 เพื่อให้เข้ากับ AdminLTE Template รุ่นเก่าได้ดีขึ้น -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<!-- 4. สคริปต์หลักของ AdminLTE (ควบคุมเมนู Treeview) -->
<script src="dist/js/app.min.js"></script>

<!-- 5. Chart.js และ Plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>

<!-- แก้ปัญหา conflict ระหว่าง jQuery UI และ Bootstrap -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<!-- =====================================================
     Page‑Specific JS (โค้ดวาดกราฟของคุณ)
====================================================== -->
<script>
(async () => {
    const userId = <?= $userId ?>;

    try {
        const res  = await fetch(`user_data.php?user_id=${userId}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        drawStepChart(data.salestep);
        drawWinForecastChart(data.winforecast);
        drawSumValuePercentChart(data.sumvaluepercent);
    } catch (err) {
        console.error(err);
        // ใช้ console.error แทน alert เพื่อไม่ให้รบกวนผู้ใช้
        const errorContainer = document.querySelector('.content'); 
        if(errorContainer) {
            errorContainer.innerHTML = '<div class="alert alert-danger"><strong>Error!</strong> ไม่สามารถโหลดข้อมูลกราฟได้ โปรดตรวจสอบ Console Log</div>';
        }
    }

    /* ------------------------- Step Chart ------------------------- */
    function drawStepChart(rows) {
        if (!Array.isArray(rows) || rows.length === 0) {
            document.getElementById('stepChart').parentNode.innerHTML = '<p class="text-center text-muted">ไม่มีข้อมูลแสดงกราฟขั้นตอน</p>';
            return;
        }

        const labels = rows.map(r => r.month);
        const datasets = [
            { label: 'Present',   data: rows.map(r => +r.present_value),  backgroundColor: 'rgba(128, 81, 255, 1)' },
            { label: 'Budget',    data: rows.map(r => +r.budgeted_value), backgroundColor: 'rgba(255, 0, 144, 1)' },
            { label: 'TOR',       data: rows.map(r => +r.tor_value),      backgroundColor: 'rgba(230, 180, 40, 1)' },
            { label: 'Bidding',   data: rows.map(r => +r.bidding_value),  backgroundColor: 'rgba(230, 120, 40, 1)' },
            { label: 'Win',       data: rows.map(r => +r.win_value),      backgroundColor: 'rgba(34, 139, 34, 1)' },
            { label: 'Lost',      data: rows.map(r => +r.lost_value),     backgroundColor: 'rgba(178, 34, 34, 1)' }
        ];

        new Chart(document.getElementById('stepChart').getContext('2d'), {
            type: 'bar',
            data: { labels, datasets },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'สถานะโครงการในแต่ละเดือน' },
                    legend: { position: 'top' }
                },
                scales: {
                    x: { stacked: true, title: { display: true, text: 'เดือน' } },
                    y: { stacked: true, beginAtZero: true, title: { display: true, text: 'มูลค่าโครงการ' } }
                }
            }
        });
    }

    /* -------------- Cumulative Win vs Forecast Chart -------------- */
    function drawWinForecastChart(rows) {
        if (!Array.isArray(rows) || rows.length === 0) {
            document.getElementById('winForecastChart').parentNode.innerHTML = '<p class="text-center text-muted">ไม่มีข้อมูลแสดงกราฟ Forecast</p>';
            return;
        }

        const { Target, Forecast, Win } = rows[0];
        const labels = ['Target', 'Forecast', 'Win'];
        const data = [+Target, +Forecast, +Win];
        const colors = ['rgba(153,102,255,0.7)', 'rgba(54,162,235,0.7)', 'rgba(34, 139, 34, 0.7)'];

        new Chart(document.getElementById('winForecastChart').getContext('2d'), {
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
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => `${ctx.label}: ${ctx.parsed.y.toLocaleString()}` }
                    }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'จำนวนเงิน' } }
                }
            }
        });
    }

    /* ------------------ Sum Value Percent Chart ------------------ */
    function drawSumValuePercentChart(rows) {
        if (!Array.isArray(rows) || rows.length === 0) {
            document.getElementById('sumValuePercentChart').parentNode.innerHTML = '<p class="text-center text-muted">ไม่มีข้อมูลแสดงกราฟสัดส่วน</p>';
            return;
        }

        const labels = rows.map(r => r.product);
        const values = rows.map(r => +r.sum_value);
        const total = values.reduce((acc, v) => acc + v, 0);
        const palette = ['rgba(255,99,132,0.7)', 'rgba(54,162,235,0.7)', 'rgba(255,206,86,0.7)', 'rgba(75,192,192,0.7)', 'rgba(153,102,255,0.7)', 'rgba(255,159,64,0.7)'];
        const backgroundColors = values.map((_, i) => palette[i % palette.length]);

        new Chart(document.getElementById('sumValuePercentChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{ data: values, backgroundColor: backgroundColors }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { display: true, text: 'สัดส่วนมูลค่าของกลุ่มสินค้า' },
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: context => {
                                const value = context.parsed;
                                const percent = total > 0 ? (value / total * 100).toFixed(2) : '0.00';
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