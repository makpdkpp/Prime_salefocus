<?php

require_once 'functions.php';
session_start();
// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}
// ---------------------------------------------------
// 1) Authentication (user‑only dashboard)
// ---------------------------------------------------
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$email  = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
$avatar  = htmlspecialchars($_SESSION['avatar'] ?? '', ENT_QUOTES, 'UTF-8');
// ตรวจสอบ transactioonal_team        

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
          /* ==== ปรับขนาดรูปใน sidebar ให้เท่ากันตอนยุบ/ขยาย ==== */
    body.sidebar-mini .main-sidebar .user-panel .image img,
    body:not(.sidebar-mini) .main-sidebar .user-panel .image img {
      width: 40px;
      height: 40px;
      object-fit: cover;
    }
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
                            <img src="<?= $avatar ?>" class="user-image" alt="User Image">
                            <span class="hidden-xs"><?= $email ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="user-header">
                                <img src="<?= $avatar ?>" class="img-circle" alt="User Image">
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
        <a href="admin_team/edit_profile.php" style="display: inline-block;">
            <img src="<?= $avatar ?>" class="img-circle" alt="User Image" style="width: 45px; height: 45px;">
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
                        <li class="active"><a href="home_team.php"><i class="fa fa-circle-o"></i> Dashboard (กราฟ)</a></li>
                        <li><a href="home_team_table.php"><i class="fa fa-circle-o"></i> Dashboard (ตาราง)</a></li>
                    </ul>
                </li>

                <!-- Add data -->
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="admin_team/adduser01.php"><i class="fa fa-circle-o"></i> เพิ่มรายละเอียดการขาย</a></li>
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
      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border"><h3 class="box-title">ยอดขายรายคน(มูลค่า)</h3></div>
      <div class="box-body"><canvas id="winTeamChart"></canvas></div>
        </div>
      </div>
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
                        <div class="box-body"><select id="teamSelect"></select>
                          <canvas id="barChart"></canvas></div>
                      
                    </div>
                </div>
                <!-- Win vs Forecast Chart -------------------------------------->
                <div class="col-md-6">
                    <div class="box box-success">
                        <div class="box-header"><h3 class="box-title">สถานะโครงการในแต่ละขั้นตอนTeam</h3></div>
                        <div class="box-body">
                          <canvas id="stepChartteam"></canvas></div>
                      
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
                        <div class="box-body"><canvas id="pieChart" height="180"></canvas></div>
                    </div>
                </div>
            
            
            </div>
        </section>
    </div><!-- /.content-wrapper -->
</div><!-- /.wrapper -->
<!-- =====================================================
     JS Dependencies (Load once at the bottom)
