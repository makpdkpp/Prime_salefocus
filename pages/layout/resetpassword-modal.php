<?php
// resetpassword-modal.php - เฉพาะฟอร์มรีเซ็ตรหัสผ่านสำหรับ modal
require_once __DIR__ . '/../../functions.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = connectDb();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modal_reset'])) {
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
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+8 hours'));
            $stmt = $db->prepare('UPDATE user SET reset_token=?, token_expiry=? WHERE user_id=?');
            $stmt->bind_param('ssi', $token, $expiry, $uid);
            $stmt->execute();
            $stmt->close();
            $resetLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/pages/layout/set-password.php?token=' . $token;
            $subject = 'Reset your password';
            $body = "<p>กรุณาคลิกลิงก์นี้เพื่อรีเซ็ตรหัสผ่านของคุณ:<br><a href='$resetLink'>$resetLink</a></p>";
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'immsendermail@gmail.com';
                $mail->Password = 'npou efln pgpf bhxd';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->setFrom('no-reply@example.com', 'PrimeForecast');
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
<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="resetPasswordModalLabel">รีเซ็ตรหัสผ่าน</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php if($message): ?>
          <div class="alert alert-success"><?= $message ?></div>
        <?php elseif($error): ?>
          <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" id="resetPasswordForm">
          <div class="form-group">
            <label>อีเมลที่ลงทะเบียน</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <input type="hidden" name="modal_reset" value="1">
          <button type="submit" class="btn btn-primary">ส่งลิงก์รีเซ็ตรหัสผ่าน</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $db->close(); ?>
