<?php
session_start();
include("../../functions.php");
$conn = connectDb();

if (isset($_GET['company_id'])) {
    $company_id = intval($_GET['company_id']); // แปลงให้เป็นตัวเลขเพื่อความปลอดภัย

    // ใช้ prepared statement
    $stmt = $conn->prepare("DELETE FROM company_catalog WHERE company_id = ?");
    $stmt->bind_param("i", $company_id);

    if ($stmt->execute()) {
        header("Location: top-nav.php"); // Redirect เมื่อสำเร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
