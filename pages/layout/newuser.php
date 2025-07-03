<?php
// เพิ่มโค้ดแสดง Error เพื่อช่วยในการดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../../functions.php';
require_once '../../lib/PHPMailer/src/PHPMailer.php';
require_once '../../lib/PHPMailer/src/SMTP.php';
require_once '../../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = connectDb();
$message = '';
$message_type = ''; 

$avatar = htmlspecialchars($_SESSION['avatar'] ?? '../../dist/img/user2-160x160.jpg', ENT_QUOTES, 'UTF-8');

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_email'])) {
    $email = trim($_POST['invite_email']);
    $role_id = isset($_POST['invite_role']) ? (int)$_POST['invite_role'] : 0;
    $position_id = isset($_POST['invite_position']) ? (int)$_POST['invite_position'] : 0;
    $team_ids = isset($_POST['invite_team']) ? $_POST['invite_team'] : [];
    if (!is_array($team_ids)) $team_ids = [$team_ids];

    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $role_id > 0 && $position_id > 0 && count($team_ids) > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 day'));
        
        $stmt_check = $db->prepare('SELECT user_id FROM user WHERE email=?');
        $stmt_check->bind_param('s', $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($user = $result->fetch_assoc()) {
            $uid = $user['user_id'];
            $stmt_check->close();
            
            $stmt_update = $db->prepare('UPDATE user SET role_id=?, position_id=?, reset_token=?, token_expiry=?, is_active=0 WHERE user_id=?');
            $stmt_update->bind_param('iissi', $role_id, $position_id, $token, $expiry, $uid);
            $stmt_update->execute();
            $stmt_update->close();

            // ลบทีมเดิม แล้วเพิ่มทีมใหม่
            $db->query('DELETE FROM transactional_team WHERE user_id=' . (int)$uid);
            $stmt_trans = $db->prepare('INSERT INTO transactional_team (team_id, user_id) VALUES (?, ?)');
            foreach ($team_ids as $team_id) {
                $team_id = (int)$team_id;
                $stmt_trans->bind_param('ii', $team_id, $uid);
                $stmt_trans->execute();
            }
            $stmt_trans->close();
        } else {
            $stmt_check->close();
            $temporary_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
            $default_surename = '';
            $default_forecast = 0;
            // First insert the user (team_id removed)
            $stmt_insert = $db->prepare('INSERT INTO user (email, nname, surename, role_id, position_id, forecast, reset_token, token_expiry, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            if (!$stmt_insert) {
                die('Prepare failed: ' . $db->error);
            }
            $stmt_insert->bind_param('sssiiisss', $email, $email, $default_surename, $role_id, $position_id, $default_forecast, $token, $expiry, $temporary_password);
            $stmt_insert->execute();
            $new_user_id = $db->insert_id;
            $stmt_insert->close();
            // Insert all selected teams
            if ($new_user_id) {
                $stmt_trans = $db->prepare('INSERT INTO transactional_team (team_id, user_id) VALUES (?, ?)');
                foreach ($team_ids as $team_id) {
                    $team_id = (int)$team_id;
                    $stmt_trans->bind_param('ii', $team_id, $new_user_id);
                    $stmt_trans->execute();
                }
                $stmt_trans->close();
            }
        }
        if (empty($message)) {
            $link = 'http://' . $_SERVER['HTTP_HOST'] . '/Prime_saleficus/pages/layout/set-password.php?token=' . $token;
            $subject = 'User Invitation to PrimeForecast';
            $body = "Please click on the link to set your password: $link";
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'immsendermail@gmail.com';
                $mail->Password = 'npou efln pgpf bhxd';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('no-reply@primeforecast.com', 'PrimeForecast Admin');
                $mail->addAddress($email);
                $mail->Subject = $subject;
                $mail->Body = $body;
                $mail->send();
                $message = 'Invitation sent successfully to ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                $message_type = 'success';
            } catch (Exception $e) {
                $message = 'Mailer Error: ' . $mail->ErrorInfo;
                $message_type = 'danger';
            }
        }
    } else {
        $message = 'Please provide a valid email, role, position, and team.';
        $message_type = 'danger';
    }
    // POST-REDIRECT-GET: เก็บ message ใน session แล้ว redirect
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $message_type;
    header('Location: newuser.php');
    exit;
}

