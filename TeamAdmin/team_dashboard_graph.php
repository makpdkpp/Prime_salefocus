<?php
// FILE: TeamAdmin/team_dashboard_graph.php

// แก้ไข Path: ถอย 1 ขั้นเพื่อหา functions.php
require_once '../functions.php';
session_start();

// แก้ไข Path: ถอย 1 ขั้นเพื่อไปหน้า index.php
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$email = htmlspecialchars($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Team Dashboard | PrimeForecast</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../plugins_v3/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../dist_v3/css/adminlte.min.css">

  <style>
    /* .content-wrapper { background-color: #f4f6f9; } */
    .summary-boxes { display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 20px; }
    .summary-box { flex: 1; min-width: 220px; background: #fff; padding: 20px; border-radius: 8px; text-align: left; box-shadow: 0 4px 8px rgba(0,0,0,0.05); border-left: 5px solid #28a745; }
    .summary-box h4 { font-size: 16px; margin-bottom: 10px; color: #555; font-weight: bold; }
    .summary-box p { font-size: 26px; font-weight: bold; margin: 0; color: #333; }
    .sidebar {padding-bottom: 30px; }
    #teamSumChart {
      /*min-width: 900px !important;*/
      width: 100% !important;
      max-width: 100vw;
      height: 350px !important;
      display: block;
    }
    .card-body {
      /*min-width: 900px !important;*/
      width: 100% !important;
      /*overflow-x: auto !important;*/
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-dark navbar-success">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
           <img src="<?= $avatar ?>" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline"><?= $email ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header bg-success">
            <img src="<?= $avatar ?>" class="img-circle elevation-2" alt="User Image">
            <p><?= $email ?> <small>Team Head</small></p>
          </li>
          <li class="user-footer">
             <a href="../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-success elevation-4">
    <a href="../home_admin_team.php" class="brand-link navbar-success" style="text-align: center;">
         <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>
    <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image"><a href="../User/edit_profile.php"><img src="<?= $avatar ?>" class="img-circle elevation-2" alt="User Image"></a></div>
                <div class="info"><a href="#" class="d-block"><?= $email ?></a></div>
            </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <li class="nav-header">MAIN NAVIGATION</li>
            <li class="nav-item">
                 <a href="../home_admin_team.php" class="nav-link"><i class="nav-icon fas fa-user-shield"></i><p>My Dashboard</p></a>
            </li>
            <li class="nav-item menu-open">
                <a href="#" class="nav-link active"><i class="nav-icon fas fa-users"></i><p>Team Dashboard<i class="right fas fa-angle-left"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"><a href="team_dashboard_graph.php" class="nav-link active"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (กราฟ)</p></a></li>
                    <li class="nav-item"><a href="team_dashboard_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (ตาราง)</p></a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>เพิ่มข้อมูล<i class="fas fa-angle-left right"></i></p></a>
                <ul class="nav nav-treeview">
                    <li class="nav-item"><a href="add_infoteamadmin.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>เพิ่มรายละเอียดการขาย</p></a></li>
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
            <h1>Team Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../home_admin_team.php">Home</a></li>
              <li class="breadcrumb-item active">Team Dashboard</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container-fluid">
        <div class="summary-boxes">
          <div class="summary-box">
            <h4>มูลค่า Forecast ทั้งหมด (ในทีม)</h4>
            <p><span id="estimatevalue">Loading...</span> บาท</p>
          </div>
          <div class="summary-box">
            <h4>มูลค่าที่ WIN ทั้งหมด (ในทีม)</h4>
            <p><span id="totalWinDisplay">Loading...</span> บาท</p>
          </div>
          <div class="summary-box">
            <h4>จำนวนโครงการที่ WIN (ในทีม)</h4>
            <p><span id="wincount">Loading...</span> โครงการ</p>
          </div>
          <div class="summary-box">
            <h4>จำนวนโครงการที่ LOST (ในทีม)</h4>
            <p><span id="lostcount">Loading...</span> โครงการ</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header"><h3 class="card-title">ยอดขายรวมของทีม(มูลค่า)</h3></div>
              <div class="card-body"><canvas id="teamSumChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header"><h3 class="card-title">ยอดขายรายคนในทีม(บาท)</h3></div>
              <div class="card-body"><canvas id="personSumChart" height="180"></canvas></div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header"><h3 class="card-title">สถานะการขายในแต่ละขั้นตอน (จำนวน)</h3></div>
              <div class="card-body"><canvas id="salestatusChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header"><h3 class="card-title">ประมาณการมูลค่าในแต่ละขั้นตอนการขาย</h3></div>
              <div class="card-body"><canvas id="statusValueChart" height="180"></canvas></div>
            </div>
          </div>
        </div>
        
<div class="row">
          <div class="col-md-6">
            <div class="card card-success">
              <div class="card-header"><h3 class="card-title">กราฟเปรียบเทียบ Target/Forecast/Win</h3></div>
              <div class="card-body"><canvas id="teamSaleforecastChart" height="180"></canvas></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header"><h3 class="card-title">สัดส่วนกลุ่มสินค้า</h3></div>
              <div class="card-body"><canvas id=" " height="180"></canvas></div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>
</div>

<script src="../plugins_v3/jquery/jquery.min.js"></script>
<script src="../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist_v3/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    async function loadDashboardData() {
        try {
            const response = await fetch('../team_data.php');
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            const data = await response.json();

            document.getElementById('estimatevalue').innerText =
                (data.teamEstimatevalue !== undefined && data.teamEstimatevalue !== null)
                ? Number(data.teamEstimatevalue).toLocaleString('th-TH')
                : "0";
            document.getElementById('totalWinDisplay').innerText = Number(data.teamWinvalue || 0).toLocaleString('th-TH');
            document.getElementById('wincount').innerText = Number(data.teamWinCount || 0).toLocaleString('th-TH');
            document.getElementById('lostcount').innerText = Number(data.teamCountLost || 0).toLocaleString('th-TH');

            renderTeamSumChart(data.teamSumMonth || []);
            renderPersonSumChart(data.teamSumByPerson || []);
            renderSaleStatusChart(data.teamSalestep || []);
            renderStatusValueChart(data.teamSalestepValue || []);
            renderTeamSaleforecastChart(data.teamSaleforecast || []);
        } catch (error) {
            console.error('Error loading dashboard data:', error);
            document.getElementById('estimatevalue').innerText = "Error";
        }
    }

    let teamSumChartInstance = null;
    let personSumChartInstance = null;
    let saleStatusChartInstance = null;
    let statusValueChartInstance = null;
    let teamSaleforecastChartInstance = null;

    function renderTeamSumChart(rawData) {
        if (!document.getElementById('teamSumChart') || !rawData.length) return;
        const labels = rawData.map(item => item.month);
        const values = rawData.map(item => Number(item.total));
        const ctx = document.getElementById('teamSumChart').getContext('2d');
        if (teamSumChartInstance) teamSumChartInstance.destroy();
        teamSumChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'ยอดรวม (มูลค่า)',
                    data: values,
                    backgroundColor: '#28a745',
                    maxBarThickness: 60,
                    barPercentage: 0.6,
                    categoryPercentage: 0.5
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString('th-TH')
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                animation: false
            }
        });
    }

    function renderPersonSumChart(rawData) {
        if (!document.getElementById('personSumChart') || !rawData.length) return;
        const labels = rawData.map(item => item.NAME);
        const values = rawData.map(item => Number(item.total));
        const ctx = document.getElementById('personSumChart').getContext('2d');
        if (personSumChartInstance) personSumChartInstance.destroy();
        personSumChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: 'ยอดรวม (มูลค่า)', data: values, backgroundColor: 'rgba(54, 162, 235, 0.7)' }] },
            options: { plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    function renderSaleStatusChart(rawData) {
        if (!document.getElementById('salestatusChart') || !rawData.length) return;
        const labels = rawData.map(r => r.month);
        const datasets = [
            { label: 'Present', data: rawData.map(r => +r.present_count), backgroundColor: 'rgba(128, 81, 255, 1)' },
            { label: 'Budget', data: rawData.map(r => +r.budgeted_count), backgroundColor: 'rgba(255, 0, 144, 1)' },
            { label: 'TOR', data: rawData.map(r => +r.tor_count), backgroundColor: 'rgba(230, 180, 40, 1)' },
            { label: 'Bidding', data: rawData.map(r => +r.bidding_count), backgroundColor: 'rgba(230, 120, 40, 1)' },
            { label: 'Win', data: rawData.map(r => +r.win_count), backgroundColor: 'rgba(34, 139, 34, 1)' },
            { label: 'Lost', data: rawData.map(r => +r.lost_count), backgroundColor: 'rgba(178, 34, 34, 1)' }
        ];
        const ctx = document.getElementById('salestatusChart').getContext('2d');
        if (saleStatusChartInstance) saleStatusChartInstance.destroy();
        saleStatusChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets },
            options: { scales: { y: { ticks: { precision: 0 } } } }
        });
    }

    function renderStatusValueChart(rawData) {
        if (!document.getElementById('statusValueChart') || !rawData.length) return;
        const labels = rawData.map(r => r.month);
        const datasets = [
            { label: 'Present', data: rawData.map(r => +r.present_value), backgroundColor: 'rgba(128, 81, 255, 1)' },
            { label: 'Budget', data: rawData.map(r => +r.budgeted_value), backgroundColor: 'rgba(255, 0, 144, 1)' },
            { label: 'TOR', data: rawData.map(r => +r.tor_value), backgroundColor: 'rgba(230, 180, 40, 1)' },
            { label: 'Bidding', data: rawData.map(r => +r.bidding_value), backgroundColor: 'rgba(230, 120, 40, 1)' },
            { label: 'Win', data: rawData.map(r => +r.win_value), backgroundColor: 'rgba(34, 139, 34, 1)' },
            { label: 'Lost', data: rawData.map(r => +r.lost_value), backgroundColor: 'rgba(178, 34, 34, 1)' }
        ];
        const ctx = document.getElementById('statusValueChart').getContext('2d');
        if (statusValueChartInstance) statusValueChartInstance.destroy();
        statusValueChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets },
            options: { scales: { y: { ticks: { callback: v => v.toLocaleString('th-TH') } } } }
        });
    }

    function renderTeamSaleforecastChart(rawData) {
        if (!document.getElementById('teamSaleforecastChart') || !rawData.length) return;
        const labels = rawData.map(item => item.nname);
        const target = rawData.map(item => Number(item.Target));
        const forecast = rawData.map(item => Number(item.Forecast));
        const win = rawData.map(item => Number(item.Win));
        const ctx = document.getElementById('teamSaleforecastChart').getContext('2d');
        if (teamSaleforecastChartInstance) teamSaleforecastChartInstance.destroy();
        teamSaleforecastChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Target',
                        data: target,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Forecast',
                        data: forecast,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Win',
                        data: win,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    }
                ]
            },
            options: {
                plugins: { legend: { display: true } },
                scales: {
                    x: {
                        grid: { display: false },
                        stacked: false,
                        ticks: { autoSkip: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString('th-TH')
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                animation: false
            }
        });
    }

    document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>


</body>
</html>