<?php
session_start();
require_once '../../functions.php';
$conn = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];

    // เช็คว่ามีการใช้งานอยู่ใน transactional หรือไม่
    $check = $conn->prepare("SELECT COUNT(*) FROM transactional WHERE product_id = ?");
    $check->bind_param("i", $product_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>alert('ไม่สามารถลบได้ เนื่องจากมีการใช้งาน priority นี้อยู่ในข้อมูลการขาย'); window.location.href='boxed.php';</script>";
        exit();
    }

    // ถ้าไม่มีการใช้งาน ให้ลบได้
    $stmt = $conn->prepare("DELETE FROM product_group WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location.href='boxed.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('รหัสไม่ถูกต้อง หรือไม่มีข้อมูล'); window.history.back();</script>";
}

$conn->close();
?>
