<?php
session_start();

// โหลดฟังก์ชัน
include __DIR__ . '/../../functions.php';
$mysqli = connectDb();

// ตรวจสอบว่าเป็น POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $industry = trim($_POST['Industry'] ?? '');
    $action   = $_POST['action']   ?? 'add';

    if ($action === 'edit' && !empty($_POST['Industry_id']) && is_numeric($_POST['Industry_id'])) {
        // แก้ไข
        $industry_id = (int) $_POST['Industry_id'];

        // ใช้ table ชื่อ lowercase ตามจริง
        $sql = "UPDATE `industry_group`
                   SET `Industry` = ?
                 WHERE `industry_id` = ?";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("si", $industry, $industry_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('อัปเดตข้อมูลสำเร็จ');
                    window.location.href = 'fixed.php';
                  </script>";
        } else {
            echo "<script>
                    alert('เกิดข้อผิดพลาดในการอัปเดต: " . addslashes($stmt->error) . "');
                    window.history.back();
                  </script>";
        }
        $stmt->close();

    } elseif ($industry !== '') {
        // เพิ่มใหม่
        $sql = "INSERT INTO `industry_group` (`Industry`) VALUES (?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("s", $industry);

        if ($stmt->execute()) {
            echo "<script>
                    alert('เพิ่มข้อมูลสำเร็จ');
                    window.location.href = 'fixed.php';
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
