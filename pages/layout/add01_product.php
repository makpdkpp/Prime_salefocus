<?php
session_start();
include("../../connect.php");
include("../../functions.php");

$mysqli = connectDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $industry = trim($_POST['Industry'] ?? '');

    // ตรวจสอบว่าต้องการ "แก้ไข"
    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'edit' &&
        !empty($_POST['Industry_id']) &&
        is_numeric($_POST['Industry_id'])
    ) {
        $industry_id = (int)$_POST['Industry_id'];

        $stmt = $mysqli->prepare("UPDATE Industry_group SET Industry = ? WHERE Industry_id = ?");
        $stmt->bind_param("si", $industry, $industry_id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='fixed.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    // ถ้าไม่ใช่การแก้ไข → ให้เป็นการเพิ่มใหม่
    } elseif ($industry !== '') {
        $stmt = $mysqli->prepare("INSERT INTO Industry_group (Industry) VALUES (?)");
        $stmt->bind_param("s", $industry);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='fixed.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
    }
}
?>
