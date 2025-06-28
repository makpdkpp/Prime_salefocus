<?php
session_start();
require_once '../../functions.php';

if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli = connectDb();

    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
    $position_id = isset($_POST['position_id']) ? (int)$_POST['position_id'] : 0;
    $team_ids = isset($_POST['team_id']) ? $_POST['team_id'] : [];
    if (!is_array($team_ids)) $team_ids = [$team_ids];

    if ($user_id > 0 && $role_id > 0 && $position_id > 0 && count($team_ids) > 0) {
        // Update user role and position
        $stmt = $mysqli->prepare('UPDATE user SET role_id=?, position_id=? WHERE user_id=?');
        if (!$stmt) {
            die('Prepare failed (user update): ' . $mysqli->error);
        }
        $stmt->bind_param('iii', $role_id, $position_id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Remove old teams
        $stmt_del = $mysqli->prepare('DELETE FROM transactional_team WHERE user_id=?');
        if (!$stmt_del) {
            die('Prepare failed (delete teams): ' . $mysqli->error);
        }
        $stmt_del->bind_param('i', $user_id);
        $stmt_del->execute();
        $stmt_del->close();

        // Insert new teams
        $stmt_ins = $mysqli->prepare('INSERT INTO transactional_team (team_id, user_id) VALUES (?, ?)');
        if (!$stmt_ins) {
            die('Prepare failed (insert teams): ' . $mysqli->error);
        }
        foreach ($team_ids as $team_id) {
            $team_id = (int)$team_id;
            $stmt_ins->bind_param('ii', $team_id, $user_id);
            $stmt_ins->execute();
        }
        $stmt_ins->close();

        $_SESSION['message'] = 'อัปเดตข้อมูลผู้ใช้สำเร็จ';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
        $_SESSION['message_type'] = 'danger';
    }

    $mysqli->close();
}

header('Location: newuser.php');
exit;
?>