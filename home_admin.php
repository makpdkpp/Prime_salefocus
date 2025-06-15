<?php
require_once 'functions.php';
session_start();

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
  <meta charset="UTF-8">
  <title>Prime Focus 25 V1 (admin)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="images/logo.png" href="images/logo.png">
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
  <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" />
  <link href="dist/css/AdminLTE.min.css" rel="stylesheet" />
  <link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .summary-boxes {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-bottom: 20px;
      justify-content: space-between;
    }
    .summary-box {
      flex: 1;
      min-width: 200px;
      background: #fff;
      border: 1px solid #ddd;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .summary-box h4 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #555;
    }
    .summary-box p {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
      color: #000;
    }
  </style>
</head>
<body class="skin-blue">
<div class="wrapper">
<header class="main-header">
  <a href="home_admin.php" class="logo"><b>Prime</b>Focus</a>
  <nav class="navbar navbar-static-top" role="navigation">
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only">Toggle</span></a>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="dist/img/user2-160x160.jpg" class="user-image" alt="User Image" />
            <span class="hidden-xs"><?php echo $email; ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
              <p><?php echo $email; ?><small>User</small></p>
            </li>
            <li class="user-footer">
              <div class="pull-right"><a href="logout.php" class="btn btn-default btn-flat">Sign out</a></div>
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
        <img src="dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
      </div>
      <div class="pull-left info">
        <p><?php echo $email; ?> (Admin)</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>
      <li class="active"><a href="home_admin.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li class="treeview active">
            <a href="#"><i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล....</span></a>
            <ul class="treeview-menu">
            <li><a href="pages/layout/top-nav.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลบริษัท</a></li>
                <li><a href="pages/layout/boxed.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
                <li ><a href="pages/layout/fixed.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
                <li><a href="pages/layout/collapsed-sidebar.php"><i class="fa fa-circle-o"></i>ขั้นตอนการขาย</a></li>
                <li><a href="pages/layout/of_winning.php"><i class="fa fa-circle-o"></i>โอกาสสการชนะ</a></li>
                <li><a href="pages/layout/Saleteam.php"><i class="fa fa-circle-o"></i>ทีมขาย</a></li>
                <li><a href="pages/layout/position_u.php"><i class="fa fa-circle-o"></i>ตำแหน่ง</a></li>
                <li><a href="pages/layout/Profile_user.php"><i class="fa fa-circle-o"></i>รายละเอียดผู้ใช้งาน</a></li>
            </ul>
    </ul>
  </section>
</aside>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Dashboard <small>รวมข้อมูลผู้ใช้ทั้งหมด</small></h1>
  </section>
  <section class="content">
    <div class="summary-boxes">
      <div class="summary-box">
        <h4>มูลค่า Forecast  ทั้งหมด</h4>
         <p><span id="estimatevalue"></span> บาท</p>
      </div>
      <div class="summary-box">
        <h4>มูลค่าที่ WIN ทั้งหมด</h4>
        <p><span id="totalWinDisplay"></span> บาท</p>
      </div>
      <div class="summary-box">
        <h4>จำนวนโครงการที่ WIN</h4>
        <p><span id="wincount"></span> โครงการ</p>
      </div>
      <div class="summary-box">
        <h4>จำนวนโครงการที่ LOST</h4>
        <p><span id="lostcount"></span> โครงการ</p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border"><h3 class="box-title">ยอดรวมสะสมทุกคนที่ WIN (เป็นกราฟที่1)</h3></div>
      <div class="box-body"><canvas id="winstatusValueChart" height="180"></canvas></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box box-info">
          <div class="box-header with-border"><h3 class="box-title">ยอดขายรายคน(จำนวนโครงการ)(กราฟที่2)</h3></div>
      <div class="box-body"><canvas id="teamSumChart" height="180"></canvas></div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="box box-success">
          <div class="box-header with-border"><h3 class="box-title">ยอดขายรายคน(รายได้)(กราฟ3)</h3></div>
      <div class="box-body"><canvas id="personSumChart" height="180"></canvas></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="box box-info">
          <div class="box-header with-border"><h3 class="box-title">ยอดขายรายคน(มูลค่า)(กราฟ4)</h3></div>
      <div class="box-body"><canvas id="countByPersonChart" height="180"></canvas></div>
        </div>
      </div>
    </div>

    <div class="row">
  <div class="col-md-6">
    <div class="box box-success">
      <div class="box-header with-border"><h3 class="box-title">สถานะการขายในแต่ละขั้นตอน (ย้ายไปกราฟ55)</h3></div>
          <div class="box-body"><canvas id="salestatusChart" height="180"></canvas></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border"><h3 class="box-title">ประมาณการรายได้ในแต่ละขั้นตอนการขาย(ย้ายไปกราฟ6)</h3></div>
          <div class="box-body"><canvas id="statusValueChart" height="180"></canvas></div>
    </div>
  </div>
  </div>

  <div class="row">
  <div class="col-md-6">
    <div class="box box-success">
      <div class="box-header with-border"><h3 class="box-title">มูลค่า Forecast ทั้งหมด(ย้ายไปกราฟ7)</h3></div>
          <div class="box-body"><canvas id="salesForecastChart" height="180"></canvas></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border"><h3 class="box-title">กราฟข้อมูล % บิดดิ่ง(กราฟ8)</h3></div>
      <div class="box-body"><canvas id="productWinRateChart" height="180"></canvas></div>
    </div>
  </div>
  </div>
  
  <div class="row">
  <div class="col-md-6">
    <div class="box box-success">
      <div class="box-header with-border"><h3 class="box-title">TOP 10 ประเภทโซลูชั่น(กราฟ9)</h3></div>
      <div class="box-body"><canvas id="topProductsChart" height="180"></canvas></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border"><h3 class="box-title">ยอดขาย Top 10 ของลูกค้า(กราฟ10)</h3></div>
      <div class="box-body"><canvas id="topCustomerChart" height="180"></canvas></div>
    </div>
  </div>
  </div>
  <!--
  <div class="row">
  <div class="col-md-6">
    <div class="box box-success">
      <div class="box-header with-border"><h3 class="box-title">TOP 10 ประเภทโซลูชั่น(กราฟ9)</h3></div>
      <div class="box-body"><canvas id="personSumChart" height="180"></canvas></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="box box-info">
      <div class="box-header with-border"><h3 class="box-title">ยอดขาย Top 10 ของลูกค้า(กราฟ10)</h3></div>
      <div class="box-body"><canvas id="chartWin" height="180"></canvas></div>
    </div>
  </div>
  </div>
  </div>
  </div>
  -->
