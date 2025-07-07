<?php
session_start();
require_once '../../functions.php';
$conn = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['level_id']) && is_numeric($_GET['level_id'])) {
    $level_id = (int)$_GET['level_id'];


    // ถ้าไม่มีการใช้งาน ให้ลบได้
    $stmt = $conn->prepare("DELETE FROM step WHERE level_id = ?");
    $stmt->bind_param("i", $level_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location.href='collapsed-sidebar.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('รหัสไม่ถูกต้อง หรือไม่มีข้อมูล'); window.history.back();</script>";
}

$conn->close();
?>
