<?php
require_once '../../functions.php';      // ← ปรับ path ให้ถูกกับโปรเจ็กต์
$mysqli = connectDb();

if (isset($_GET['company_id'])) {
    $company_id = $_GET['company_id'];

    // ตรวจสอบก่อนว่ามีการใช้งานใน transactional หรือไม่
    $check = $mysqli->prepare("SELECT COUNT(*) FROM transactional WHERE company_id = ?");
    $check->bind_param("i", $company_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>alert('ไม่สามารถลบบริษัทนี้ได้ เนื่องจากมีการใช้งานอยู่ในธุรกรรม'); window.history.back();</script>";
        exit;
    }

    // ถ้าไม่มีการใช้งานก็ลบได้
    $stmt = $mysqli->prepare("DELETE FROM company_catalog WHERE company_id = ?");
    $stmt->bind_param("i", $company_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบข้อมูลสำเร็จ'); window.location.href='top-nav.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบ'); window.history.back();</script>";
    }

    $stmt->close();
}
?>
