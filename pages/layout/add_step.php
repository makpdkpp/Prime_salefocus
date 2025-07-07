<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $level = trim($_POST['level'] ?? '');

    // แก้ไขข้อมูล
    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'edit' &&
        !empty($_POST['level_id']) &&
        is_numeric($_POST['level_id'])
    ) {
        $level_id = (int)$_POST['level_id'];

        $stmt = $mysqli->prepare("UPDATE step SET level = ? WHERE level_id = ?");
        $stmt->bind_param("si", $level, $level_id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='collapsed-sidebar.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    // เพิ่มข้อมูลใหม่
    } elseif ($level !== '') {
        $stmt = $mysqli->prepare("INSERT INTO step (level) VALUES (?)");
        $stmt->bind_param("s", $level);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='collapsed-sidebar.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
    }
}
?>
