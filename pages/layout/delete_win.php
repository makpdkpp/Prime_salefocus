<?php
session_start();
require_once '../../functions.php';
$conn = connectDb();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['priority_id']) && is_numeric($_GET['priority_id'])) {
    $priority_id = (int)$_GET['priority_id'];

    // เช็คว่ามีการใช้งานอยู่ใน transactional หรือไม่
    $check = $conn->prepare("SELECT COUNT(*) FROM transactional WHERE priority_id = ?");
    $check->bind_param("i", $priority_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>alert('ไม่สามารถลบได้ เนื่องจากมีการใช้งาน priority นี้อยู่ในข้อมูลการขาย'); window.location.href='of_winning.php';</script>";
        exit();
    }

    // ถ้าไม่มีการใช้งาน ให้ลบได้
    $stmt = $conn->prepare("DELETE FROM priority_level WHERE priority_id = ?");
    $stmt->bind_param("i", $priority_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location.href='of_winning.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('รหัสไม่ถูกต้อง หรือไม่มีข้อมูล'); window.history.back();</script>";
}

$conn->close();
?>
