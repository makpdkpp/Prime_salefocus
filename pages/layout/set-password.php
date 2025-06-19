<?php
require_once '../../functions.php';
$db = connectDb();
$token = $_GET['token'] ?? '';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        $stmt = $db->prepare('SELECT id FROM user WHERE reset_token=? AND token_expiry > NOW()');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->bind_result($uid);
        if ($stmt->fetch()) {
            $stmt->close();
            $hashed = md5($password);
            $stmt = $db->prepare('UPDATE user SET password=?, is_active=1, reset_token=NULL, token_expiry=NULL WHERE id=?');
            $stmt->bind_param('si', $hashed, $uid);
            $stmt->execute();
            $stmt->close();
            $message = 'Password set successfully.';
        } else {
            $error = 'Invalid or expired token';
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
</div>
</body>
</html>
<?php $db->close(); ?>
