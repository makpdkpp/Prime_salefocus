<?php
session_start();
include("../../functions.php");

$mysqli = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
  header('Location: ../../index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $company_id = intval($_POST['company_id'] ?? 0);
  $company = trim($_POST['company'] ?? '');
  $industry_id = intval($_POST['industry'] ?? 0);

  if ($company_id && $company && $industry_id) {
    $stmt = $mysqli->prepare("UPDATE company_catalog SET company = ?, Industry_id = ? WHERE company_id = ?");
    $stmt->bind_param("sii", $company, $industry_id, $company_id);

    if ($stmt->execute()) {
      echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='top-nav.php';</script>";
    } else {
      echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดต'); window.history.back();</script>";
    }

    $stmt->close();
  } else {
    echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
  }
}
?>
