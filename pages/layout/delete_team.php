<?php
session_start();
require_once '../../functions.php';
$conn = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['team_id']) && is_numeric($_GET['team_id'])) {
    $team_id = (int)$_GET['team_id'];

    // เช็คว่ามีการใช้งานอยู่ใน transactional หรือไม่
    $check = $conn->prepare("SELECT COUNT(*) FROM transactional WHERE team_id = ?");
    $check->bind_param("i", $team_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>alert('ไม่สามารถลบได้ เนื่องจากมีการใช้งาน priority นี้อยู่ในข้อมูลการขาย'); window.location.href='Saleteam.php';</script>";
        exit();
    }

    // ถ้าไม่มีการใช้งาน ให้ลบได้
    $stmt = $conn->prepare("DELETE FROM team_catalog WHERE team_id = ?");
    $stmt->bind_param("i", $team_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location.href='Saleteam.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('รหัสไม่ถูกต้อง หรือไม่มีข้อมูล'); window.history.back();</script>";
}

$conn->close();
?>
