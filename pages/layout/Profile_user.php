<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

// Update forecast if submitted
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
  <meta charset="UTF-8">
  <title>ผู้ใช้งาน | PrimeForecast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    body { background: #e9f2f9; }
    .container1 {
      max-width: 1100px;
      margin: 40px auto;
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    input {
      width: 100%; padding: 10px; margin-bottom: 20px;
      border: 1px solid #ccc; border-radius: 5px; font-size: 16px;
    }
    .btn-add {
      position: fixed; bottom: 30px; right: 30px; background: #0056b3;
      color: #fff; border-radius: 50%; width: 56px; height: 56px;
      font-size: 24px; border: none; z-index: 999;
    }
    .modal-content { border-radius: 10px; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
    th { background: #0056b3; color: white; }
    tr:hover { background-color: #f5f5f5; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
<header class="main-header">
  <a href="../../home_admin.php" class="logo"><b>Prime</b>Forecast</a>
  <nav class="navbar navbar-static-top">
    <div class="navbar-custom-menu ml-auto d-flex justify-content-end w-100">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="../../dist/img/user2-160x160.jpg" class="user-image img-circle elevation-2">
            <span class="hidden-xs text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
          </a>
          <ul class="dropdown-menu">
            <li class="user-header">
              <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" style="width:60px;height:60px">
              <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
            </li>
            <li class="user-footer">
              <div class="d-flex justify-content-end w-100">
                <a href="../../logout.php" class="btn btn-default btn-flat">Sign out</a>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>

<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
      </div>
      <div class="info pl-2">
        <p class="mb-0 text-white font-weight-bold" style="font-size:14px;">
          <?php echo $_SESSION['email'] ?? ''; ?> <span class="text-muted" style="font-size:12px;">(Admin)</span>
        </p>
        <a href="#" class="d-block text-success"><i class="fa fa-circle"></i> Online</a>
      </div>
    </div>
    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li><a href="../../home_admin.php"><i class="fa fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
      <li class="treeview active">
        <a href="#">
          <i class="fa fa-folder-open"></i> <span>เพิ่มข้อมูล....</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="../layout/top-nav.php"><i class="fas fa-building"></i> เพิ่มข้อมูลบริษัท</a></li>
            <li><a href="../layout/boxed.php"><i class="fas fa-boxes"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
            <li><a href="../layout/fixed.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
            <li><a href="../layout/Source_of_the_budget.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลที่มาของงบประมาณ</a></li>
            <li><a href="../layout/collapsed-sidebar.php"><i class="fas fa-tasks"></i> ขั้นตอนการขาย</a></li>
            <li><a href="../layout/of_winning.php"><i class="fas fa-trophy"></i> โอกาสการชนะ</a></li>
            <li><a href="../layout/Saleteam.php"><i class="fas fa-users"></i> ทีมขาย</a></li>
            <li><a href="../layout/position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
            <li class="active"><a href="../layout/Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
            <li><a href="../layout/newuser.php"><i class="fas fa-user-plus"></i> เพิ่มผู้ใช้งาน</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>
<div class="content-wrapper">
  <section class="content">
    <div class="container1">
      <h3>ข้อมูลผู้ใช้งาน</h3>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ชื่อ</th>
            <th>นามสกุล</th>
            <th>Email</th>
            <th>Target</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['nname']) ?></td>
              <td><?= htmlspecialchars($row['surename']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= number_format((float)$row['forecast']) ?></td>
              <td>
                <button class='btn btn-sm btn-warning' data-toggle="modal" data-target="#editModal" data-id="<?= $row['user_id'] ?>" data-forecast="<?= $row['forecast'] ?>">
                  <i class='fa fa-edit'></i> Edit
                </button>
                <a href='delete_Pro.php?user_id=<?= $row['user_id'] ?>' onclick="return confirm('คุณต้องการลบหรือไม่?')" class='btn btn-sm btn-danger'>
                  <i class='fa fa-trash'></i> Delete
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

<!-- Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form method="POST" onsubmit="return validateForecast()">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">แก้ไข Forecast</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="editUserId">
          <div class="form-group">
            <label for="forecast">Forecast (บาท)</label>
            <input type="int" name="forecast" id="editForecast" class="form-control" required pattern="^[0-9,]+$" title="กรุณากรอกเฉพาะตัวเลขหรือจุลภาค (,) เท่านั้น">
            <div class="error-message d-none" id="forecastError">กรุณากรอกเฉพาะตัวเลขจำนวนเงินบาท เช่น 100,000</div>
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

<script>
  $('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var userId = button.data('id');
    var forecast = button.data('forecast');

    var modal = $(this);
    modal.find('#editUserId').val(userId);
    modal.find('#editForecast').val(forecast);
    $('#forecastError').addClass('d-none');
  });

  function validateForecast() {
    const input = document.getElementById('editForecast');
    const value = input.value;
    const regex = /^[0-9,]+$/;
    if (!regex.test(value)) {
      document.getElementById('forecastError').classList.remove('d-none');
      return false;
    }
    return true;
  }
</script>
</body>
</html>
