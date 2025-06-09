<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $add_company = $_POST['company'];
    $add_industry = $_POST['industry'];
  
    // คำสั่ง SQL เพื่อเพิ่มข้อมูล
    $sqlid = "SELECT Industry_id FROM industry_group WHERE industry = '$add_industry' ";
    $industryid = $mysqli->query($sqlid);
    $row = $industryid->fetch_assoc();
    $id = $row['Industry_id'];
    echo $id;
    $sql = "INSERT INTO company_catalog (company, Industry_id) VALUES (?,?)";

    // เตรียมคำสั่ง SQL
    $stmt = $mysqli->prepare($sql);

    // ผูกค่าตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("si", $add_company , $id);  // s = string, i = integer

    // เรียกใช้คำสั่ง SQL และตรวจสอบการเพิ่มข้อมูล
    if ($stmt->execute()) {
        echo "ข้อมูลถูกเพิ่มสำเร็จ!";
        header("location: top-nav.php");
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$mysqli->close();
?>
