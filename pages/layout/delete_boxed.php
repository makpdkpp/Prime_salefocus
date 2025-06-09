<?php
session_start();
include("../../functions.php");
$conn = connectDb();

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']); // แปลงให้เป็นตัวเลขเพื่อความปลอดภัย

    // ใช้ prepared statement
    $stmt = $conn->prepare("DELETE FROM product_group WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        header("Location: boxed.php"); // Redirect เมื่อสำเร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
