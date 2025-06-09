<?php
session_start();
include("../../functions.php");
$conn = connectDb();

if (isset($_GET['priority_id'])) {
    $priority_id = intval($_GET['priority_id']); // แปลงให้เป็นตัวเลขเพื่อความปลอดภัย

    // ใช้ prepared statement
    $stmt = $conn->prepare("DELETE FROM priority_level WHERE priority_id = ?");
    $stmt->bind_param("i", $priority_id);

    if ($stmt->execute()) {
        header("Location: of_winning.php"); // Redirect เมื่อสำเร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
