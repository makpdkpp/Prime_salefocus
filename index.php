<?php
ob_start();
require_once 'functions.php';
session_start();

$db = connectDb();
$error = handleLogin($db);
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>Login | Prime Forecast</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
  body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', sans-serif;
    background: #f1f5f9;
  }

  .login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
  }

  .card-login {
    display: flex;
    flex-direction: row;
    width: 100%;
    max-width: 960px;
    background: white;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
  }

  .image-section {
    flex: 1;
    display: block;
  }

  .image-section img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .form-section {
    flex: 1;
    padding: 50px 40px;
  }

  .logo-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 22px;
    font-weight: bold;
  }

  .logo-title img {
    height: 28px;
  }

  .form-section h3 {
    font-weight: 700;
    margin-top: 15px;
    margin-bottom: 30px;
  }

  .form-control {
    border-radius: 6px;
    font-size: 15px;
  }

  .btn-login {
    background-color: #d32f2f;
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 10px;
    font-size: 16px;
    border-radius: 6px;
    width: 100%;
    transition: all 0.3s ease;
  }

  .btn-login:hover {
    background-color: #b71c1c;
  }

  .text-link {
    font-size: 13px;
    color: #d32f2f;
    text-decoration: none;
  }

  .text-link:hover {
    text-decoration: underline;
  }

  .bottom-text {
    text-align: center;
    font-size: 14px;
    margin-top: 15px;
  }

  .bottom-text a {
    color: #d32f2f;
    font-weight: 500;
    text-decoration: none;
  }

  .bottom-text a:hover {
    text-decoration: underline;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .card-login {
      flex-direction: column;
    }

    .image-section {
      display: none; /* หรือจะเปลี่ยนเป็นแสดงด้านบนก็ได้ */
    }

    .form-section {
      padding: 30px 20px;
    }
  }

  @media (max-width: 480px) {
    .form-section {
      padding: 20px 15px;
    }

    .logo-title {
      font-size: 18px;
    }

    .form-section h3 {
      font-size: 20px;
    }
  }
</style>
</head>
<body>
<div class="login-wrapper">
  <div class="card-login">
    <div class="image-section">
      <img src="images/login-illustration.jpg" alt="Login Illustration">
    </div>
    <div class="form-section">
      <div class="logo-title mb-2">
        <img src="images/logo.png" alt="Logo">

        <span><span style="color: #d32f2f;">Prime</span> Forecast</span>

      </div>
      <h3>เข้าสู่ระบบ</h3>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="index.php">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="JohnDoe@primes.co.th" required>
        </div>
        <div class="form-group">
          <label>รหัสผ่าน</label>
          <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
        </div>
        <div class="d-flex justify-content-end mb-3">
          <a href="#" class="text-link" data-toggle="modal" data-target="#resetPasswordModal">ลืมรหัสผ่าน?</a>
        </div>
        <button type="submit" name="signIn" class="btn btn-login">เข้าสู่ระบบ</button>
        <div class="bottom-text mt-4">
          <!-- ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a> -->
        </div>
      </form>
      <?php include 'pages/layout/resetpassword-modal.php'; ?>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    </div>
  </div>
</div>
<script>
  document.getElementById('password').addEventListener('input', function (e) {
    // กรองอักขระภาษาไทย
    const thaiPattern = /[\u0E00-\u0E7F]/g;
    if (thaiPattern.test(this.value)) {
      this.value = this.value.replace(thaiPattern, '');
    }
  });
</script>

</body>
</html>
