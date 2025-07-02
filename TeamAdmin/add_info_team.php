<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ------------------------------------------------------------------
   add_info_team.php – (แก้ไขล่าสุด)
   ------------------------------------------------------------------ */
require_once '../functions.php';
session_start();

if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$mysqli = connectDb();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_infoteamadmin.php');
    exit;
}

/* ------------------------------------------------------------------
   รับค่าจากฟอร์ม
   ------------------------------------------------------------------ */
$user_id            = (int)($_POST['user_id'] ?? 0);
$company_id         = (int)($_POST['company_id'] ?? 0);
$product_id         = (int)($_POST['Product_id'] ?? 0);
$product_detail     = trim($_POST['Product_detail'] ?? '');
$Step_id            = 0;
$Source_budget_id   = (int)($_POST['Source_budget_id'] ?? 0);
$fiscalyear         = $_POST['fiscalyear'] ?? null;

$flag = fn($k) => isset($_POST[$k]) && $_POST[$k] === '1' ? 1 : 0;
$date = fn($k) => ($_POST[$k] ?? '') ?: null;

$present         = $flag('present');
$budgeted        = $flag('budgeted');
$tor             = $flag('tor');
$tor_date        = $date('tor_date');
$bidding         = $flag('bidding');
$bidding_date    = $date('bidding_date');
$win             = $flag('win');
$win_date        = $date('win_date');
$lost            = $flag('lost');
$lost_date       = $date('lost_date');

$currentDate = date('Y-m-d');
$present_date  = $present  ? $currentDate : null;
$budgeted_date = $budgeted ? $currentDate : null;

if ($win) $Step_id = 5;
elseif ($lost) $Step_id = 6;
elseif ($bidding) $Step_id = 4;
elseif ($tor) $Step_id = 3;
elseif ($budgeted) $Step_id = 2;
elseif ($present) $Step_id = 1;

$team_id              = (int)($_POST['team_id'] ?? 0);
$contact_start_date   = $date('contact_start_date');
$predict_close_date   = $date('date_of_closing_of_sale');
$deal_close_date      = $date('sales_can_be_close');
$priority_id          = $_POST['priority_id'] !== '' ? (int)$_POST['priority_id'] : null;
$product_value        = (float)str_replace(',', '', $_POST['product_value'] ?? '0');
$remark               = trim($_POST['remark'] ?? '');

if (!$user_id || !$company_id || !$product_id || !$team_id || !$contact_start_date || !$product_detail) {
    $_SESSION['error_message'] = 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน: ชื่อโครงการ, บริษัท, กลุ่มสินค้า, และวันที่เริ่มโครงการ';
    header('Location: add_infoteamadmin.php');
    exit;
}

$sql = "INSERT INTO transactional (
    user_id, company_id, Product_id, Product_detail,
    Step_id, Source_budget_id, fiscalyear,
    present, present_date, budgeted, budgeted_date,
    tor, tor_date, bidding, bidding_date,
    win, win_date, lost, lost_date,
    team_id, contact_start_date, date_of_closing_of_sale, sales_can_be_close,
    priority_id, product_value, remark, timestamp
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp()
)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    exit('Prepare failed: ' . $mysqli->error);
}

/* ------------------------------------------------------------------
   bind_param (ฉบับแก้ไขที่ถูกต้อง 100%)
   ------------------------------------------------------------------ */
$stmt->bind_param(
    "iiisiss" .  // 7 ตัว: user_id, company_id, Product_id, Product_detail, Step_id, Source_budget_id, fiscalyear
    "isis"    .  // 4 ตัว: present, present_date, budgeted, budgeted_date
    "isis"    .  // 4 ตัว: tor, tor_date, bidding, bidding_date
    "isis"    .  // 4 ตัว: win, win_date, lost, lost_date
    "isssids",   // 7 ตัว: team_id, contact_start_date, ... , remark
    $user_id, $company_id, $product_id, $product_detail,
    $Step_id, $Source_budget_id, $fiscalyear,
    $present, $present_date, 
    $budgeted, $budgeted_date,
    $tor, $tor_date, 
    $bidding, $bidding_date,
    $win, $win_date, 
    $lost, $lost_date,
    $team_id, $contact_start_date, $predict_close_date, $deal_close_date,
    $priority_id, $product_value, $remark
);

if ($stmt->execute()) {
    $_SESSION['success_message'] = 'บันทึกข้อมูลเรียบร้อยแล้ว!';
    header('Location: add_infoteamadmin.php');
    exit;
}

$_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error;
header('Location: add_infoteamadmin.php');
exit();
?>