<script>
const chartEstimate = new Chart(document.getElementById('chartEstimate'), {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($estimateLabels); ?>,
    datasets: [{
      label: 'ยอดขายทั้งหมด (บาท)',
      data: <?php echo json_encode($estimateValues); ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.7)'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, title: { display: true, text: 'บาท' } } }
  }
});

const chartWin = new Chart(document.getElementById('3'), {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($estimateLabels); ?>,
    datasets: [{
      label: 'รายได้จริงจาก Win (บาท)',
      data: <?php echo json_encode($winvalue); ?>,
      backgroundColor: 'rgba(75, 192, 192, 0.7)'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, title: { display: true, text: 'บาท' } } }
  }
});
</script>
<script>
    fetch('admin_data.php')
      .then(res => res.json())
      .then(data => {

        const winvalue = data.winvalue;
        const wincount   = data.wincount;
        const estimatevalue   = data.estimatevalue;
        const lostcount = data.lostcount;
       
       //estimatevalue
      document.getElementById('estimatevalue').innerText =
      Number(estimatevalue).toLocaleString('th-TH');
      //รายได้เก็บจริง
      document.getElementById('totalWinDisplay').innerText =
      Number(winvalue).toLocaleString('th-TH');
     //Win
      document.getElementById('wincount').innerText =
      Number(wincount).toLocaleString('th-TH');
      //Lost
      document.getElementById('lostcount').innerText =
      Number(lostcount).toLocaleString('th-TH');

        const chartWin = new Chart(document.getElementById('chartWin'), {
  type: 'bar',
  data: {
    labels: ['ผลรวม'] ,
    datasets: [{
      label: 'รายได้จริงจาก Win (บาท)',
      data: [winvalue] ,
      backgroundColor: 'rgba(75, 192, 192, 0.7)'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: { y: { beginAtZero: true, title: { display: true, text: 'บาท' } } }
  }
});
        

        new Chart(document.getElementById('myChart'), {
          type: 'bar',
          data: {
            labels: step,
            datasets: [{
              label: 'บาท',
              data: sumstep
            }]
          },
          options: {
            scales: {
              y: { beginAtZero: true }
            }
          }
        });
      })
      .catch(err => console.error('Error fetching data:', err));

     
  </script>

<script>
    fetch('admin_data.php')
      .then(res => res.json())
      .then(json => {
        const raw = json.salestatus || [];
        // raw เป็น array ของ { month, present_count, budgeted_count, ... }

        const labels   = raw.map(r => r.month);           // e.g. "2025-05"
        const present  = raw.map(r => Number(r.present_count));
        const budgeted = raw.map(r => Number(r.budgeted_count));
        const tor      = raw.map(r => Number(r.tor_count));
        const bidding  = raw.map(r => Number(r.bidding_count));
        const win      = raw.map(r => Number(r.win_count));
        const lost     = raw.map(r => Number(r.lost_count));

        const ctx = document.getElementById('salestatusChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              { label: 'Present',   data: present,   backgroundColor: 'rgba(75,192,192,0.7)' },
              { label: 'Budgeted',  data: budgeted,  backgroundColor: 'rgba(54,162,235,0.7)' },
              { label: 'TOR',       data: tor,       backgroundColor: 'rgba(255,206,86,0.7)' },
              { label: 'Bidding',   data: bidding,   backgroundColor: 'rgba(255,99,132,0.7)' },
              { label: 'Win',       data: win,       backgroundColor: 'rgba(153,102,255,0.7)' },
              { label: 'Lost',      data: lost,      backgroundColor: 'rgba(255,159,64,0.7)' }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Salestatus per Month'
              },
              legend: {
                position: 'top'
              }
            },
            scales: {
              x: {
                title: { display: true, text: 'เดือน (YYYY-MM)' }
              },
              y: {
                beginAtZero: true,
                title: { display: true, text: 'จำนวนครั้ง' },
                ticks: { precision: 0 }
              }
            }
          }
        });
      })
      .catch(err => {
        console.error('Error loading salestatus:', err);
        document.body.insertAdjacentHTML('beforeend',
          '<p style="color:red;">ไม่สามารถโหลดข้อมูลกราฟได้</p>');
      });
  </script>

