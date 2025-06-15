<?php
session_start();
require_once '../../functions.php';
$conn = connectDb();

// ตรวจสอบว่าได้รับค่า Industry_id และเป็นตัวเลข
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['Industry_id']) && is_numeric($_GET['Industry_id'])) {
    $industry_id = (int)$_GET['Industry_id'];

    // ตรวจสอบว่าอุตสาหกรรมนี้ถูกใช้อยู่ใน company_catalog หรือไม่
    $check = $conn->prepare("SELECT COUNT(*) FROM company_catalog WHERE Industry_id = ?");
    $check->bind_param("i", $industry_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        echo "<script>
            alert('ไม่สามารถลบได้ เนื่องจากอุตสาหกรรมนี้ถูกใช้งานอยู่ในข้อมูลบริษัท');
            window.location.href='fixed.php';
        </script>";
        exit();
    }

    // หากไม่มีการใช้งาน ให้ทำการลบ
    $stmt = $conn->prepare("DELETE FROM industry_group WHERE Industry_id = ?");
    $stmt->bind_param("i", $industry_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('ลบข้อมูลอุตสาหกรรมสำเร็จ');
            window.location.href='fixed.php';
        </script>";
    } else {
        echo "<script>
            alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            window.history.back();
        </script>";
    }

    $stmt->close();
} else {
    echo "<script>
        alert('รหัสไม่ถูกต้อง หรือไม่มีข้อมูล');
        window.history.back();
    </script>";
}

$conn->close();
?>
