<?php
session_start();

// โหลดฟังก์ชัน
require_once '../../functions.php';      // ← ปรับ path ให้ถูกกับโปรเจ็กต์
$mysqli = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }

// ตรวจสอบว่าเป็น POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Product = trim($_POST['Product'] ?? '');
    $action   = $_POST['action']   ?? 'add';

    if ($action === 'edit' && !empty($_POST['product_id']) && is_numeric($_POST['product_id'])) {
        // แก้ไข
        $product_id = (int) $_POST['product_id'];

        // ใช้ table ชื่อ lowercase ตามจริง
        $sql = "UPDATE `product_group`
                   SET `Product` = ?
                 WHERE `product_id` = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("si", $Product, $product_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('อัปเดตข้อมูลสำเร็จ');
                    window.location.href = 'boxed.php';
                  </script>";
        } else {
            echo "<script>
                    alert('เกิดข้อผิดพลาดในการอัปเดต: " . addslashes($stmt->error) . "');
                    window.history.back();
                  </script>";
        }
        $stmt->close();

    } elseif ($Product !== '') {
        // เพิ่มใหม่
        $sql = "INSERT INTO `product_group` (`Product`) VALUES (?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("s", $Product);

        if ($stmt->execute()) {
            echo "<script>
                    alert('เพิ่มข้อมูลสำเร็จ');
                    window.location.href = 'boxed.php';
                  </script>";
        } else {
            echo "<script>
                    alert('เกิดข้อผิดพลาดในการเพิ่ม: " . addslashes($stmt->error) . "');
                    window.history.back();
                  </script>";
        }
        $stmt->close();

    } else {
        echo "<script>
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                window.history.back();
              </script>";
    }
}

$mysqli->close();
?>
