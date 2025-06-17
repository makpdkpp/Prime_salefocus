<?php
/* ---------------------------------------------------------
   delete_source_budget.php   –   ลบข้อมูลใน source_of_the_budget
   (ป้องกันการลบถ้ามีการอ้างอิงใน transactional)
---------------------------------------------------------- */
session_start();
require_once '../../functions.php';      // ฟังก์ชัน connectDb()
$conn = connectDb();

/* ตรวจว่าเป็น GET และมี id ที่เป็นตัวเลข */
if ($_SERVER['REQUEST_METHOD'] === 'GET'
    && isset($_GET['Source_budget_id'])
    && ctype_digit($_GET['Source_budget_id'])) {

    $Source_budget_id = (int)$_GET['Source_budget_id'];

    /* ---------- 1) เช็กการอ้างอิงใน transactional 
    $chk = $conn->prepare(
            "SELECT COUNT(*) 
               FROM transactional 
              WHERE Source_budget_id = ?");
    $chk->bind_param('i', $Source_budget_id);
    $chk->execute();
    $chk->bind_result($inUse);
    $chk->fetch();
    $chk->close();

    if ($inUse > 0) {
        echo "<script>
                alert('ไม่สามารถลบได้ เพราะมีการใช้ Source_budget นี้อยู่ในข้อมูลการขาย');
                location.href='Source_of_the_budget.php';
              </script>";
        exit;
    }

    /* ---------- 2) ลบได้ ---------- */
    $del = $conn->prepare(
            "DELETE FROM source_of_the_budget 
              WHERE Source_budget_id = ?");
    $del->bind_param('i', $Source_budget_id);

    if ($del->execute()) {
        echo "<script>
                alert('ลบข้อมูลสำเร็จ');
                location.href='Source_of_the_budget.php';
              </script>";
    } else {
        echo "<script>
                alert('เกิดข้อผิดพลาดในการลบ: ".addslashes($del->error)."');
                history.back();
              </script>";
    }
    $del->close();

} else {
    echo "<script>
            alert('รหัสไม่ถูกต้อง หรือไม่มีข้อมูล');
            history.back();
          </script>";
}

$conn->close();
?>
