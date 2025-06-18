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
    <title>Prime Focus 25 • User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===== CSS Dependencies ===== -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

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
                        <div class="box-header"><h3 class="box-title">สถานะการขายในแต่ละขั้นตอน</h3></div>
                        <div class="box-body"><canvas id="stepChart"></canvas></div>
                    </div>
                </div>

                <!-- Win vs Forecast Chart -------------------------------------->
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title">Cumulative Win vs Forecast</h3></div>
                        <div class="box-body"><canvas id="winForecastChart"></canvas></div>
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

        const labels = rows.map(r => r.month);

        const datasets = [
            { label: 'Present',   data: rows.map(r => +r.present_value),  backgroundColor: 'rgba(75,192,192,0.7)', stack: 'stack1' },
            { label: 'Budgeted',  data: rows.map(r => +r.budgeted_value), backgroundColor: 'rgba(54,162,235,0.7)', stack: 'stack1' },
            { label: 'TOR',       data: rows.map(r => +r.tor_value),      backgroundColor: 'rgba(255,206,86,0.7)', stack: 'stack1' },
            { label: 'Bidding',   data: rows.map(r => +r.bidding_value),  backgroundColor: 'rgba(255,99,132,0.7)', stack: 'stack1' },
            { label: 'Win',       data: rows.map(r => +r.win_value),      backgroundColor: 'rgba(153,102,255,0.7)', stack: 'stack1' },
            { label: 'Lost',      data: rows.map(r => +r.lost_value),     backgroundColor: 'rgba(255,159,64,0.7)', stack: 'stack1' }
        ];

        new Chart(
            document.getElementById('stepChart').getContext('2d'),
            {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    plugins: {
                        title: { display: true, text: 'Sales Status per Month (Stacked)' },
                        legend: { position: 'top' }
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
            }
        );
    }

    /* -------------- Cumulative Win vs Forecast Chart -------------- */
    function drawWinForecastChart(rows) {
        if (!Array.isArray(rows) || rows.length === 0) {
            document.getElementById('winForecastChart').replaceWith(
                document.createTextNode('ไม่มีข้อมูลแสดงกราฟ Forecast')
            );
            return;
        }

        const labels   = rows.map(r => r.month);
        const forecast = rows.map(r => +r.forecast);
        const cumWin   = rows.reduce((arr, r, i) => {
            arr[i] = (arr[i - 1] || 0) + +r.win_value;
            return arr;
        }, []);

        new Chart(
            document.getElementById('winForecastChart').getContext('2d'),
            {
                data: {
                    labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'ยอดสะสม',
                            data: cumWin,
                            backgroundColor: 'rgba(153,102,255,0.7)',
                            stack: 'stack0'
                        },
                        {
                            type: 'bar',
                            label: 'Forecast',
                            data: forecast,
                            backgroundColor: 'rgba(54,162,235,0.3)',
                            borderColor: 'rgba(54,162,235,0.9)',
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
                        x: {
                            stacked: true,
                            title: { display: true, text: 'เดือน (YYYY-MM)' }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            title: { display: true, text: 'มูลค่า (บาท)' },
                            ticks: {
                                callback: v => Number(v).toLocaleString('th-TH')
                            }
                        }
                    }
                }
            }
        );

        // Celebrate when reaching/exceeding forecast
        if (cumWin[cumWin.length - 1] >= forecast[forecast.length - 1]) {
            confetti({ particleCount: 120, spread: 90, origin: { y: 0.6 } });
        }
    }
})();
</script>
</body>
</html>
