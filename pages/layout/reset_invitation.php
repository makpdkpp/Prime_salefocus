<?php
// เปิด Error reporting เพื่อช่วยดีบัก
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
// ตรวจสอบสิทธิ์ Admin (สำคัญมาก!)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) { // สมมติว่า role_id 1 คือ Superadmin
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

// โหลดไฟล์ที่จำเป็น
require_once '../../functions.php'; // Path อาจต้องปรับตามโครงสร้างโฟลเดอร์ของคุณ
require_once '../../lib/PHPMailer/src/PHPMailer.php';
require_once '../../lib/PHPMailer/src/SMTP.php';
require_once '../../lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');
$db = connectDb();

// รับข้อมูลที่ส่งมาแบบ JSON
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบ User ID']);
    exit();
}

// สร้าง Token และวันหมดอายุใหม่ (3 วัน)
$newToken = bin2hex(random_bytes(32));
$expiryDate = new DateTime();
$expiryDate->add(new DateInterval('P3D')); // เพิ่มไป 3 วัน
$formattedExpiry = $expiryDate->format('Y-m-d H:i:s');

// 1. อัปเดต Token และวันหมดอายุในฐานข้อมูล
$stmt = $db->prepare("UPDATE user SET reset_token = ?, token_expiry = ? WHERE user_id = ?");
if (!$stmt) {
     echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $db->error]);
     exit();
}
$stmt->bind_param("ssi", $newToken, $formattedExpiry, $userId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // 2. ดึงอีเมลของผู้ใช้เพื่อส่งคำเชิญ
    $stmt_email = $db->prepare("SELECT email FROM user WHERE user_id = ?");
    $stmt_email->bind_param("i", $userId);
    $stmt_email->execute();
    $result = $stmt_email->get_result();
    $user = $result->fetch_assoc();
    $userEmail = $user['email'];
    $stmt_email->close();

    // 3. ส่งอีเมลด้วย PHPMailer
    $link = 'http://' . $_SERVER['HTTP_HOST'] . '/Prime_saleficus/pages/layout/set-password.php?token=' . $newToken;
    $subject = 'Re: User Invitation to PrimeForecast';
    $body = "Your invitation has been renewed. Please click on the link to set your password: $link";
    
    try {
        $mail = new PHPMailer(true);
        // ตั้งค่า SMTP เหมือนในไฟล์ newuser.php
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'immsendermail@gmail.com';
        $mail->Password = 'npou efln pgpf bhxd';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@primeforecast.com', 'PrimeForecast Admin');
        $mail->addAddress($userEmail);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถอัปเดตข้อมูลผู้ใช้ได้ หรือไม่พบผู้ใช้']);
}

$stmt->close();
$db->close();
?>