<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

if (isset($_GET['position_id']) && is_numeric($_GET['position_id'])) {
    $position_id = (int)$_GET['position_id'];

    $stmt = $mysqli->prepare("DELETE FROM position WHERE position_id = ?");
    $stmt->bind_param("i", $position_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลเรียบร้อยแล้ว'); window.location.href='position_u.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('รหัสตำแหน่งไม่ถูกต้อง'); window.history.back();</script>";
}
?>
