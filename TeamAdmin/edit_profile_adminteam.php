<?php

require_once '../functions.php';
$conn = connectDb();
session_start();

// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];

// ดึงข้อมูล user
$stmt = $conn->prepare(
  "SELECT nname, surename, email, avatar_path, role_id, position_id 
   FROM user 
   WHERE user_id = ?"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// เตรียมข้อมูลสำหรับแสดง
$nname   = htmlspecialchars($user['nname'],   ENT_QUOTES, 'UTF-8');
$surname = htmlspecialchars($user['surename'],ENT_QUOTES, 'UTF-8');
$email   = htmlspecialchars($user['email'],   ENT_QUOTES, 'UTF-8');
$avatar  = $user['avatar_path']
           ? htmlspecialchars($user['avatar_path'], ENT_QUOTES, 'UTF-8')
           : '../dist/img/user2-160x160.jpg';

// ดึงชื่อ Role จาก role_catalog
$roles = [];
$rs = $conn->query("SELECT role_id, role FROM role_catalog ORDER BY role");
while ($r = $rs->fetch_assoc()) {
    $roles[(int)$r['role_id']] = $r['role'];
}
$rs->free();

// ดึงชื่อ Position จาก position
$positions = [];
$ps = $conn->query("SELECT position_id, position FROM position ORDER BY position");
while ($p = $ps->fetch_assoc()) {
    $positions[(int)$p['position_id']] = $p['position'];
}
$ps->free();

$roleName     = $roles[(int)$user['role_id']]       ?? 'Unknown';
$positionName = $positions[(int)$user['position_id']] ?? 'Unknown';
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>เพิ่มรายละเอียดการขาย</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<link rel="stylesheet" href="../plugins_v3/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="../dist_v3/css/adminlte.min.css">
    
 <style>
    /* ==== ปรับขนาดรูปใน sidebar ให้เท่ากันตอนยุบ/ขยาย ==== */
    body.sidebar-mini .main-sidebar .user-panel .image img,
    body:not(.sidebar-mini) .main-sidebar .user-panel .image img {
      width: 40px;
      height: 40px;
      object-fit: cover;
    }

    /* ==== เอาแถบขาวด้านหลัง content ออก (เฉพาะ background ของ wrapper) ==== */
    .content-wrapper {
      background: none;
    }

    /* ==== สไตล์กล่องโปรไฟล์ ==== */
    .profile-box {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;            /* ให้เป็นขาว */
      padding: 30px;               /* รักษาช่องว่าง */
      border-radius: 8px;          /* มุมโค้ง */
      box-shadow: 0 2px 6px rgba(0,0,0,0.1); /*เงานิดหน่อยให้ดูเด่น*/
    }

    .profile-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .avatar-wrapper {
      position: relative;
      width: 160px;
      height: 160px;
      margin: 0 auto 30px;
    }
    .avatar-wrapper img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
      border: 2px solid #ccc;
    }
    .avatar-wrapper .btn-avatar {
      position: absolute;
      bottom: 5px; right: 5px;
      background: #d9534f; color: #fff;
      border: none; width: 32px; height: 32px;
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 18px;
    }
    .profile-details .label {
      font-weight: bold;
      color: #333;
      margin-bottom: 5px;
    }
    .profile-details .value {
      color: #333;
      margin: 0;
    }
  </style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<nav class="main-header navbar navbar-expand navbar-dark navbar-success">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="<?= $avatar ?>" class="user-image img-circle elevation-2" alt="User Image">
                    <span class="d-none d-md-inline"><?= $email ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-success">
                        <img src="<?= $avatar ?>" class="img-circle elevation-2" alt="User Image">
                        <p><?= $email ?><small>Team Head</small></p>
                    </li>
                    <li class="user-footer">
                        <a href="../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-success elevation-4">
    <a href="../home_admin_team.php" class="brand-link navbar-success" style="text-align: center;">
         <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>
    <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image"><a href="edit_profile_adminteam.php"><img src="../<?= $avatar ?>" class="img-circle elevation-2" alt="User Image"></a></div>
                <div class="info"><a href="#" class="d-block"><?= $email ?></a></div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <li class="nav-header">MAIN NAVIGATION</li>
                    <li class="nav-item menu-open">
                        <a href="#" class="nav-link active"><i class="nav-icon fas fa-user-shield"></i><p>My Dashboard<i class="right fas fa-angle-left"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="../home_admin_team.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard (กราฟ)</p></a></li>
                            <li class="nav-item"><a href="home_admin_team_table.php" class="nav-link active"><i class="far fa-circle nav-icon"></i><p>Dashboard (ตาราง)</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Team Dashboard<i class="right fas fa-angle-left"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="team_dashboard_graph.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (กราฟ)</p></a></li>
                            <li class="nav-item"><a href="team_dashboard_table.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Dashboard ทีม (ตาราง)</p></a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link"><i class="nav-icon fas fa-edit"></i><p>เพิ่มข้อมูล<i class="fas fa-angle-left right"></i></p></a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item"><a href="add_infoteamadmin.php" class="nav-link"><i class="far fa-circle nav-icon"></i><p>เพิ่มรายละเอียดการขาย</p></a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

  <div class="content-wrapper">
    <section class="content">
      <div class="profile-box">
        <!-- Header -->
        <div class="profile-header">
          <h2>👋 Hello!</h2>
        </div>

        <!-- Avatar -->
        <div class="avatar-wrapper">
          <img id="avatarPreview" src="../<?= $avatar ?>" alt="Avatar">
          <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;">
        </div>

        <!-- Details Grid -->
        <div class="row profile-details">
          <div class="col-md-6 mb-3">
            <p class="label">Name</p>
            <p class="value"><?= $nname ?></p>
          </div>
          <div class="col-md-6 mb-3">
            <p class="label">Surname</p>
            <p class="value"><?= $surname ?></p>
          </div>
          <div class="col-md-6 mb-3">
            <p class="label">Role</p>
            <p class="value"><?= $roleName ?></p>
          </div>
          <div class="col-md-6 mb-3">
            <p class="label">Team</p>
            <p class="value"><?= $positionName ?></p>
          </div>
          <div class="col-12 mb-3">
            <p class="label">Email</p>
            <p class="value"><?= $email ?></p>
          </div>
        </div>

        <!-- Edit Button -->
        <div class="text-right">
          <button class="btn btn-primary" data-toggle="modal" data-target="#editModal">
            <i class="fa fa-pencil"></i> Edit
          </button>
          <!-- … ฝั่งบนของไฟล์ … -->

