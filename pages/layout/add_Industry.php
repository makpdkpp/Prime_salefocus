<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();
$mysqli->set_charset('utf8');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

/* ทำเฉพาะ POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: fixed.php');  // กันคนเปิดตรง-ๆ
    exit;
}

$Industry = trim($_POST['Industry'] ?? '');
$action   = $_POST['action'] ?? 'add';

/* ---------- EDIT ---------- */
if ($action === 'edit') {

    if ($Industry === '') {
        echo "<script>alert('กรุณากรอกชื่อ Industry');history.back();</script>";
        exit;
    }

    if (empty($_POST['Industry_id']) || !ctype_digit($_POST['Industry_id'])) {
        echo "<script>alert('Industry_id ไม่ถูกต้อง');history.back();</script>";
        exit;
    }

    $Industry_id = (int)$_POST['Industry_id'];

    $stmt = $mysqli->prepare(
        "UPDATE industry_group SET Industry = ? WHERE Industry_id = ?"
    );
    $stmt->bind_param('si', $Industry, $Industry_id);
    $stmt->execute();

    echo "<script>alert('อัปเดตสำเร็จ');location='fixed.php';</script>";
    exit;
}

/* ---------- ADD ---------- */
if ($Industry === '') {
    echo "<script>alert('กรุณากรอกชื่อ Industry');history.back();</script>";
    exit;
}

$stmt = $mysqli->prepare(
    "INSERT INTO industry_group (Industry) VALUES (?)"
);
$stmt->bind_param('s', $Industry);
$stmt->execute();

echo "<script>alert('เพิ่มข้อมูลสำเร็จ');location='fixed.php';</script>";
