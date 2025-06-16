<?php
/***********************************************************************
 * save_industry.php   –   เพิ่ม / แก้ไข  industry_group
 ***********************************************************************/
session_start();
require_once '../../functions.php';      // ← ปรับ path ให้ถูกกับโปรเจ็กต์
$mysqli = connectDb();
$mysqli->set_charset('utf8');            // ป้องกันภาษาไทยเพี้ยน
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ===== 1) ต้องเป็น POST เท่านั้น ===== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<script>alert('Method ไม่ถูกต้อง');history.back();</script>";
    exit;
}

/* ===== 2) รับค่าจากฟอร์ม ===== */
$Industry = isset($_POST['Industry']) ? trim($_POST['Industry']) : '';
$action   = isset($_POST['action'])   ? $_POST['action']        : 'add';

function errorBack($msg){
    echo "<script>alert('".addslashes($msg)."');history.back();</script>";
    exit;
}

/* ===== 3) แก้ไข (action = edit) ===== */
if ($action === 'edit') {

    if ($Industry === '') errorBack('กรุณากรอกชื่อ Industry');

    if (empty($_POST['Industry_id']) || !ctype_digit($_POST['Industry_id']))
        errorBack('Industry_id ไม่ถูกต้อง');

    $Industry_id = (int)$_POST['Industry_id'];

    $sql  = "UPDATE industry_group SET industry=? WHERE industry_id=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('si', $Industry, $Industry_id);
    $stmt->execute();

    echo "<script>alert('อัปเดตสำเร็จ');location='fixed.php';</script>";
    exit;
}

/* ===== 4) เพิ่มใหม่ (action = add) ===== */
if ($Industry === '') errorBack('กรุณากรอกชื่อ Industry');

$sql  = "INSERT INTO industry_group (industry) VALUES (?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('s', $Industry);

try {
    $stmt->execute();
    echo "<script>alert('เพิ่มข้อมูลสำเร็จ');location='fixed.php';</script>";
} catch (mysqli_sql_exception $e) {
    // เช่น Duplicate entry : error code 1062
    errorBack('เพิ่มไม่สำเร็จ: '.$e->getMessage());
}
?>
