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
    $team_id = isset($_POST['team_id']) ? (int)$_POST['team_id'] : 0;

    if ($user_id > 0 && $role_id > 0 && $position_id > 0 && $team_id > 0) {
        $stmt = $mysqli->prepare("UPDATE user SET role_id = ?, position_id = ?, team_id = ? WHERE user_id = ?");
        $stmt->bind_param("iiii", $role_id, $position_id, $team_id, $user_id);

        if ($stmt->execute()) {
            $_SESSION['message'] = 'อัปเดตข้อมูลผู้ใช้สำเร็จ';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $stmt->error;
            $_SESSION['message_type'] = 'danger';
        }
        $stmt->close();
    } else {
        $_SESSION['message'] = 'ข้อมูลไม่ถูกต้อง';
        $_SESSION['message_type'] = 'danger';
    }

    $mysqli->close();
}

header('Location: newuser.php');
exit;
?>