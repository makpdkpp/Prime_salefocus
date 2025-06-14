<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $priority = trim($_POST['priority'] ?? '');

    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'edit' &&
        !empty($_POST['priority_id']) &&
        is_numeric($_POST['priority_id'])
    ) {
        $priority_id = (int)$_POST['priority_id'];

        $stmt = $mysqli->prepare("UPDATE priority_level SET priority = ? WHERE priority_id = ?");
        $stmt->bind_param("si", $priority, $priority_id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='of_winning.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    } elseif ($priority !== '') {
        $stmt = $mysqli->prepare("INSERT INTO priority_level (priority) VALUES (?)");
        $stmt->bind_param("s", $priority);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='of_winning.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
    }
}
?>
