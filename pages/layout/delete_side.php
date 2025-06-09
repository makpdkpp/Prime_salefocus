<?php
session_start();
include("../../functions.php");
$conn = connectDb();

if (isset($_GET['level_id'])) {
    $level_id = intval($_GET['level_id']); // แปลงให้เป็นตัวเลขเพื่อความปลอดภัย

    // เปลี่ยนชื่อตารางจาก level_id เป็นชื่อที่ถูกต้อง เช่น step
    $stmt = $conn->prepare("DELETE FROM step WHERE level_id = ?");
    $stmt->bind_param("i", $level_id);

    if ($stmt->execute()) {
        header("Location: collapsed-sidebar.php"); // Redirect เมื่อสำเร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