<script>
    fetch('admin_data.php')
      .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
      })
      .then(json => {
        // raw array จากคีย์ sumbyperson
        const raw = json.sumbyperson || [];

        // สร้าง labels และ values
        const labels = raw.map(item => item.nname);
        const values = raw.map(item => Number(item.total_value));

        // วาดกราฟแท่ง
        const ctx = document.getElementById('personSumChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'ยอดรวม (บาท)',
              data: values,
              backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'สรุปยอดรวม product_value ต่อผู้ใช้'
              },
              legend: { display: false }
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'ยอดรวม (บาท)'
                },
                ticks: {
                  // แสดงคั่นหลักพัน
                  callback: v => v.toLocaleString('th-TH')
                }
              }
            }
          }
        });
      })
      .catch(err => {
        console.error('Error fetching data:', err);
      });
  </script>
   <script>
    fetch('admin_data.php')
      .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
      })
      .then(json => {
        // raw array จากคีย์ sumbyperteam
        const raw = json.sumbyperteam || [];

        // สร้าง labels (ชื่อทีม) และ values (ยอดรวม)
        const labels = raw.map(item => item.team);
        const values = raw.map(item => Number(item.sumvalue));

        // วาดกราฟแท่ง
        const ctx = document.getElementById('teamSumChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'ยอดรวม (บาท)',
              data: values,
              backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'สรุปยอดรวม product_value ต่อทีม'
              },
              legend: { display: false }
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: 'ยอดรวม (บาท)'
                },
                ticks: {
                  callback: v => v.toLocaleString('th-TH')
                }
              },
              x: {
                title: {
                  display: true,
                  text: 'ทีม'
                }
              }
            }
          }
        });
      })
      .catch(err => {
        console.error('Error fetching data:', err);
      });
  </script>
  <script>
    fetch('admin_data.php')
      .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
      })
      .then(json => {
        const raw = json.salestatusvalue || [];

        const labels    = raw.map(r => r.month);
        const present   = raw.map(r => Number(r.present_value));
        const budgeted  = raw.map(r => Number(r.budgeted_value));
        const tor       = raw.map(r => Number(r.tor_value));
        const bidding   = raw.map(r => Number(r.bidding_value));
        const win       = raw.map(r => Number(r.win_value));
        const lost      = raw.map(r => Number(r.lost_value));

        const ctx = document.getElementById('statusValueChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
              { label: 'Present',   data: present,   backgroundColor: 'rgba(75,192,192,0.7)' },
              { label: 'Budgeted',  data: budgeted,  backgroundColor: 'rgba(54,162,235,0.7)' },
              { label: 'TOR',       data: tor,       backgroundColor: 'rgba(255,206,86,0.7)' },
              { label: 'Bidding',   data: bidding,   backgroundColor: 'rgba(255,99,132,0.7)' },
              { label: 'Win',       data: win,       backgroundColor: 'rgba(153,102,255,0.7)' },
              { label: 'Lost',      data: lost,      backgroundColor: 'rgba(255,159,64,0.7)' }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Salestatus Value per Month'
              },
              legend: {
                position: 'top'
              }
            },
            scales: {
              x: {
                title: { display: true, text: 'เดือน (YYYY-MM)' }
              },
              y: {
                beginAtZero: true,
                title: { display: true, text: 'มูลค่า (บาท)' },
                ticks: { callback: v => v.toLocaleString('th-TH') }
              }
            }
          }
        });
      })
      .catch(err => {
        console.error('Error loading salestatusvalue:', err);
        const msg = document.createElement('p');
        msg.style.color = 'red';
        msg.textContent = 'ไม่สามารถโหลดข้อมูลกราฟได้';
        document.body.appendChild(msg);
      });
  </script>

