<?php
session_start();
include("../../functions.php");
$conn = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // แปลงให้เป็นตัวเลขเพื่อความปลอดภัย

    // ใช้ prepared statement
    $stmt = $conn->prepare("DELETE FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header("Location: Profile_user.php"); // Redirect เมื่อสำเร็จ
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
