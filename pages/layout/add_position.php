<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $position = trim($_POST['position'] ?? '');

    // ตรวจสอบว่าเป็นการแก้ไขหรือเพิ่ม
    $isEdit = isset($_POST['action']) && $_POST['action'] === 'edit';
    $position_id = isset($_POST['position_id']) && is_numeric($_POST['position_id']) ? (int)$_POST['position_id'] : null;

    // แก้ไขข้อมูล
    if ($isEdit && $position_id !== null) {
        $stmt = $mysqli->prepare("UPDATE position SET position = ? WHERE position_id = ?");
        $stmt->bind_param("si", $position, $position_id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='position_u.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    // เพิ่มข้อมูลใหม่
    } elseif (!$isEdit && $position !== '') {
        $stmt = $mysqli->prepare("INSERT INTO position (position) VALUES (?)");
        $stmt->bind_param("s", $position);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='position_u.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน หรือมีบางอย่างผิดพลาด'); window.history.back();</script>";
    }
}
?>
