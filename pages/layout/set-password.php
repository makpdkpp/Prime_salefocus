<?php
require_once '../../functions.php';
$db = connectDb();
$token = $_GET['token'] ?? '';
$message = '';
$error = '';
$debug = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $db->prepare('SELECT user_id, token_expiry, NOW() FROM user WHERE reset_token=?');
        if ($stmt) {
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->bind_result($uid, $token_expiry, $now);
            if ($stmt->fetch()) {
                $debug .= "<div style='color:blue'>user_id: $uid<br>token_expiry: $token_expiry<br>NOW(): $now<br>token: $token</div>";
                $stmt->close();
                if ($token_expiry > $now) {
                    $hashed = md5($password);
                    $stmt = $db->prepare('UPDATE user SET password=?, is_active=1, reset_token=NULL, token_expiry=NULL WHERE user_id=?');
                    if ($stmt) {
                        $stmt->bind_param('si', $hashed, $uid);
                        $stmt->execute();
                        $stmt->close();
                        header('Location: ../../index.php');
                        exit;
                    } else {
                        $error = 'Database error (update)';
                    }
                } else {
                    $error = 'Token expired';
                }
            } else {
                $error = 'Invalid or expired token';
                $debug .= "<div style='color:red'>ไม่พบ token นี้ในฐานข้อมูล หรือหมดอายุแล้ว</div>";
            }
        } else {
            $error = 'Database error (select)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Set Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body style="background:#e9f2f9;">
<div class="container" style="max-width:500px;margin-top:60px;">
  <h3 class="mb-4">Set Your Password</h3>
  <?php if($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php elseif($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>
  <form method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Set Password</button>
  </form>
  <?php if($debug) echo $debug; ?>
</div>
</body>
</html>
<?php $db->close(); ?>
