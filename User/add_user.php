<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ------------------------------------------------------------------
   add_user.php  –  รับข้อมูลจากฟอร์ม adduser01.php แล้วบันทึกลงตาราง transactional
   ------------------------------------------------------------------ */
require_once '../functions.php';
session_start();

// ===== ตรวจสอบสิทธิ์ =====
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$mysqli = connectDb();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adduser01.php');
    exit;
}

/* ------------------------------------------------------------------
   รับค่าจากฟอร์ม
   ------------------------------------------------------------------ */
$user_id            = (int)($_POST['user_id'] ?? 0);
$company_id         = (int)($_POST['company_id'] ?? 0);
$product_id         = (int)($_POST['Product_id'] ?? 0);
$product_detail     = trim($_POST['Product_detail'] ?? '');
$Step_id            = (int)($_POST['Step_id'] ?? 0);
$Source_budget_id   = (int)($_POST['Source_budget_id'] ?? 0);
$fiscalyear         = $_POST['fiscalyear'] ?? null;

$flag = fn($k) => isset($_POST[$k]) && $_POST[$k] === '1' ? 1 : 0;
$date = fn($k) => ($_POST[$k] ?? '') ?: null;

$present         = $flag('present');
$present_date    = $date('present_date');
$budgeted        = $flag('budgeted');
$budgeted_date   = $date('budgeted_date');
$tor             = $flag('tor');
$tor_date        = $date('tor_date');
$bidding         = $flag('bidding');
$bidding_date    = $date('bidding_date');
$win             = $flag('win');
$win_date        = $date('win_date');
$lost            = $flag('lost');
$lost_date       = $date('lost_date');

$team_id              = (int)($_POST['team_id'] ?? 0);
$contact_start_date   = $date('contact_start_date');
$predict_close_date   = $date('date_of_closing_of_sale');
$deal_close_date      = $date('sales_can_be_close');
$priority_id          = $_POST['priority_id'] !== '' ? (int)$_POST['priority_id'] : null;
$product_value        = (float)str_replace(',', '', $_POST['product_value'] ?? '0');
$remark               = trim($_POST['remark'] ?? '');

/* ------------------------------------------------------------------
   ตรวจสอบค่าบังคับ
   ------------------------------------------------------------------ */
if (!$user_id || !$company_id || !$product_id || !$team_id || !$contact_start_date) {
    exit('Missing required fields.');
}

/* ------------------------------------------------------------------
   SQL Insert
   ------------------------------------------------------------------ */
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
   bind_param – 26 ค่า + timestamp
   ------------------------------------------------------------------ */
// Type string = 26 ตัวอักษร
// i = int, s = string, d = double
$stmt->bind_param(
    "iiisiiisisiisisisisisssids", // ← แก้ให้เป็น 26 ตัวเท่านั้น
    $user_id,
    $company_id,
    $product_id,
    $product_detail,
    $Step_id,
    $Source_budget_id,
    $fiscalyear, // ถ้า fiscalyear เป็น string ให้ใช้ s

    $present,
    $present_date,
    $budgeted,
    $budgeted_date,
    $tor,
    $tor_date,
    $bidding,
    $bidding_date,
    $win,
    $win_date,
    $lost,
    $lost_date,

    $team_id,
    $contact_start_date,
    $predict_close_date,
    $deal_close_date,
    $priority_id,
    $product_value,
    $remark
);

/* ------------------------------------------------------------------
   Execute
   ------------------------------------------------------------------ */
if ($stmt->execute()) {
    header('Location: adduser01.php?success=1');
    exit;
}

exit('DB error: ' . $stmt->error);
?>
