<?php
ob_start();
require_once 'functions.php';
session_start();

$db = connectDb();
$error = handleLogin($db);
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Prime Focus</title>
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
      height: 100vh;
      padding: 20px;
    }

    .card-login {
      display: flex;
      flex-direction: row;
      width: 100%;
      max-width: 960px;
      background: white;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      border-radius: 10px;
      overflow: hidden;
    }

    .card-login .image-section {
      flex: 1;
    }

    .card-login .image-section img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .card-login .form-section {
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

    .btn-outline-custom {
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 6px;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 15px;
      margin-top: 10px;
      background-color: white;
    }

    .btn-outline-custom img {
      height: 20px;
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

    .text-link {
      font-size: 13px;
      color: #d32f2f;
      text-decoration: none;
    }

    .text-link:hover {
      text-decoration: underline;
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
        <span><span style="color: #6a42f1;">Prime</span> Focus</span>
      </div>
      <h3>Login</h3>

      <form method="post" action="index.php">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" class="form-control" placeholder="JohnDoe@gmail.com" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>

        <div class="d-flex justify-content-end mb-3">
          <a href="#" class="text-link">Forgot password?</a>
        </div>

        <button type="submit" name="signIn" class="btn btn-login">Login</button>

        <div class="bottom-text mt-4">
          Don’t have an account? <a href="register.php">Signup now</a>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
