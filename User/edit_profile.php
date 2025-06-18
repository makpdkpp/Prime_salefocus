<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../functions.php';
session_start();

// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$email = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');

$conn = connectDb();
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

$nname     = htmlspecialchars($user['nname'], ENT_QUOTES, 'UTF-8');
$surname   = htmlspecialchars($user['surename'], ENT_QUOTES, 'UTF-8');
$roleId    = (int)$user['role_id'];
$userEmail = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
$roleName  = $roles[$roleId] ?? 'Unknown';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8" />
    <title>Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- CSS Dependencies -->
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css" />
    <link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />

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

<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

    <!-- Header -->
    <header class="main-header">
        <a href="../home_user.php" class="logo"><b>Prime</b>Focus</a>
        <nav class="navbar navbar-static-top" role="navigation">
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="../dist/img/user2-160x160.jpg" class="user-image" alt="User Image" />
                            <span class="hidden-xs"><?= $email ?></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li class="user-header">
                                <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
                                <p><?= $email ?> <small>User</small></p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-right">
                                    <a href="../logout.php" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Sidebar -->
    <aside class="main-sidebar" role="navigation" aria-label="Sidebar navigation">
        <section class="sidebar">
            <div class="user-panel">
                <div class="pull-left image">
                    <a href="User/edit_profile.php" aria-label="Edit Profile">
                        <img src="../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image" style="width: 45px; height: 45px;" />
                    </a>
                </div>
                <div class="pull-left info">
                    <p><?= $email ?> (User)</p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu" data-widget="tree" role="menu" aria-label="Main menu">
                <li class="header">MAIN NAVIGATION</li>
                <li class="active treeview" aria-expanded="true">
                    <a href="#" aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu" role="menu">
                        <li class="active"><a href="../home_user.php"><i class="fa fa-circle-o"></i> Dashboard (กราฟ)</a></li>
                        <li><a href="../home_user_01.php"><i class="fa fa-circle-o"></i> Dashboard (ตาราง)</a></li>
                    </ul>
                </li>

                <li class="treeview" aria-expanded="false">
                    <a href="#" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล</span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu" role="menu">
                        <li><a href="../User/adduser01.php"><i class="fa fa-circle-o"></i> เพิ่มรายละเอียดการขาย</a></li>
                    </ul>
                </li>
            </ul>
        </section>
    </aside>

    <!-- Content -->
    <div class="content-wrapper" role="main">
        <section class="content">
            <div class="profile-container" role="region" aria-label="User Profile">
                <div class="profile-title">👋 Hello! <?= $nname . " " . $surname ?></div>

                <!-- รูปโปรไฟล์และปุ่มแก้ไขรูป -->
                <div style="position: relative; width: 160px; height: 160px; margin: 0 auto 30px;">
                    <div class="profile-image-wrapper" id="profileImageWrapperMain"
                         style="width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 2px solid #ccc;">
                        <img src="../dist/img/user2-160x160.jpg" alt="User Profile Image" id="profileImageMain"
                             style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" />
                    </div>
                    <button class="edit-icon" id="editIconMain" title="เปลี่ยนรูปโปรไฟล์" aria-label="เปลี่ยนรูปโปรไฟล์"
                         style="position: absolute; bottom: 8px; right: 8px; background: #d9534f; color: white; width: 36px; height: 36px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.3); user-select: none; cursor: pointer; font-size: 24px; line-height: 1;">
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

            

                <!-- input สำหรับเลือกไฟล์รูปโปรไฟล์ซ่อน -->
                <input type="file" id="fileInputMain" accept="image/*" style="display:none;" aria-label="เลือกไฟล์รูปโปรไฟล์" />
            </div>
        </section>
    </div><!-- /.content-wrapper -->

<

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>
<script src="../dist/js/app.min.js"></script>

<script>
    // เปลี่ยนรูปโปรไฟล์
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
