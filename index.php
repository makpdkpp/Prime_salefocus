<?php
ob_start();
require_once 'functions.php';
session_start();

$db = connectDb();
$error = handleLogin($db);
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
  <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" />
  <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" />
  <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Custom Theme - Red and White -->
  <style>
    body {
      background-color: #ffffff;
      color: #333;
      font-family: Arial, sans-serif;
    }

    .modal {
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #d32f2f;
      padding: 20px 30px;
      border-radius: 10px;
      color: #fff;
      min-width: 300px;
      max-width: 90%;
      text-align: center;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }

    .close {
      position: absolute;
      top: 12px;
      right: 20px;
      color: #fff;
      font-size: 24px;
      cursor: pointer;
    }

    .title {
      color: #d32f2f;
      font-size: 24px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .input-box i {
      color: #d32f2f;
    }

    .text a,
    .text label {
      color: #d32f2f;
      cursor: pointer;
    }

    input.btn {
      background-color: #d32f2f; /* สีแดงหลัก */
  color: white;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

    input.btn:hover {
      background-color: #b71c1c; /* สีแดงเข้มตอน hover */
      transform: scale(1.05);
    }

    .form-group label {
      color: #d32f2f;
      margin-top: 10px;
    }

    .cover img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }
    
  </style>
</head>

<body>
<div id="popup" style="display:none;" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closePopup()">&times;</span>
    <p id="popup-message"></p>
  </div>
</div>

<div class="container">
  <input type="checkbox" id="flip">
  <div class="cover">
    <div class="front">
      <img src="images/frontImg.jpg" alt="">
      <div class="text">
        <span class="text-1">Primes <br> -----</span>
        <span class="text-2">------</span>
      </div>
    </div>
    <div class="back">
      <img class="backImg" src="images/backImg.jpg" alt="">
      <div class="text">
        <span class="text-1">prime <br> ------</span>
        <span class="text-2">-------</span>
      </div>
    </div>
  </div>

  <div class="forms">
    <div class="form-content" id="signIn">
      <div class="login-form">
        <div class="title">Login</div>
        <form method="post" action="index.php">
          <div class="input-boxes">
            <div class="input-box">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="input-box">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="text"><a href="#">Forgot password?</a></div>
            <div class="button input-box">
              <input type="submit" class="btn" value="Submit" name="signIn">
            </div>
            <div class="text sign-up-text">Don't have an account? <label for="flip">Signup now</label></div>
          </div>
        </form>
      </div>

      <div class="signup-form" id="signup">
        <div class="title">SignupSSS</div>
        <form method="post" action="functions.php">
          <div class="input-boxes">
            <div class="input-box">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="email" placeholder="Enter your email" required>
            </div>
            <div class="input-box">
              <i class="fas fa-lock"></i>
              <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
              <label>ประเภทผู้ใช้งาน :</label>
              <select class="form-control" name="role" id="role">
                <option value="">-- โปรดเลือก --</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
              </select>
            </div>
          </div>
          <div class="button input-box" >
            <input type="submit" class="btn" value="Submit" name="signUp">
          </div>
          <div class="text sign-up-text">Already have an account? <label for="flip">Login now</label></div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function closePopup() {
  document.getElementById("popup").style.display = "none";
}

window.onload = function() {
  const urlParams = new URLSearchParams(window.location.search);
  const error = urlParams.get('error');
  const success = urlParams.get('success');

  if (error || success) {
    const popup = document.getElementById("popup");
    const message = document.getElementById("popup-message");
    popup.style.display = "flex";
    message.innerText = error ? error : success;
  }
};
</script>
</body>
</html>
