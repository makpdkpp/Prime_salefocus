<?php
session_start();
require_once __DIR__ . '/../../functions.php';
$mysqli = connectDb();


/* ——— รับค่าจากฟอร์ม ——— */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ฟิลด์ในฟอร์ม */
    $Source_budge = trim($_POST['Source_budge'] ?? '');
    $action       = $_POST['action']       ?? 'add';     // add | edit

   /* ---------- EDIT ---------- */
if ($action === 'edit'
    && isset($_POST['Source_budget_id'])
    && is_numeric($_POST['Source_budget_id'])) {

    $Source_budget_id = (int)$_POST['Source_budget_id'];

    $sql  = "UPDATE source_of_the_budget
                SET Source_budge = ?
              WHERE Source_budget_id = ?";
    $stmt = $mysqli->prepare($sql) or die($mysqli->error);
    $stmt->bind_param("si", $Source_budge, $Source_budget_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('อัปเดตข้อมูลสำเร็จ');
                location='Source_of_the_budget.php';
              </script>";
    } else {
        echo "<script>
                alert('Error : ".addslashes($stmt->error)."');
                history.back();
              </script>";
    }
    exit;   // *** สำคัญ: จบที่นี่ ไม่ให้ไหลไป ADD
}


    /* ---------- ADD ---------- */
    if ($Source_budge !== '') {

        $sql  = "INSERT INTO `source_of_the_budget` (`Source_budge`)
                 VALUES (?)";
        $stmt = $mysqli->prepare($sql) or die("Prepare failed: ".$mysqli->error);
        $stmt->bind_param("s", $Source_budge);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ');
                  window.location.href='Source_of_the_budget.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: ".addslashes($stmt->error)."');
                  history.back();</script>";
        }
        $stmt->close();
        exit;
    }

    /* ---------- ข้อมูลไม่ครบ ---------- */
    echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน');history.back();</script>";
}
/* ---------- method ไม่ใช่ POST ---------- */
$mysqli->close();
header('Location: Source_of_the_budget.php');
exit;
?>
