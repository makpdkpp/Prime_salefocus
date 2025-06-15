<?php
session_start();
include("../../functions.php");

$conn = connectDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $Product = trim($_POST['Product'] ?? '');

    // ตรวจสอบว่าต้องการ "แก้ไข"
    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'edit' &&
        !empty($_POST['product_id']) &&
        is_numeric($_POST['product_id'])
    ) {
        $id = (int)$_POST['product_id'];

        $stmt = $conn->prepare("UPDATE product_group SET Product = ? WHERE product_id = ?");
        $stmt->bind_param("si", $Product, $id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='boxed.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    // ถ้าไม่ใช่การแก้ไข → ให้เป็นการเพิ่มใหม่
    } elseif ($Product !== '') {
        $stmt = $conn->prepare("INSERT INTO product_group (Product) VALUES (?)");
        $stmt->bind_param("s", $Product);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='boxed.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
    }
}

$conn->close();
?>
