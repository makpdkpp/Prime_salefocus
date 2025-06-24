<?php
require_once '../functions.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_FILES['profile_image'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['profile_image'];
$userId = (int)$_SESSION['user_id'];

// ตรวจสอบประเภทไฟล์
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
if (!in_array($file['type'], $allowedTypes)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type']);
    exit;
}

// ตั้งชื่อไฟล์ใหม่ให้ไม่ซ้ำ
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFileName = 'profile_' . $userId . '_' . time() . '.' . $ext;
$uploadDir = '../uploads/';
$uploadPath = $uploadDir . $newFileName;

// สร้างโฟลเดอร์หากยังไม่มี
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ย้ายไฟล์ไปยังโฟลเดอร์ uploads
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // อัปเดต URL ลงฐานข้อมูล
    $imageUrl = $uploadPath; // หรือเก็บเฉพาะชื่อไฟล์ก็ได้
    $conn = connectDb();
    $stmt = $conn->prepare("UPDATE user SET profile_image_url = ? WHERE user_id = ?");
    $stmt->bind_param("si", $imageUrl, $userId);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true, 'url' => $imageUrl]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
}
?>