$positions_query = $db->query("SELECT position_id, position FROM position ORDER BY position ASC");
$positions = $positions_query ? $positions_query->fetch_all(MYSQLI_ASSOC) : [];

$teams_query = $db->query("SELECT team_id, team FROM team_catalog ORDER BY team ASC");
$teams = $teams_query ? $teams_query->fetch_all(MYSQLI_ASSOC) : [];

$roles_query = $db->query("SELECT role_id, role FROM role_catalog ORDER BY role_id ASC");
$roles = $roles_query ? $roles_query->fetch_all(MYSQLI_ASSOC) : [];

$user_query = $db->query("
    SELECT u.user_id, u.email, u.is_active, u.role_id, u.position_id, p.position,
           GROUP_CONCAT(tc.team SEPARATOR ', ') AS team,
           GROUP_CONCAT(tt.team_id) AS team_ids
    FROM user u
    LEFT JOIN position p ON u.position_id = p.position_id
    LEFT JOIN transactional_team tt ON u.user_id = tt.user_id
    LEFT JOIN team_catalog tc ON tt.team_id = tc.team_id
    GROUP BY u.user_id
    ORDER BY u.user_id DESC
");
$userRows = $user_query ? $user_query->fetch_all(MYSQLI_ASSOC) : [];

$db->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เพิ่มผู้ใช้งาน | PrimeForecast</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="../../plugins_v3/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../../dist_v3/css/adminlte.min.css">
  <style>
    .content-wrapper { background-color: #b3d6e4; }
    .container1 { max-width: 1100px; margin: 20px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); }
    .btn-add { position: fixed; bottom: 30px; right: 30px; background: #0056b3; color: #fff; border-radius: 50%; width: 56px; height: 56px; font-size: 24px; border: none; z-index: 1040; }
    .modal-content { border-radius: 10px; padding: 20px; }
    .table thead { background: #0056b3; color: white; }
    .sidebar {padding-bottom: 30px; }
    body.sidebar-mini .main-sidebar .user-panel .image img,
    body:not(.sidebar-mini) .main-sidebar .user-panel .image img {
      width: 40px;
      height: 40px;
      object-fit: cover;
    }

    /* ▼▼▼ เพิ่ม CSS สำหรับโทรศัพท์ ▼▼▼ */
    .table-responsive-container {
      overflow-x: auto; /* ทำให้สามารถ scroll แนวนอนได้ */
      -webkit-overflow-scrolling: touch; /* ทำให้การ scroll ลื่นขึ้นบน iOS */
    }
    /* ▲▲▲ จบส่วนที่เพิ่ม ▲▲▲ */
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
          <img src="../../<?= $avatar ?>" class="user-image img-circle elevation-2" alt="User Image">
          <span class="d-none d-md-inline text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header" style="background-color: #0056b3; color: #fff;">
            <img src="../../<?= $avatar ?>" class="img-circle elevation-2" alt="User Image">
            <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
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
          <a href="adminedit_profile.php"> <img src="../../<?= $avatar ?>" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;"></a>
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></a>
          <a href="#" class="d-block" style="color: #c2c7d0; font-size: 0.9em;"><i class="fa fa-circle text-success" style="font-size: 0.7em;"></i> Online</a>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-header">MAIN NAVIGATION</li>
          <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard<i class="right fas fa-angle-left"></i></p></a><ul class="nav nav-treeview"><li class="nav-item"><a href="../../home_admin.php" class="nav-link"><i class="far fa-chart-bar nav-icon"></i><p>Dashboard (กราฟ)</p></a></li><li class="nav-item"><a href="super_admin_table.php" class="nav-link"><i class="fas fa-table nav-icon"></i><p>Dashboard (ตาราง)</p></a></li></ul></li>
          <li class="nav-item menu-is-opening menu-open"><a href="#" class="nav-link active"><i class="nav-icon fas fa-folder-open"></i><p>เพิ่มข้อมูล....<i class="right fas fa-angle-left"></i></p></a>
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
              <li class="nav-item"><a href="newuser.php" class="nav-link active"><i class="fas fa-user-plus nav-icon"></i><p>เพิ่มผู้ใช้งาน</p></a></li>
            </ul>
          </li>
        </ul>
      </nav>
      </div>
    </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <div class="container-fluid"><div class="row mb-2"><div class="col-sm-6"><h1></h1></div><div class="col-sm-6"><ol class="breadcrumb float-sm-right"><li class="breadcrumb-item"><a href="../../home_admin.php">หน้าหลัก</a></li><li class="breadcrumb-item active">เพิ่มผู้ใช้งาน</li></ol></div></div></div>
    </section>

    <section class="content">
      <div class="container1">
        <h3>รายชื่อผู้ใช้งานในระบบ</h3>
        <?php if(!empty($message)): ?>
          <div class="alert alert-<?= $message_type ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
        <?php endif; ?>
        
        <div class="table-responsive-container">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>Email</th>
                <th>สิทธิ์ผู้ใช้งาน</th>
                <th>ตำแหน่ง</th>
                <th>ทีมขาย</th>
                <th style="width: 150px;">Status</th>
                <th style="width: 100px;">แก้ไข</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($userRows as $u): ?>
              <tr>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <?php
                    switch ($u['role_id']) {
                      case 1: echo '<span class="badge badge-danger">Superadmin</span>'; break;
                      case 2: echo '<span class="badge badge-info">AdminTeam</span>'; break;
                      case 3: echo '<span class="badge badge-success">Sale</span>'; break;
                      default: echo '<span class="badge badge-secondary">N/A</span>';
                    }
                  ?>
                </td>
                <td><?= htmlspecialchars($u['position'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($u['team'] ?? 'N/A') ?></td>
                <td>
                  <?php if ($u['is_active']): ?>
                    <span class="badge badge-success">Active</span>
                  <?php else: ?>
                    <span class="badge badge-warning">Pending Invitation</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning btn-edit" 
                          data-toggle="modal" 
                          data-target="#editUserModal"
                          data-user-id="<?= $u['user_id'] ?>"
                          data-role-id="<?= $u['role_id'] ?>"
                          data-position-id="<?= $u['position_id'] ?>"
                          data-team-ids="<?= htmlspecialchars($u['team_ids']) ?>">
                    <i class="fas fa-edit"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($userRows)): ?>
                <tr><td colspan="6" class="text-center">-- ไม่มีผู้ใช้งานในระบบ --</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        </div>
    </section>
  </div>

  <button class="btn-add" data-toggle="modal" data-target="#inviteModal" title="Invite New User"><i class="fas fa-user-plus"></i></button>
  <div class="modal fade" id="inviteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header"><h5 class="modal-title">Invite User by Email</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">
            <div class="form-group"><label for="invite_email">Email Address</label><input type="email" id="invite_email" name="invite_email" class="form-control" placeholder="Enter email" required></div>
            <div class="form-group">
              <label for="invite_role">Role</label>
              <select id="invite_role" name="invite_role" class="form-control" required>
                <option value="" disabled selected>-- Select Role --</option>
                <?php foreach ($roles as $role): ?>
                  <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label for="invite_position">Position</label><select id="invite_position" name="invite_position" class="form-control" required><option value="" disabled selected>-- Select Position --</option><?php foreach ($positions as $pos): ?><option value="<?= $pos['position_id'] ?>"><?= htmlspecialchars($pos['position']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label for="invite_team">Sales Team</label>
            <div id="invite_team_group">
              <?php foreach ($teams as $team): ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="invite_team[]" id="invite_team_<?= $team['team_id'] ?>" value="<?= $team['team_id'] ?>">
                  <label class="form-check-label" for="invite_team_<?= $team['team_id'] ?>"><?= htmlspecialchars($team['team']) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Send Invitation</button></div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form action="update_user_role.php" method="POST">
          <div class="modal-header"><h5 class="modal-title">แก้ไขสิทธิ์และตำแหน่ง</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
          <div class="modal-body">
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="form-group">
              <label for="edit_role">Role</label>
              <select id="edit_role" name="role_id" class="form-control" required>
                <?php foreach ($roles as $role): ?>
                  <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label for="edit_position">Position</label><select id="edit_position" name="position_id" class="form-control" required><option value="" disabled>-- Select Position --</option><?php foreach ($positions as $pos): ?><option value="<?= $pos['position_id'] ?>"><?= htmlspecialchars($pos['position']) ?></option><?php endforeach; ?></select></div>
            <div class="form-group"><label for="edit_team">Sales Team</label>
              <div id="edit_team_group">
                <?php foreach ($teams as $team): ?>
                  <div class="form-check">
                    <input class="form-check-input edit-team-checkbox" type="checkbox" name="team_id[]" id="edit_team_<?= $team['team_id'] ?>" value="<?= $team['team_id'] ?>">
                    <label class="form-check-label" for="edit_team_<?= $team['team_id'] ?>"><?= htmlspecialchars($team['team']) ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Changes</button></div>
        </form>
      </div>
    </div>
  </div>

</div>
<script src="../../plugins_v3/jquery/jquery.min.js"></script>
<script src="../../plugins_v3/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../dist_v3/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // --- ส่วนสำหรับ Invite Modal ---
    const inviteRoleSelect = $('#invite_role');
    const invitePositionGroup = $('#invite_position').closest('.form-group');
    const inviteTeamGroup = $('#invite_team_group').closest('.form-group');
    function toggleInviteFields() {
        const selectedRole = inviteRoleSelect.val();
        if (selectedRole === '2' || selectedRole === '3') {
            invitePositionGroup.show();
            inviteTeamGroup.show();
        } else {
            invitePositionGroup.hide();
            inviteTeamGroup.hide();
        }
    }
    toggleInviteFields(); 
    inviteRoleSelect.on('change', toggleInviteFields);

    // --- ส่วนสำหรับ Edit Modal ---
    const editRoleSelect = $('#edit_role');
    const editPositionGroup = $('#edit_position').closest('.form-group');
    const editTeamGroup = $('#edit_team_group').closest('.form-group');
    function toggleEditFields() {
        const selectedRole = editRoleSelect.val();
        if (selectedRole === '2' || selectedRole === '3') {
            editPositionGroup.show();
            editTeamGroup.show();
        } else {
            editPositionGroup.hide();
            editTeamGroup.hide();
        }
    }
    editRoleSelect.on('change', toggleEditFields);

    $('.btn-edit').on('click', function() {
        const userId = $(this).data('user-id');
        const roleId = $(this).data('role-id');
        const positionId = $(this).data('position-id');
        let teamIds = $(this).data('team-ids');

        $('#edit_user_id').val(userId);
        $('#edit_role').val(roleId);
        $('#edit_position').val(positionId);
        $('.edit-team-checkbox').prop('checked', false);
        if (teamIds && teamIds !== 'null' && teamIds !== null && teamIds !== undefined) {
          let ids = String(teamIds).split(',').map(id => id.trim()).filter(id => id !== '' && id !== 'null');
          ids.forEach(function(id) {
            if (id) $('#edit_team_' + id).prop('checked', true);
          });
        }
        toggleEditFields(); 
    });
});
</script>

</body>
</html>