<!-- แทรก modal Edit ไว้ตรงนี้ -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form method="POST" action="update.php" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel"><i class="fa fa-pencil-alt"></i> แก้ไขโปรไฟล์</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" value="<?= $userId ?>">
          <div class="form-group text-center">
            <div class="avatar-wrapper mb-3" style="position:relative; width:100px; height:100px; margin:0 auto;">
              <img id="avatarInputPreview" src="../<?= $avatar ?>" class="rounded-circle" style="width:100px; height:100px; object-fit:cover;">
              <button type="button" class="btn btn-sm btn-danger" 
                      style="position:absolute; bottom:0; right:0; padding:4px;" 
                      id="changeAvatarBtnModal">
                <i class="fa fa-camera"></i>
              </button>
              <input type="file" name="avatar" id="avatarInputModal" accept="image/*" style="display:none;">
            </div>
          </div>
          <div class="form-group">
            <label for="nname">ชื่อ (Name)</label>
            <input type="text" class="form-control" name="nname" id="nname" value="<?= $nname ?>" required>
          </div>
          <div class="form-group">
            <label for="surname">นามสกุล (Surname)</label>
            <input type="text" class="form-control" name="surname" id="surname" value="<?= $surname ?>" required>
          </div>
          <div class="form-group">
            <label for="emailField">E-mail</label>
            <input type="email" class="form-control" id="emailField" value="<?= $email ?>" disabled>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-success">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="../plugins_v3/jquery/jquery.min.js"></script>
<script src="../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist_v3/js/adminlte.min.js"></script>
<script>
  // เปิด file input เมื่อคลิกปุ่มกล้องใน modal
  document.getElementById('changeAvatarBtnModal').addEventListener('click', function(){
    document.getElementById('avatarInputModal').click();
  });
  // แสดงพรีวิวรูปที่เลือก
  document.getElementById('avatarInputModal').addEventListener('change', function(){
    const file = this.files[0];
    if(file && file.type.startsWith('image/')){
      const reader = new FileReader();
      reader.onload = e => document.getElementById('avatarInputPreview').src = e.target.result;
      reader.readAsDataURL(file);
    }
  });
</script>
