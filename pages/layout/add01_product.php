<?php
include("../../functions.php");
$conn = connectDb();
$sql = "SELECT id, Industry FROM industry_group ";


// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $Industry = $_POST['Industry'];

    // คำสั่ง SQL เพื่อเพิ่มข้อมูล
    $sql = "INSERT INTO industry_group (Industry) VALUES (?)";

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($sql);

    // ผูกค่าตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("s", $Industry);  // s = string, i = integer

    // เรียกใช้คำสั่ง SQL และตรวจสอบการเพิ่มข้อมูล
    if ($stmt->execute()) {
        echo "ข้อมูลถูกเพิ่มสำเร็จ!";
        header("location: fixed.php");
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
