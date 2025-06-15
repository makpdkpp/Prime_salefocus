<?php
require_once 'functions.php'; // มี connectDb()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nname = trim($_POST['nname']);
    $surename = trim($_POST['surename']);
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password']));
    $role = $_POST['role'] === 'Admin' ? 1 : 2;

    $db = connectDb();
    $stmt = $db->prepare("INSERT INTO user (nname, surename, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nname, $surename, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('สมัครสมาชิกสำเร็จ'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการสมัครสมาชิก'); window.history.back();</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>สมัครสมาชิก | PrimeFocus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Inter', sans-serif;
      background: #f1f5f9;
    }

    .signup-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      padding: 20px;
    }

    .card-signup {
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

    .form-group label {
      font-weight: 500;
    }

    .form-control {
      border-radius: 6px;
      font-size: 15px;
    }

    .btn-signup {
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

    .btn-signup:hover {
      background-color: #b71c1c;
    }

    .bottom-text {
      text-align: center;
      font-size: 14px;
      margin-top: 15px;
    }

    .bottom-text a {
      color: #6a42f1;
      font-weight: 500;
      text-decoration: none;
    }

    .bottom-text a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .card-signup {
        flex-direction: column;
      }

      .image-section {
        display: none;
      }
    }
  </style>
</head>
<body>
<div class="signup-wrapper">
  <div class="card-signup">
    <!-- รูปด้านซ้าย -->
    <div class="image-section">
      <img src="images/login-illustration.jpg" alt="Signup Illustration">
    </div>

    <!-- แบบฟอร์มด้านขวา -->
    <div class="form-section">
      <div class="logo-title mb-2">
        <img src="images/logo.png" alt="Logo">
        <span><span style="color: #d32f2f;">Prime</span> Focus</span>
      </div>

      <h3>สมัครสมาชิก</h3>
      <form action="register.php" method="POST">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label>ชื่อ</label>
            <input type="text" name="nname" class="form-control" required>
          </div>
          <div class="form-group col-md-6">
            <label>นามสกุล</label>
            <input type="text" name="surename" class="form-control" required>
          </div>
        </div>

        <div class="form-group">
          <label>อีเมล</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
          <label>รหัสผ่าน</label>
          <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group">
          <label>บทบาท</label>
          <select name="role" class="form-control" required>
            <option value="">-- เลือกบทบาท --</option>
            <option value="Admin">Admin</option>
            <option value="Sale">Sale</option>
          </select>
        </div>

        <button type="submit" name="signUp" class="btn btn-signup">สมัครสมาชิก</button>

        <div class="bottom-text">
          มีบัญชีแล้ว? <a href="index.php">เข้าสู่ระบบ</a>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>

