<?php
// home_admin_team.php (ไฟล์ข้างนอก)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: index.php'); exit;
}
$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <title>Prime Focus 25 • My Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="dist_v3/css/adminlte.min.css">
    <style>
        .card { margin-bottom: 1.5rem; }
        .card-body { position: relative; min-height: 400px; }
        canvas { position: absolute; top: 0; left: 0; width: 100% !important; height: 100% !important; }
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
                    <img src="dist_v3/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
                    <span class="d-none d-md-inline"><?= $email ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-success">
                        <img src="dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                        <p><?= $email ?><small>Team Head</small></p>
                    </li>
                    <li class="user-footer">
                        <a href="logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-success elevation-4">
        <a href="home_admin_team.php" class="brand-link">
             <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image"><a href="User/edit_profile.php"><img src="dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image"></a></div>
                <div class="info"><a href="#" class="d-block"><?= $email ?></a></div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">MAIN NAVIGATION</li>
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active"><i class="nav-icon fas fa-user-shield"></i><p>My Dashboard<i class="right fas fa-angle-left"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="home_admin_team.php" class="nav-link active"><i class="far fa-circle nav-icon"></i><p>Dashboard (กราฟ)</p></a></li>
                            <li class="nav-item"><a href="TeamAdmin/home_admin_team_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (ตาราง)</p></a></li>
                            </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Team Dashboard<i class="right fas fa-angle-left"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="TeamAdmin/team_dashboard_graph.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (กราฟ)</p></a></li>
                            <li class="nav-item"><a href="TeamAdmin/team_dashboard_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (ตาราง)</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>เพิ่มข้อมูล<i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="TeamAdmin/add_infoteamadmin.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>เพิ่มรายละเอียดการขาย</p></a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                 <div class="row">
                    <div class="col-lg-8">
                        <div class="card card-success">
                            <div class="card-header"><h3 class="card-title">สถานะโครงการในแต่ละขั้นตอน</h3></div>
                            <div class="card-body"><canvas id="stepChart"></canvas></div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-success">
                             <div class="card-header"><h3 class="card-title">สัดส่วนกลุ่มสินค้า</h3></div>
                            <div class="card-body"><canvas id="sumValuePercentChart"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card card-success">
                            <div class="card-header"><h3 class="card-title">ยอดขาย vs เป้าหมาย vs Forecast</h3></div>
                            <div class="card-body" style="min-height: 250px;"><canvas id="winForecastChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.1.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
(async () => {
    const userId = <?= $userId ?>;

    try {
        const res  = await fetch(`team_data.php?user_id=${userId}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();

        drawStepChart(data.salestep);
        drawWinForecastChart(data.winforecast);
        drawSumValuePercentChart(data.sumvaluepercent);
    } catch (err) {
        //  V V V V ส่วนที่แก้ไข V V V V
        console.error(err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้: ' + err.message);
        // ^ ^ ^ ^ จบส่วนที่แก้ไข ^ ^ ^ ^
    }

    /* ------------------------- Step Chart ------------------------- */
    function drawStepChart(rows) {
    const chartNode = document.getElementById('stepChart');
    if (!chartNode || !Array.isArray(rows) || rows.length === 0) {
        if (chartNode) chartNode.parentNode.innerHTML = '<p class="text-center text-muted pt-5">ไม่มีข้อมูลแสดงกราฟขั้นตอน</p>';
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
    new Chart(chartNode.getContext('2d'), {
        type: 'bar', data: { labels, datasets },
        options: { responsive: true, maintainAspectRatio: false, plugins: { title: { display: false }, legend: { position: 'top' } },
            scales: { x: { stacked: false, title: { display: true, text: 'เดือน' } }, y: { stacked: false, beginAtZero: true, title: { display: true, text: 'มูลค่าโครงการ' } } }
        }
    });
}
    /* -------------- Cumulative Win vs Forecast Chart -------------- */
    function drawWinForecastChart(rows) {
        const chartNode = document.getElementById('winForecastChart');
        if (!chartNode || !Array.isArray(rows) || rows.length === 0) {
             if(chartNode) chartNode.parentNode.innerHTML = '<p class="text-center text-muted pt-5">ไม่มีข้อมูลแสดงกราฟ Forecast</p>';
            return;
        }
        const { Target, Forecast, Win } = rows[0];
        const labels = ['Target', 'Forecast', 'Win'];
        const data = [+Target, +Forecast, +Win];
        const colors = ['rgba(153,102,255,0.7)', 'rgba(54,162,235,0.7)', 'rgba(34, 139, 34, 0.7)'];
        new Chart(chartNode.getContext('2d'), {
            type: 'bar', data: { labels: labels, datasets: [{ label: 'หน่วย: จำนวนเงิน', data: data, backgroundColor: colors }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, title: { display: true, text: 'จำนวนเงิน' } } }
            }
        });
    }
    /* ------------------ Sum Value Percent Chart (แก้ไขแล้ว) ------------------ */
    function drawSumValuePercentChart(rows) {
        const chartNode = document.getElementById('sumValuePercentChart');
        if (!chartNode || !Array.isArray(rows) || rows.length === 0) {
            if(chartNode) chartNode.parentNode.innerHTML = '<p class="text-center text-muted pt-5">ไม่มีข้อมูลแสดงกราฟสัดส่วน</p>';
            return;
        }
        const labels = rows.map(r => r.product);
        const values = rows.map(r => +r.sum_value);
        const total = values.reduce((acc, v) => acc + v, 0);
        const palette = ['rgba(54,162,235,0.8)', 'rgba(255,99,132,0.8)', 'rgba(255,206,86,0.8)', 'rgba(75,192,192,0.8)', 'rgba(153,102,255,0.8)', 'rgba(255,159,64,0.8)','rgba(0,128,128,0.8)' ];
        const backgroundColors = values.map((_, i) => palette[i % palette.length]);
        const borderColors = backgroundColors.map(color => color.replace('0.8', '1'));
        new Chart(chartNode.getContext('2d'), {
            type: 'pie', data: { labels: labels, datasets: [{ data: values, backgroundColor: backgroundColors, borderColor: borderColors, borderWidth: 1 }] },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    title: { display: false }, legend: { position: 'right', labels: { boxWidth: 20, font: { size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percent = total > 0 ? (value / total * 100).toFixed(2) : 0;
                                return `${label}: ${value.toLocaleString('th-TH')} (${percent}%)`;
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