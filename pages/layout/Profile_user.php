<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();
$avatar = htmlspecialchars($_SESSION['avatar'] ?? '../../dist/img/user2-160x160.jpg', ENT_QUOTES, 'UTF-8');

// ส่วน PHP สำหรับอัปเดตข้อมูล จะคงไว้เหมือนเดิม
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateForecast'])) {
  $forecast = str_replace(',', '', $_POST['forecast'] ?? '');
  $userId   = $_POST['user_id'] ?? '';

  if ($forecast !== '' && is_numeric($userId) && is_numeric($forecast)) {
    $stmt = $mysqli->prepare("UPDATE user SET forecast = ? WHERE user_id = ?");
    if ($stmt) {
      $stmt->bind_param("si", $forecast, $userId);
      $stmt->execute();
      $stmt->close();
      header("Location: Profile_user.php");
      exit;
    }
  } else {
    $error = "กรุณากรอกเฉพาะตัวเลขเท่านั้น (บาท)";
  }
}

$sql = "SELECT user_id, nname, surename, email, forecast FROM user WHERE role_id = 2";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>รายละเอียดผู้ใช้งาน | PrimeForecast</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../../plugins_v3/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../dist_v3/css/adminlte.min.css">

  <style>
    /* CSS หลักสำหรับทุกหน้า */
    .content-wrapper { background-color: #b3d6e4; }
    .container1 {
      max-width: 1100px;
      margin: 20px auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .modal-content { border-radius: 10px; padding: 20px; }
    .table thead { background: #0056b3; color: white; }
    .pagination .page-item.active .page-link { background-color: #0056b3; border-color: #0056b3; }
    .btn-custom-edit {
        background-color: #17a2b8; /* สี Info หรือสีฟ้าอมเขียว */
        border-color: #17a2b8;
        color: #fff; /* สีตัวอักษร */
    }

    .btn-custom-edit:hover {
        background-color: #138496; /* สีเข้มขึ้นเมื่อเมาส์ชี้ */
        border-color: #117a8b;
        color: #fff;
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
          <img src="../../<?= $avatar ?>" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="../../<?= $avatar ?>" class="img-circle elevation-2" alt="User Image">
            <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
          </li>
          <li class="user-footer">
            <a href="../../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../../home_admin.php" class="brand-link" style="background-color: #0056b3; text-align: center;">
        <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
        <div class="image">
          <a href="adminedit_profile.php"> <img src="../../<?= $avatar ?>" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;"></a>
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></a>
          <a href="#" class="d-block" style="color: #c2c7d0; font-size: 0.9em;"><i class="fa fa-circle text-success" style="font-size: 0.7em;"></i> Online</a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN NAVIGATION</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../../home_admin.php" class="nav-link">
                  <i class="far fa-chart-bar nav-icon"></i>
                  <p>Dashboard (กราฟ)</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="super_admin_table.php" class="nav-link">
                  <i class="fas fa-table nav-icon"></i>
                  <p>Dashboard (ตาราง)</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item menu-is-opening menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-folder-open"></i><p>เพิ่มข้อมูล....<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="top-nav.php" class="nav-link"><i class="fas fa-building nav-icon"></i><p>เพิ่มข้อมูลบริษัท</p></a></li>
              <li class="nav-item"><a href="boxed.php" class="nav-link"><i class="fas fa-boxes nav-icon"></i><p>เพิ่มข้อมูลกลุ่มสินค้า</p></a></li>
              <li class="nav-item"><a href="fixed.php" class="nav-link"><i class="fas fa-industry nav-icon"></i><p>เพิ่มข้อมูลอุตสาหกรรม</p></a></li>
              <li class="nav-item"><a href="Source_of_the_budget.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>เพิ่มข้อมูลที่มาของงบประมาณ</p></a></li>
              <li class="nav-item"><a href="collapsed-sidebar.php" class="nav-link"><i class="fas fa-tasks nav-icon"></i><p>ขั้นตอนการขาย</p></a></li>
              <li class="nav-item"><a href="of_winning.php" class="nav-link"><i class="fas fa-trophy nav-icon"></i><p>โอกาสการชนะ</p></a></li>
              <li class="nav-item"><a href="Saleteam.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>ทีมขาย</p></a></li>
              <li class="nav-item"><a href="position_u.php" class="nav-link"><i class="fas fa-user-tag nav-icon"></i><p>ตำแหน่ง</p></a></li>
              <li class="nav-item"><a href="Profile_user.php" class="nav-link active"><i class="fas fa-id-card nav-icon"></i><p>รายละเอียดผู้ใช้งาน</p></a></li>
              <li class="nav-item"><a href="newuser.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
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
            <h1></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../../home_admin.php">หน้าหลัก</a></li>
              <li class="breadcrumb-item active">ข้อมูลผู้ใช้งาน</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container1">
        <h3>ข้อมูลผู้ใช้งาน</h3>
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h5><i class="icon fas fa-ban"></i> Error!</h5>
              <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>ชื่อ</th>
              <th>นามสกุล</th>
              <th>Email</th>
              <th>Target</th>
              <th style="width: 160px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['nname']) ?></td>
                <td><?= htmlspecialchars($row['surename']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= number_format((float)$row['forecast'], 2) ?></td>
                <td>
  <button class='btn btn-sm btn-custom-edit btn-edit' data-toggle="modal" data-target="#editModal" data-id="<?= $row['user_id'] ?>" data-forecast="<?= $row['forecast'] ?>">
    <i class='fas fa-edit'></i> Edit
  </button>
  <a href='delete_Pro.php?user_id=<?= $row['user_id'] ?>' onclick="return confirm('คุณต้องการลบหรือไม่?')" class='btn btn-sm btn-danger'>
    <i class='fas fa-trash'></i> Delete
  </a>
</td>
              </tr>
            <?php endwhile; ?>
            <?php if ($result->num_rows === 0): ?>
              <tr><td colspan='5' class='text-center'>-- ไม่พบข้อมูลในระบบ --</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST" onsubmit="return validateForecast()">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">แก้ไข Target</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="user_id" id="editUserId">
            <div class="form-group">
              <label for="forecast">Target (บาท)</label>
              <input type="text" name="forecast" id="editForecast" class="form-control" required>
              <small id="forecastError" class="form-text text-danger d-none">กรุณากรอกเฉพาะตัวเลขเท่านั้น</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
            <button type="submit" name="updateForecast" class="btn btn-primary">บันทึก</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="../../plugins_v3/jquery/jquery.min.js"></script>
<script src="../../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>

<script>
  $(document).ready(function () {
    $('#editModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var userId = button.data('id');
      // แปลง forecast เป็นตัวเลขที่ไม่มี comma
      var forecast = String(button.data('forecast')).replace(/,/g, '');

      var modal = $(this);
      modal.find('#editUserId').val(userId);
      modal.find('#editForecast').val(forecast);
      $('#forecastError').addClass('d-none');
    });
  });

  function validateForecast() {
    const input = document.getElementById('editForecast');
    const errorDiv = document.getElementById('forecastError');
    const value = input.value.replace(/,/g, ''); // เอา comma ออกก่อนเช็ค

    if (isNaN(value) || value.trim() === '') {
      errorDiv.classList.remove('d-none');
      return false;
    }
    errorDiv.classList.add('d-none');
    return true;
  }
</script>
</body>
</html>