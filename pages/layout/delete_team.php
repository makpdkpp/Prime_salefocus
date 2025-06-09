<?php
require_once '../../functions.php';
$mysqli = connectDb();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // แปลงให้เป็นตัวเลข ป้องกัน SQL injection

    // ขั้นตอนที่ 1: อัปเดต user ให้ team_id เป็น NULL ก่อน
    $updateSql = "UPDATE user SET team_id = NULL WHERE team_id = $id";
    $mysqli->query($updateSql);

    // ขั้นตอนที่ 2: ลบทีมออกจาก team_catalog
    $deleteSql = "DELETE FROM team_catalog WHERE id = $id";

    if ($mysqli->query($deleteSql) === TRUE) {
        header("location: Saleteam.php?success=ลบทีมเรียบร้อย");
        exit;
    } else {
        echo "เกิดข้อผิดพลาด: " . $mysqli->error;
    }
}

$mysqli->close();
?>
