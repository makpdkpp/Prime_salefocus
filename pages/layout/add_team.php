<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 1) {
    header('Location: ../../index.php');
    exit;
  }
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $team = trim($_POST['team'] ?? '');

    // แก้ไขข้อมูล
    if (
        isset($_POST['action']) &&
        $_POST['action'] === 'edit' &&
        !empty($_POST['team_id']) &&
        is_numeric($_POST['team_id'])
    ) {
        $team_id = (int)$_POST['team_id'];

        $stmt = $mysqli->prepare("UPDATE team_catalog SET team = ? WHERE team_id = ?");
        $stmt->bind_param("si", $team, $team_id);

        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location.href='Saleteam.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();

    // เพิ่มข้อมูลใหม่
    } elseif ($team !== '') {
        $stmt = $mysqli->prepare("INSERT INTO team_catalog (team) VALUES (?)");
        $stmt->bind_param("s", $team);

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='Saleteam.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
    }
}

$mysqli->close();
?>