<script>//
    fetch('admin_data.php')
      .then(res => {
        if (!res.ok) throw new Error('HTTP error ' + res.status);
        return res.json();
      })
      .then(json => {
        const raw = json.salestatusvalue || [];

        const labels    = raw.map(r => r.month);
        
        const win       = raw.map(r => Number(r.win_value));
       

        const ctx = document.getElementById('winstatusValueChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [
             
              { label: 'Win',       data: win,       backgroundColor: 'rgba(153,102,255,0.7)' }            
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Salestatus Value per Month'
              },
              legend: {
                position: 'top'
              }
            },
            scales: {
              x: {
                title: { display: true, text: 'เดือน (YYYY-MM)' }
              },
              y: {
                beginAtZero: true,
                title: { display: true, text: 'มูลค่า (บาท)' },
                ticks: { callback: v => v.toLocaleString('th-TH') }
              }
            }
          }
        });
      })
      .catch(err => {
        console.error('Error loading salestatusvalue:', err);
        const msg = document.createElement('p');
        msg.style.color = 'red';
        msg.textContent = 'ไม่สามารถโหลดข้อมูลกราฟได้';
        document.body.appendChild(msg);
      });
  </script>

  <script>
    (async () => {
      try {
        const res  = await fetch('admin_data.php');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();
        const raw  = json.saleforecast || [];

        if (!raw.length) {
          const canvas = document.getElementById('salesForecastChart');
          canvas.parentNode.replaceChild(
            document.createTextNode('ไม่มีข้อมูลสำหรับแสดงกราฟนี้'),
            canvas
          );
          return;
        }

        const labels   = raw.map(r => r.nname);
        const forecast = raw.map(r => Number(r.forecast));
        const actual   = raw.map(r => Number(r.win_total));

        const ctx = document
          .getElementById('salesForecastChart')
          .getContext('2d');

        new Chart(ctx, {
          type: 'bar',
          data: {
            labels,
            datasets: [
              {
                label: 'Win Total',
                data: actual,
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
              },
{
  		label: 'Forecast',
    		data: forecast,
      		backgroundColor: 'rgba(54, 162, 235, 0.7)'
      		}
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Forecast vs Actual Win per User'
              },
              legend: {
                position: 'top'
              }
            },
            scales: {
              x: {
                title: { display: true, text: 'ผู้ใช้ (nname)' }
              },
              y: {
                beginAtZero: true,
                title: { display: true, text: 'มูลค่า (บาท)' },
                ticks: {
                  callback: v => v.toLocaleString('th-TH')
                }
              }
            }
          }
        });
      } catch (err) {
        console.error('Error loading saleforecast:', err);
        const msg = document.createElement('p');
        msg.style.color = 'red';
        msg.textContent = 'เกิดข้อผิดพลาดในการโหลดข้อมูลกราฟ';
        document.body.appendChild(msg);
      }
    })();
  </script>

   <script>
    (async () => {
      try {
        const res  = await fetch('admin_data.php');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const json = await res.json();
        const raw  = json.countbyperson || [];

        if (!raw.length) {
          const canvas = document.getElementById('countByPersonChart');
          canvas.parentNode.replaceChild(
            document.createTextNode('ไม่มีข้อมูลสำหรับแสดงกราฟนี้'),
            canvas
          );
          return;
        }

        // เตรียม labels และ data array
        const labels      = raw.map(r => r.nname);
        const countValues = raw.map(r => Number(r.count_value));

        // ดึง context ของ canvas
        const ctx = document
          .getElementById('countByPersonChart')
          .getContext('2d');

        // สร้าง bar chart
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels,
            datasets: [{
              label: 'จำนวน Win (count)',
              data: countValues,
              backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: 'Count of Wins per User'
              },
              legend: {
                display: false
              }
            },
            scales: {
              x: {
                title: { display: true, text: 'ผู้ใช้ (nname)' }
              },
              y: {
                beginAtZero: true,
                title: { display: true, text: 'จำนวน Win' },
                ticks: { precision: 0 }
              }
            }
          }
        });
      } catch (err) {
        console.error('Error loading sumbyperson:', err);
        const msg = document.createElement('p');
        msg.style.color = 'red';
        msg.textContent = 'เกิดข้อผิดพลาดในการโหลดข้อมูลกราฟ';
        document.body.appendChild(msg);
      }
    })();
  </script>
