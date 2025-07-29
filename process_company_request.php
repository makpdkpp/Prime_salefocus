<?php
// process_company_request.php

session_start();
require_once 'functions.php';

// --- ส่วนเรียกใช้งาน PHPMailer แบบ Manual ---
require_once 'lib/PHPMailer/src/Exception.php';
require_once 'lib/PHPMailer/src/PHPMailer.php';
require_once 'lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
$userEmail = htmlspecialchars($_SESSION['email']); // อีเมลของคนส่งคำขอ

if (empty($companyName)) {
    echo json_encode(['success' => false, 'message' => 'กรุณากรอกชื่อบริษัท']);
    exit;
}

$mysqli = connectDb();

// 3. บันทึกข้อมูลลง DB
$stmt = $mysqli->prepare("INSERT INTO company_requests (company_name, notes, user_id) VALUES (?, ?, ?)");
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

// 4. ดึงอีเมล Super Admin (role_id = 1) และส่งอีเมลแจ้งเตือน
$admin_emails = [];
$result = $mysqli->query("SELECT email FROM user WHERE role_id = 1");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $admin_emails[] = $row['email'];
    }
}

if (!empty($admin_emails)) {
    $mail = new PHPMailer(true);
    try {
        // --- ส่วนการตั้งค่า PHPMailer ที่คุณให้มา ---
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'immsendermail@gmail.com';
        $mail->Password = 'npou efln pgpf bhxd'; // This is a Gmail App Password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';

        // --- ผู้ส่งและผู้รับ ---
        $mail->setFrom('no-reply@primeforecast.com', 'PrimeForecast System');
        
        // เพิ่มอีเมล Super Admin ทุกคนเป็นผู้รับ
        foreach ($admin_emails as $admin_email) {
            $mail->addAddress($admin_email);
        }

        // --- เนื้อหาอีเมล ---
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
        echo json_encode(['success' => false, 'message' => "บันทึกข้อมูลสำเร็จ แต่ส่งอีเมลไม่สำเร็จ: {$mail->ErrorInfo}"]);
    }
} else {
    // กรณีที่ไม่พบ Super Admin ในระบบ
    echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ แต่ไม่พบอีเมลผู้ดูแลระบบที่จะแจ้งเตือน']);
}

$mysqli->close();
?>