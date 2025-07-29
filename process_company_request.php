<?php
// process_company_request.php

// --- ส่วนเรียกใช้งาน PHPMailer แบบ Manual ---
session_start();
require_once 'functions.php'; // ตรวจสอบว่า path ไปยัง functions.php ถูกต้อง

// แก้ไข path ไปยังโฟลเดอร์ lib ให้ถูกต้องตามโครงสร้างโปรเจกต์ของคุณ
require_once 'lib/PHPMailer/src/Exception.php';
require_once 'lib/PHPMailer/src/PHPMailer.php';
require_once 'lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// --- สิ้นสุดส่วนเรียกใช้งาน ---

header('Content-Type: application/json');

// 1. ตรวจสอบสิทธิ์
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 3) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้รับอนุญาต']);
    exit;
}

// 2. รับและตรวจสอบข้อมูล
$companyName = trim($_POST['company_name'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$userId = (int)$_SESSION['user_id'];
$userEmail = htmlspecialchars($_SESSION['email']);

if (empty($companyName)) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อบริษัท']);
    exit;
}

$mysqli = connectDb();

// 3. บันทึกข้อมูลลง DB
$stmt = $mysqli->prepare("INSERT INTO company_requests (company_name, notes, requested_by_user_id) VALUES (?, ?, ?)");
if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Database prepare failed']);
    exit;
}

$sanitizedNotes = htmlspecialchars($notes, ENT_QUOTES, 'UTF-8');
$sanitizedCompanyName = htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8');
$stmt->bind_param("ssi", $sanitizedCompanyName, $sanitizedNotes, $userId);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
    $stmt->close();
    $mysqli->close();
    exit;
}
$stmt->close();

// 4. ส่งอีเมลแจ้งเตือน
$superAdminEmail = 'superadmin@example.com'; // <<<<<< ใส่อีเมลของ Super Admin
$mail = new PHPMailer(true);

try {
    //การตั้งค่า Server
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();
    $mail->Host       = 'smtp.example.com';        // <<<<<< ใส่ SMTP Server ของคุณ
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@example.com';  // <<<<<< ใส่อีเมลที่จะใช้ส่ง
    $mail->Password   = 'your_email_password';     // <<<<<< ใส่รหัสผ่านของอีเมล
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    //ผู้รับ
    $mail->setFrom('your_email@example.com', 'PrimeForecast System');
    $mail->addAddress($superAdminEmail);

    //เนื้อหา
    $mail->isHTML(true);
    $mail->Subject = 'มีคำขอเพิ่มบริษัทใหม่';
    $mail->Body    = "
        <h2>มีคำขอเพิ่มบริษัทใหม่เข้าระบบ</h2>
        <p>คุณ <strong>{$userEmail}</strong> ได้ส่งคำขอเพิ่มข้อมูลบริษัทใหม่ ดังนี้:</p>
        <hr>
        <p><strong>ชื่อบริษัทที่ขอเพิ่ม:</strong> {$sanitizedCompanyName}</p>
        <p><strong>รายละเอียดเพิ่มเติม:</strong><br>{$sanitizedNotes}</p>
        <hr>
        <p>กรุณาเข้าระบบเพื่อตรวจสอบและดำเนินการอนุมัติคำขอนี้</p>
    ";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // กรณีส่งอีเมลไม่สำเร็จ ให้ส่ง error message กลับไป
    echo json_encode(['success' => false, 'message' => "บันทึกข้อมูลสำเร็จ แต่ส่งอีเมลไม่สำเร็จ: {$mail->ErrorInfo}"]);
}

$mysqli->close();
?>