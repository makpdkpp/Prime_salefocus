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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_email'])) {
    $email = trim($_POST['invite_email']);
    if (!empty($email)) {
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
        }

        $link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/set-password.php?token=' . $token;
        $subject = 'User Invitation';
        $body = "Please set your password using this link: $link";

        // PHPMailer: send email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'immsendermail@gmail.com';
            $mail->Password = 'npou efln pgpf bhxd';
            $mail->SMTPSecure = 'ssl'; // Use 'ssl' for port 465
            $mail->Port = 465;
            $mail->setFrom('no-reply@example.com', 'PrimeFocus');
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            $message = 'Invitation sent to ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        } catch (Exception $e) {
            $message = 'Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}

$query = $db->query('SELECT user_id, email, is_active FROM user ORDER BY user_id DESC');
$userRows = $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
if ($query) {
    $query->free();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Invite User | PrimeFocus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
      body { background: #e9f2f9; }
      .container1 { max-width: 800px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
      th { background: #0056b3; color: #fff; }
      .btn-add {
        position: fixed; bottom: 30px; right: 30px; background: #0056b3;
        color: #fff; border-radius: 50%; width: 56px; height: 56px;
        font-size: 24px; border: none; z-index: 999;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      }
      .modal-content { border-radius: 10px; padding: 20px; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
<header class="main-header">
  <a href="../../home_admin.php" class="logo"><b>Prime</b>Focus</a>
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
        <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width:45px;height:45px;">
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
          <li><a href="top-nav.php"><i class="fas fa-building"></i> เพิ่มข้อมูลบริษัท</a></li>
          <li><a href="boxed.php"><i class="fas fa-boxes"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
          <li><a href="fixed.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
          <li><a href="Source_of_the_budget.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลที่มาของงบประมาณ</a></li>
          <li><a href="collapsed-sidebar.php"><i class="fas fa-tasks"></i> ขั้นตอนการขาย</a></li>
          <li><a href="of_winning.php"><i class="fas fa-trophy"></i> โอกาสการชนะ</a></li>
          <li><a href="Saleteam.php"><i class="fas fa-users"></i> ทีมขาย</a></li>
          <li><a href="position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
          <li><a href="Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
          <li class="active"><a href="newuser.php"><i class="fas fa-user-plus"></i> เพิ่มผู้ใช้งาน</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>
<div class="content-wrapper">
<section class="content">
  <div class="container1">
    <h3>Invite Users</h3>
    <?php if(!empty($message)): ?>
      <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>
    <table class="table table-bordered">
      <thead>
        <tr><th>Email</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php foreach($userRows as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['is_active'] ? 'Active' : 'Pending' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <!-- ปุ่ม invite แบบ floating -->
  <button class="btn-add" data-toggle="modal" data-target="#inviteModal"><i class="fa fa-user-plus"></i></button>
</section>
</div>
<div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Invite User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="invite_email" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-block">Send</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $db->close(); ?>
