<?php
session_start();
require_once '../../functions.php';
require_once '../../lib/PHPMailer/src/PHPMailer.php';
require_once '../../lib/PHPMailer/src/SMTP.php';
require_once '../../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = connectDb();
$message = '';
$message_type = ''; // 'success' or 'danger'

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_email'])) {
    $email = trim($_POST['invite_email']);
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        $stmt = $db->prepare('SELECT user_id FROM user WHERE email=?');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($uid);
                $stmt->fetch();
                $stmt->close();
                $stmt = $db->prepare('UPDATE user SET reset_token=?, token_expiry=?, is_active=0 WHERE user_id=?');
                if ($stmt) {
                    $stmt->bind_param('ssi', $token, $expiry, $uid);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $stmt->close();
                $stmt = $db->prepare('INSERT INTO user (email, reset_token, token_expiry) VALUES (?, ?, ?)');
                if ($stmt) {
                    $stmt->bind_param('sss', $email, $token, $expiry);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } else {
            $message = 'Database error: ' . $db->error;
            $message_type = 'danger';
        }

        if (empty($message)) {
            $link = 'http://' . $_SERVER['HTTP_HOST'] . '/pages/layout/set-password.php?token=' . $token; // ปรับ path ให้ถูกต้อง
            $subject = 'User Invitation to PrimeForecast';
            $body = "Please click on the link to set your password: $link";

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'immsendermail@gmail.com';
                $mail->Password = 'npou efln pgpf bhxd';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('no-reply@primeforecast.com', 'PrimeForecast Admin');
                $mail->addAddress($email);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->send();
                $message = 'Invitation sent successfully to ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                $message_type = 'success';
            } catch (Exception $e) {
                $message = 'Mailer Error: ' . $mail->ErrorInfo;
                $message_type = 'danger';
            }
        }
    } else {
        $message = 'Invalid email address provided.';
        $message_type = 'danger';
    }
}

$query = $db->query('SELECT user_id, email, is_active FROM user ORDER BY user_id DESC');
$userRows = $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
if ($query) {
    $query->free();
}
$db->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เพิ่มผู้ใช้งาน | PrimeForecast</title>

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
    .btn-add {
      position: fixed; bottom: 30px; right: 30px; background: #0056b3;
      color: #fff; border-radius: 50%; width: 56px; height: 56px;
      font-size: 24px; border: none; z-index: 1040;
    }
    .modal-content { border-radius: 10px; padding: 20px; }
    .table thead { background: #0056b3; color: white; }
    .pagination .page-item.active .page-link { background-color: #0056b3; border-color: #0056b3; }
  </style>
</head>
<body class="hold-transition sidebar-mini">
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
          <img src="../../dist/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
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
    <img src="../../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
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
              <li class="nav-item"><a href="Profile_user.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>รายละเอียดผู้ใช้งาน</p></a></li>
              <li class="nav-item"><a href="newuser.php" class="nav-link active"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
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
              <li class="breadcrumb-item active">เพิ่มผู้ใช้งาน</li>
            </ol>
          </div>
        </div>
      </div>
    </section>

    <section class="content">
      <div class="container1">
        <h3>รายชื่อผู้ใช้งานในระบบ</h3>
        <?php if(!empty($message)): ?>
          <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
          </div>
        <?php endif; ?>
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Email</th>
              <th style="width: 150px;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($userRows as $u): ?>
            <tr>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <?php if ($u['is_active']): ?>
                  <span class="badge badge-success">Active</span>
                <?php else: ?>
                  <span class="badge badge-warning">Pending Invitation</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($userRows)): ?>
              <tr><td colspan="2" class="text-center">-- ไม่มีผู้ใช้งานในระบบ --</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <button class="btn-add" data-toggle="modal" data-target="#inviteModal" title="Invite New User">
    <i class="fas fa-user-plus"></i>
  </button>

  <div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Invite User by Email</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="invite_email">Email Address</label>
              <input type="email" id="invite_email" name="invite_email" class="form-control" placeholder="Enter email" required>
            </div>
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Send Invitation</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div><script src="../../plugins_v3/jquery/jquery.min.js"></script>
<script src="../../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>

</body>
</html> 