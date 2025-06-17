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
    && isset($_GET['Industry_id'])
    && ctype_digit($_GET['Industry_id'])) {

    $Industry_id = (int)$_GET['Industry_id'];

    /* ---------- 1) เช็กการอ้างอิงใน transactional ---------- */
    $chk = $conn->prepare(
            "SELECT COUNT(*) 
               FROM industry_group  
              WHERE Industry_id  = ?");
    $chk->bind_param('i', $Industry_id );
    $chk->execute();
    $chk->bind_result($inUse);
    $chk->fetch();
    $chk->close();

    if ($inUse > 0) {
        echo "<script>
                alert('ไม่สามารถลบได้ เพราะมีการใช้ Industry_id  ในข้อมูลบริษัทอยู่');
                location.href='fixed.php';
              </script>";
        exit;
    }

    /* ---------- 2) ลบได้ ---------- */
    $del = $conn->prepare(
            "DELETE FROM industry_group 
              WHERE Industry_id = ?");
    $del->bind_param('i', $Industry_id);

    if ($del->execute()) {
        echo "<script>
                alert('ลบข้อมูลสำเร็จ');
                location.href='fixed.php';
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