<script>
(async () => {
  const res  = await fetch('admin_data.php');
  const json = await res.json();
  const raw  = json.productwinrate || [];

  // แปลง priority จาก string -> ตัวเลข
  function parsePriority(p) {
    p = p.trim();
    if (p.endsWith('%')) return parseFloat(p);
    return 0;
  }

  // เตรียมข้อมูลและ sort จากมาก→น้อย
  const items = raw
    .map(r => ({
      label: r.Product,
      value: parsePriority(r.priority)
    }))
    .sort((a, b) => b.value - a.value);

  const labels = items.map(i => i.label);
  const data   = items.map(i => i.value);

  // สร้าง array สี: ถ้า value >= 80 → แดง (red), else → น้ำเงิน (blue)
  const backgroundColors = data.map(v =>
    v >= 80
      ? 'rgba(255, 99, 132, 0.7)'    // red
      : 'rgba(54, 162, 235, 0.7)'    // blue
  );

  const ctx = document.getElementById('productWinRateChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Priority (%)',
        data,
        backgroundColor: backgroundColors
      }]
    },
    options: {
      indexAxis: 'y',
      scales: {
        y: {
          reverse: false,
          title: { display: true, text: 'Product' }
        },
        x: {
          beginAtZero: true,
          max: 100,
          title: { display: true, text: 'Priority (%)' }
        }
      },
      plugins: {
        title: {
          display: true,
          text: 'Product Win Rate by Priority'
        },
        legend: { display: false }
      }
    }
  });
})();
</script>

<script>
  (async () => {
    // ดึงข้อมูลจาก admin_data.php
    const res  = await fetch('admin_data.php');
    const json = await res.json();
    const raw  = json.TopProductGroup || [];

    // เตรียม labels และ data
    const labels = raw.map(r => r.product);
    const data   = raw.map(r => Number(r.sum_value));

    // สร้างสี: 2 อันดับแรกแดง ที่เหลือฟ้า
    const backgroundColors = data.map((_, i) =>
      i < 2
        ? 'rgba(255, 99, 132, 0.7)'   // red
        : 'rgba(54, 162, 235, 0.7)'   // blue
    );

    // วาดกราฟแท่งแนวนอน
    const ctx = document.getElementById('topProductsChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'ยอดรวม (บาท)',
          data,
          backgroundColor: backgroundColors
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Top 10 Products by Total Value'
          },
          legend: { display: false }
        },
        scales: {
          x: {
            beginAtZero: true,
            title: { display: true, text: 'ยอดรวม (บาท)' }
          },
          y: {
            title: { display: true, text: 'สินค้า' },
            ticks: { mirror: false }
          }
        }
      }
    });
  })();
  </script>

  <script>
  (async () => {
    // ดึงข้อมูลจาก admin_data.php (key: TopCustopmer)
    const res  = await fetch('admin_data.php');
    const json = await res.json();
    const raw  = json.TopCustopmer || [];

    if (!raw.length) {
      const canvas = document.getElementById('topCustomerChart');
      canvas.parentNode.replaceChild(
        document.createTextNode('ไม่มีข้อมูลสำหรับแสดงกราฟนี้'),
        canvas
      );
      return;
    }

    // เตรียม labels และ data
    const labels = raw.map(r => r.company);
    const data   = raw.map(r => Number(r.sum_value));

    // กำหนดสี: 2 อันดับแรกแดง ที่เหลือฟ้า
    const backgroundColors = data.map((_, i) =>
      i < 2
        ? 'rgba(255, 99, 132, 0.7)'   // red
        : 'rgba(54, 162, 235, 0.7)'   // blue
    );

    // วาดกราฟแท่งแนวนอน
    const ctx = document.getElementById('topCustomerChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'ยอดรวม (บาท)',
          data,
          backgroundColor: backgroundColors
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Top 10 ลูกค้าตามยอดรวม'
          },
          legend: { display: false }
        },
        scales: {
          x: {
            beginAtZero: true,
            title: { display: true, text: 'ยอดรวม (บาท)' }
          },
          y: {
            title: { display: true, text: 'บริษัท/ลูกค้า' },
            ticks: { mirror: false }
          }
        }
      }
    });
  })();
  </script>

  </section>
</div>
</div>

</body>
</html>
