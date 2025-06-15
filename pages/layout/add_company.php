<?php
session_start();
include("../../functions.php");

$mysqli = connectDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $company = trim($_POST['company'] ?? '');
  $industry_id = $_POST['industry'] ?? '';

  if ($company && $industry_id) {
    // เตรียม statement
    $stmt = $mysqli->prepare("INSERT INTO company_catalog (company, Industry_id) VALUES (?, ?)");
    $stmt->bind_param("si", $company, $industry_id);

    if ($stmt->execute()) {
      echo "<script>alert('เพิ่มข้อมูลบริษัทสำเร็จ'); window.location.href='top-nav.php';</script>";
    } else {
      echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
    }

    $stmt->close();
  } else {
    echo "<script>alert('กรุณากรอกข้อมูลให้ครบ'); window.history.back();</script>";
  }
}
?>
