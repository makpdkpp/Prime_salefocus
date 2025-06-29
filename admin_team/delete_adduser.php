<?php
session_start();
include("../connect.php");
session_start();
// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}
// ดึงข้อมูลของผู้ใช้จากเซสชัน
$email = $_SESSION['email'];

// ตรวจสอบว่าอีเมล์ในเซสชันมีอยู่หรือไม่
if (isset($email)) {
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล โดยการ JOIN ตาราง users และ profile_user
    $sql = "SELECT users.id, users.email, profile_user.team, profile_user.F_name FROM users 
            INNER JOIN profile_user ON users.email = profile_user.email 
            WHERE users.email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];  // ID ของผู้ใช้ที่ล็อกอิน
        $team = $row['team'];
        $F_name = $row['F_name'];
        $table_name = "user_" . $user_id; // ชื่อตารางที่เก็บข้อมูลของผู้ใช้

        // รับค่า 'id' จาก URL
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];

            // ตรวจสอบว่า $id เป็นตัวเลข และเป็นข้อมูลของผู้ใช้ที่ล็อกอิน
            if (is_numeric($id)) {
                // ตรวจสอบว่าผู้ใช้มีสิทธิ์ลบข้อมูล
                $check_sql = "SELECT * FROM $table_name WHERE id = '$id' AND salesperson = '$F_name'";  // เช็คข้อมูลเฉพาะของผู้ใช้ที่ล็อกอิน
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) {
                    // ลบข้อมูลจากตาราง
                    $delete_sql = "DELETE FROM $table_name WHERE id = '$id'";
                    if ($conn->query($delete_sql) === TRUE) {
                        // แสดงป๊อปอัพแจ้งเตือนการลบข้อมูล
                        echo "<script>alert('ข้อมูลถูกลบสำเร็จ'); window.location.href='../home_user.php';</script>";
                    } else {
                        echo "Error: " . $conn->error;
                    }
                } else {
                    echo "ไม่พบข้อมูลที่ต้องการลบ หรือ คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้";
                    exit;
                }
            } else {
                echo "ID ที่ส่งมาผิดพลาด";
                exit;
            }
        } else {
            echo "ไม่พบการระบุ ID ใน URL";
            exit;
        }
    } else {
        echo "ไม่พบข้อมูลผู้ใช้";
    }
} else {
    echo "กรุณาล็อกอินก่อนที่จะทำการลบข้อมูล";
}
?>
