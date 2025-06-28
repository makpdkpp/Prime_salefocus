<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Path นี้ถูกต้องแล้ว
require_once '../../functions.php';
session_start();

if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    // Path นี้ถูกต้องแล้ว
    header('Location: ../../index.php');
    exit;
}


$userId = (int)$_SESSION['user_id'];
$email = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');

$conn = connectDb();
// [แก้ไข] เปลี่ยนกลับไปใช้ "surename" ตามฐานข้อมูลของคุณ
$stmt = $conn->prepare("SELECT nname, surename, role_id, email FROM user WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$roles = [
    1 => 'Admin',
    2 => 'Sales Manager',
    3 => 'Marketing',
    4 => 'Support',
    5 => 'Developer'
];

// [แก้ไข] เปลี่ยนกลับไปใช้ "surename" และใช้ ?? '' เพื่อจัดการค่าว่าง
$nname     = htmlspecialchars($user['nname'] ?? '', ENT_QUOTES, 'UTF-8');
$surname   = htmlspecialchars($user['surename'] ?? '', ENT_QUOTES, 'UTF-8');
$roleId    = (int)($user['role_id'] ?? 0);
$userEmail = htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8');
$roleName  = $roles[$roleId] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prime Forecast | Admin Profile</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="../../plugins_v3/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../dist_v3/css/adminlte.min.css">

    <style>
        .profile-container {
            max-width: 700px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .profile-title {
            font-size: 28px;
            margin-bottom: 30px;
            color: #333;
            font-weight: bold;
        }
        .row-data {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #333;
            font-size: 18px;
        }
        .row-data div {
            color: #333;
            font-size: 18px;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background-color: #0056b3;">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link text-white" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <ul class="navbar-nav ml-auto">
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <img src="../../dist_v3/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="../../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            <p>
              <?php echo $_SESSION['email'] ?? ''; ?>
              <small>Admin</small>
            </p>
          </li>
          <li class="user-footer">
            <a href="../../logout.php" class="btn btn-default btn-flat float-right">Sign out</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="../../home_admin.php" class="brand-link" style="background-color: #0056b3; text-align: center;">
        <span class="brand-text font-weight-light"><b>Prime</b>Forecast</span>
    </a>

    <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
  <div class="image">
    <a href="page_proflie_admin.php">
      <img src="../../dist_v3/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
    </a>
  </div>
  <div class="info">
    <a href="page_proflie_admin.php" class="d-block"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></a>
    <a href="#" class="d-block" style="color: #c2c7d0; font-size: 0.9em;"><i class="fa fa-circle text-success" style="font-size: 0.7em;"></i> Online</a>
  </div>
</div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN NAVIGATION</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="../../home_admin.php" class="nav-link">
                  <i class="far fa-chart-bar nav-icon"></i>
                  <p>Dashboard (กราฟ)</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="super_admin_table.php" class="nav-link">
                  <i class="fas fa-table nav-icon"></i>
                  <p>Dashboard (ตาราง)</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item menu-is-opening menu-open">
            <a href="#" class="nav-link"> <i class="nav-icon fas fa-folder-open"></i><p>เพิ่มข้อมูล....<i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="top-nav.php" class="nav-link"><i class="fas fa-building nav-icon"></i><p>เพิ่มข้อมูลบริษัท</p></a></li>
              <li class="nav-item"><a href="boxed.php" class="nav-link"><i class="fas fa-boxes nav-icon"></i><p>เพิ่มข้อมูลกลุ่มสินค้า</p></a></li>
              <li class="nav-item"><a href="fixed.php" class="nav-link"><i class="fas fa-industry nav-icon"></i><p>เพิ่มข้อมูลอุตสาหกรรม</p></a></li>
              <li class="nav-item"><a href="Source_of_the_budget.php" class="nav-link"><i class="fas fa-file-invoice-dollar nav-icon"></i><p>เพิ่มข้อมูลที่มาของงบประมาณ</p></a></li>
              <li class="nav-item"><a href="collapsed-sidebar.php" class="nav-link"><i class="fas fa-tasks nav-icon"></i><p>ขั้นตอนการขาย</p></a></li>
              <li class="nav-item"><a href="of_winning.php" class="nav-link"><i class="fas fa-trophy nav-icon"></i><p>โอกาสการชนะ</p></a></li>
              <li class="nav-item"><a href="Saleteam.php" class="nav-link"><i class="fas fa-users nav-icon"></i><p>ทีมขาย</p></a></li>
              <li class="nav-item"><a href="position_u.php" class="nav-link"><i class="fas fa-user-tag nav-icon"></i><p>ตำแหน่ง</p></a></li>
              <li class="nav-item"><a href="Profile_user.php" class="nav-link"><i class="fas fa-id-card nav-icon"></i><p>รายละเอียดผู้ใช้งาน</p></a></li>
              <li class="nav-item"><a href="newuser.php" class="nav-link"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

    <div class="content-wrapper" role="main">

    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Admin Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../../home_admin.php">Home</a></li>
                        <li class="breadcrumb-item active">Admin Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="profile-container" role="region" aria-label="User Profile">
            <div class="profile-title">👋 Hello, Admin! <?= $nname . " " . $surname ?></div>

                <div style="position: relative; width: 160px; height: 160px; margin: 0 auto 30px;">
                    <div class="profile-image-wrapper" id="profileImageWrapperMain" style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 2px solid #ccc;">
                        <img src="../../dist_v3/img/user2-160x160.jpg" alt="User Profile Image" id="profileImageMain" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" />
                    </div>
                    <button class="edit-icon" id="editIconMain" title="เปลี่ยนรูปโปรไฟล์" aria-label="เปลี่ยนรูปโปรไฟล์" style="position: absolute; bottom: 8px; right: 8px; background: #d9534f; color: white; width: 36px; height: 36px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.3); user-select: none; cursor: pointer; font-size: 24px; line-height: 1;">
                        +
                    </button>
                </div>

                <div class="row row-data">
                    <div class="col-md-6">
                        <label class="label" for="profileName">Name</label>
                        <div id="profileName"><?= $nname ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="label" for="profileSurname">Surname</label>
                        <div id="profileSurname"><?= $surname ?></div>
                    </div>
                </div>

                <div class="row row-data">
                    <div class="col-md-6">
                        <label class="label">Role</label>
                        <div><?= $roleName ?></div>
                    </div>
                    <div class="col-md-6">
                        <label class="label">Team</label>
                        <div>PDPA</div> 
                    </div>
                </div>

                <div class="row row-data">
                    <div class="col-md-12">
                        <label class="label">Email</label>
                        <div><?= $userEmail ?></div>
                    </div>
                </div>
                
                <div class="text-right mt-4">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#editModal">
                        <i class="fa fa-pencil"></i> Edit
                    </button>
                </div>

                <input type="file" id="fileInputMain" accept="image/*" style="display:none;" aria-label="เลือกไฟล์รูปโปรไฟล์" />
            </div>
        </section>
    </div>
    
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <form method="POST" action="../../update.php"> 
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="editModalLabel">Edit Profile</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                        <div class="form-group">
                            <label for="nname">Name</label>
                            <input type="text" class="form-control" name="nname" value="<?= $nname ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" name="surname" value="<?= $surname ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="../../plugins_v3/jquery/jquery.min.js"></script>
<script src="../../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>

<script>
    const editIcon = document.getElementById('editIconMain');
    const fileInput = document.getElementById('fileInputMain');
    const profileImage = document.getElementById('profileImageMain');

    editIcon.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            if (!file.type.startsWith('image/')) {
                alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น');
                fileInput.value = '';
                return;
            }
            alert('คุณได้เลือกไฟล์รูปภาพเพื่อเปลี่ยนโปรไฟล์: ' + file.name);
            const reader = new FileReader();
            reader.onload = function(e) {
                profileImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
            // TODO: อัพโหลดไฟล์ขึ้น server ที่นี่ (ใช้ fetch/AJAX ส่งไปยัง PHP backend)
        }
    });
</script>

</body>
</html>