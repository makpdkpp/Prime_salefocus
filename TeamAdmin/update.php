<?php

require_once '../functions.php';
session_start();
$conn = connectDb();
// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['user_id'])) {
    header('Location: edit_profile_adminteam.php');
    exit;
}

$userId  = (int)$_POST['user_id'];
$nname   = trim($_POST['nname']);
$surname = trim($_POST['surname']);

$conn->begin_transaction();

try {
    // อัปเดตชื่อ-สกุล
    $stmt = $conn->prepare("UPDATE user SET nname = ?, surename = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $nname, $surname, $userId);
    $stmt->execute();
    $stmt->close();

    // ตรวจสอบ avatar
    if (!empty($_FILES['avatar']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/avatars/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $fileName = "user_{$userId}_" . time() . ".$ext";
        $dest = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
            $webPath = "uploads/avatars/$fileName";
            $stmt = $conn->prepare("UPDATE user SET avatar_path = ? WHERE user_id = ?");
            $stmt->bind_param("si", $webPath, $userId);
            $stmt->execute();
            $stmt->close();
            // Set session avatar for all pages
            $_SESSION['avatar'] = $webPath;
        } else {
            throw new Exception("ไม่สามารถอัปโหลดรูปภาพได้");
        }
    }

    $conn->commit();
    $_SESSION['flash'] = "บันทึกสำเร็จ";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['flash'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
}

header('Location: edit_profile_adminteam.php');
exit;
