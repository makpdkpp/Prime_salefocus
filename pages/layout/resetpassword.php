<?php
// resetpassword.php - ฟอร์มขอรีเซ็ตรหัสผ่าน
require_once '../../functions.php';
require_once '../../lib/PHPMailer/src/PHPMailer.php';
require_once '../../lib/PHPMailer/src/SMTP.php';
require_once '../../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = connectDb();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'อีเมลไม่ถูกต้อง';
    } else {
        $stmt = $db->prepare('SELECT user_id FROM user WHERE email=?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($uid);
        if ($stmt->fetch()) {
            $stmt->close();
            // สร้าง token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+8 hours'));
            $stmt = $db->prepare('UPDATE user SET reset_token=?, token_expiry=? WHERE user_id=?');
            $stmt->bind_param('ssi', $token, $expiry, $uid);
            $stmt->execute();
            $stmt->close();
            // ส่งอีเมลด้วย PHPMailer
            $resetLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/pages/layout/set-password.php?token=' . $token;
            $subject = 'Reset your password';
            $body = "<p>กรุณาคลิกลิงก์นี้เพื่อรีเซ็ตรหัสผ่านของคุณ:<br><a href='$resetLink'>$resetLink</a></p>";
            try {
                $mail = new PHPMailer(true);
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'immsendermail@gmail.com';
                $mail->Password = 'npou efln pgpf bhxd';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->setFrom('no-reply@example.com', 'PrimeFocus');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->send();
                $message = 'ส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลแล้ว';
            } catch (Exception $e) {
                $error = 'เกิดข้อผิดพลาดในการส่งอีเมล: ' . $mail->ErrorInfo;
            }
        } else {
            $error = 'ไม่พบอีเมลนี้ในระบบ';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body style="background:#e9f2f9;">
<div class="container" style="max-width:500px;margin-top:60px;">
  <h3 class="mb-4">รีเซ็ตรหัสผ่าน</h3>
  <?php if($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php elseif($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>อีเมลที่ลงทะเบียน</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">ส่งลิงก์รีเซ็ตรหัสผ่าน</button>
    <a href="../../index.php" class="btn btn-link">กลับหน้าเข้าสู่ระบบ</a>
  </form>
</div>
</body>
</html>
<?php $db->close(); ?>
