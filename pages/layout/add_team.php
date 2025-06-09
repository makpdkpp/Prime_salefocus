<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $team = $_POST['team'];

    // คำสั่ง SQL เพื่อเพิ่มข้อมูล
    $sql = "INSERT INTO team_catalog (team) VALUES (?)";

    // เตรียมคำสั่ง SQL
    $stmt = $mysqli->prepare($sql);

    // ผูกค่าตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("s", $team);  // s = string, i = integer

    // เรียกใช้คำสั่ง SQL และตรวจสอบการเพิ่มข้อมูล
    if ($stmt->execute()) {
        echo "ข้อมูลถูกเพิ่มสำเร็จ!";
        header("location: Saleteam.php");
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$mysqli->close();
?>