====================================================== -->

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
    // ฟังก์ชันช่วยวาดกราฟ (reuse ได้หลายครั้ง)
    let barChart = null;
    function drawChart(data) {
      const labels     = data.map(i => i.nname);
      const targetData = data.map(i => +i.Target);
      const forecast   = data.map(i => +i.Forecast);
      const winData    = data.map(i => +i.Win);
      const ctx = document.getElementById('barChart').getContext('2d');
      if (barChart) barChart.destroy();
      barChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            { label: 'Target',   data: targetData },
            { label: 'Forecast', data: forecast   },
            { label: 'Win',      data: winData    }
          ]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true, title: { display: true, text: 'มูลค่า (บาท)' } }
          }
        }
      });
    }

    // IIFE: ดึง userId จาก PHP, fetch ข้อมูล, เติม select, แล้ววาดกราฟ
    (async () => {
      const userId = <?= json_encode($userId, JSON_HEX_TAG) ?>;
      try {
        const res  = await fetch(`team_data.php?user_id=${userId}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const { sumvaluepercent_team: teams } = await res.json();

        const select = document.getElementById('teamSelect');
        teams.forEach((teamObj, idx) => {
          const opt = document.createElement('option');
          opt.value = idx;
          opt.text  = teamObj.data[0]?.team || `Team ${teamObj.team_id}`;
          select.add(opt);
        });

        select.addEventListener('change', () => {
          drawChart( teams[ select.value ].data );
        });

        // วาดกราฟครั้งแรก
        if (teams.length > 0) drawChart(teams[0].data);

      } catch (err) {
        console.error('Error fetching data:', err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้: ' + err.message);
      }
    })();
  </script>
 <script>
    // ฟังก์ชันวาดกราฟ WinTeam
    let winChart = null;
    function drawWinChart(data) {
      const labels  = data.map(i => i.nname);
      const wins    = data.map(i => +i.Win);

      const ctx = document.getElementById('winTeamChart').getContext('2d');
      if (winChart) winChart.destroy();

      winChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Win Value (บาท)',
            data: wins
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: 'มูลค่า Win รวม' }
            }
          }
        }
      });
    }

    // IIFE: ดึง teamID จาก PHP, fetch ข้อมูล WinTeam, แล้ววาดกราฟ
    (async () => {
      const teamID = <?= json_encode($teamID, JSON_HEX_TAG) ?>;
      try {
        const res = await fetch(`team_data.php?team_id=${teamID}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();
        const winData = json.WinTeam || [];
        if (winData.length === 0) {
          alert('ไม่พบข้อมูล WinTeam สำหรับทีมนี้');
          return;
        }
        drawWinChart(winData);
      } catch (err) {
        console.error('Error fetching WinTeam data:', err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้: ' + err.message);
      }
    })();
  </script>
  <script>
    // ฟังก์ชันวาดกราฟ grouped bar สำหรับ User
    let userStepChart = null;
    function drawUserStepChart(data) {
      const labels    = data.map(r => r.month);
      const present   = data.map(r => +r.present_value);
      const budgeted  = data.map(r => +r.budgeted_value);
      const tor       = data.map(r => +r.tor_value);
      const bidding   = data.map(r => +r.bidding_value);
      const win       = data.map(r => +r.win_value);
      const lost      = data.map(r => +r.lost_value);
      const ctx = document.getElementById('stepChart').getContext('2d');
      if (userStepChart) userStepChart.destroy();
      userStepChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            { label: 'นำเสนอ Solution', data: present },
            { label: 'ตั้งงบประมาณ',     data: budgeted },
            { label: 'ร่าง TOR',         data: tor },
            { label: 'Bidding',          data: bidding },
            { label: 'WIN',              data: win },
            { label: 'LOST',             data: lost }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'top' },
            tooltip: { mode: 'index', intersect: false }
          },
          scales: {
            x: { title: { display: true, text: 'เดือน (YYYY-MM)' } },
            y: {
              beginAtZero: true,
              title: { display: true, text: 'มูลค่า (บาท)' }
            }
          }
        }
      });
    }
    // ฟังก์ชันวาดกราฟ grouped bar สำหรับ Team
    let teamStepChart = null;
    function drawTeamStepChart(data) {
      const labels            = data.map(r => r.month);
      const presentValues     = data.map(r => +r.present_value);
      const budgetedValues    = data.map(r => +r.budgeted_value);
      const torValues         = data.map(r => +r.tor_value);
      const biddingValues     = data.map(r => +r.bidding_value);
      const winValues         = data.map(r => +r.win_value);
      const lostValues        = data.map(r => +r.lost_value);
      const ctx = document.getElementById('stepChartteam').getContext('2d');
      if (teamStepChart) teamStepChart.destroy();
      teamStepChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels,
          datasets: [
            { label: 'นำเสนอ Solution', data: presentValues },
            { label: 'ตั้งงบประมาณ',     data: budgetedValues },
            { label: 'ร่าง TOR',         data: torValues },
            { label: 'Bidding',          data: biddingValues },
            { label: 'WIN',              data: winValues },
            { label: 'LOST',             data: lostValues }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            tooltip: { mode: 'index', intersect: false },
            legend: { position: 'top' }
          },
          scales: {
            x: {
              stacked: false,
              title: { display: true, text: 'เดือน (YYYY-MM)' }
            },
            y: {
              beginAtZero: true,
              title: { display: true, text: 'มูลค่า (บาท)' }
            }
          }
        }
      });
    }
    // IIFE: โหลดข้อมูลและวาดกราฟ User Step
    (async () => {
      const userId = <?= json_encode($userId, JSON_HEX_TAG) ?>;
      try {
        const res = await fetch(`team_data.php?user_id=${userId}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();
        const rows = json.salestep || [];
        if (!rows.length) {
          alert('ไม่พบข้อมูล Sales Step สำหรับผู้ใช้คนนี้');
          return;
        }
        drawUserStepChart(rows);
      } catch (err) {
        console.error('Error fetching salestep data:', err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้: ' + err.message);
      }
    })();
    // IIFE: โหลดข้อมูลและวาดกราฟ Team Step
    (async () => {
      const userId = <?= json_encode($userId, JSON_HEX_TAG) ?>;
      try {
        const res = await fetch(`team_data.php?user_id=${userId}`);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();
        const rows = json.salestepteam || [];
        if (!rows.length) {
          alert('ไม่พบข้อมูล Sales Step สำหรับทีมนี้');
          return;
        }
        drawTeamStepChart(rows);
      } catch (err) {
        console.error('Error fetching salestepteam data:', err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้: ' + err.message);
      }
    })();
  </script>
<script>
    let pieChart = null;

    // ฟังก์ชันวาดกราฟวงกลม จากข้อมูล [{ product, sum_value }, …]
    function drawPieChart(data) {
      const labels = data.map(item => item.product);
      const values = data.map(item => +item.sum_value);

      const ctx = document.getElementById('pieChart').getContext('2d');
      if (pieChart) pieChart.destroy();

      pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels,
          datasets: [{
            label: 'มูลค่า (บาท)',
            data: values
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: 'right' },
            tooltip: {
              callbacks: {
                label: ctx => `${ctx.label}: ${ctx.parsed.toLocaleString()} บาท`
              }
            }
          }
        }
      });
    }

    // IIFE: ดึงข้อมูลจาก team_data.php แล้ววาดกราฟวงกลม
    (async () => {
      try {
        const res = await fetch('team_data.php');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();

        // อ่าน array จาก key teamsumvaluepercent
        const items = json.teamsumvaluepercent || [];
        if (items.length === 0) {
          alert('ไม่พบข้อมูลสำหรับกราฟวงกลม');
          return;
        }

        drawPieChart(items);
      } catch (err) {
        console.error('Error fetching teamsumvaluepercent:', err);
        alert('ไม่สามารถโหลดข้อมูลกราฟได้: ' + err.message);
      }
    })();
  </script>
</body>
</html>
