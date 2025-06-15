<?php
/* ------------------------------------------------------------------
   add_user.php  –  รับข้อมูลจากฟอร์ม adduser01.php แล้วบันทึกลงตาราง transactional
   ฟิลด์ตรงกับภาพโครงสร้างฐานข้อมูลที่ส่งมา
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
   1) รับค่าจากฟอร์ม (ใช้ชื่อ field ตาม adduser01.php)
   ------------------------------------------------------------------ */
$user_id              = (int)($_POST['user_id']            ?? 0);
$company_id           = (int)($_POST['company_id']         ?? 0);
$product_id           = (int)($_POST['Product_id']         ?? 0);
$product_detail       = trim($_POST['Product_detail']      ?? '');
$team_id              = (int)($_POST['team_id']            ?? 0);
$priority_id          = $_POST['priority_id']              !== '' ? (int)$_POST['priority_id'] : null;

// ค่าตัวเลข – ตัดคอมมาออก, แปลงเป็น float
$product_value        = str_replace(',', '', $_POST['product_value'] ?? '0');
$product_value        = (float)$product_value;

// วันที่
$contact_start_date       = $_POST['contact_start_date']        ?: null;
$predict_close_date       = $_POST['date_of_closing_of_sale']   ?: null;
$deal_close_date          = $_POST['sales_can_be_close']        ?: null;

// Process flags & dates (default 0/null)
$flag = fn(string $k) => isset($_POST[$k]) && $_POST[$k] === '1' ? 1 : 0;
$date = fn(string $k) => ($_POST[$k] ?? '') ?: null;

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

$remark              = trim($_POST['remark'] ?? '');

/* ------------------------------------------------------------------
   2) Validate ขั้นต่ำ
   ------------------------------------------------------------------ */
if (!$user_id || !$company_id || !$product_id || !$team_id || !$contact_start_date) {
    exit('Missing required fields.');
}

/* ------------------------------------------------------------------
   3) เตรียม SQL – ใช้ prepared statement ปลอดภัยจาก SQLi
   ------------------------------------------------------------------ */
$sql = "INSERT INTO transactional (
            user_id, company_id, Product_id, Product_detail,
            present, present_date, budgeted, budgeted_date, tor, tor_date,
            bidding, bidding_date, win, win_date, lost, lost_date,
            team_id, contact_start_date, date_of_closing_of_sale, sales_can_be_close,
            priority_id, product_value, remark, timestamp
        ) VALUES (
            ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, current_timestamp()
        )";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    exit('Prepare failed: '.$mysqli->error);
}

/*
   bind_param types:
   i user_id
   i company_id
   i Product_id
   s Product_detail
   i present
   s present_date (nullable)
   i budgeted
   s budgeted_date
   i tor
   s tor_date
   i bidding
   s bidding_date
   i win
   s win_date
   i lost
   s lost_date
   i team_id
   s contact_start_date
   s predict_close_date
   s deal_close_date
   i priority_id (nullable => use null)
   d product_value
   s remark
*/
$stmt->bind_param(
    "iiisisisisisisisissidis",
    $user_id,
    $company_id,
    $product_id,
    $product_detail,
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

if ($stmt->execute()) {
    header('Location: adduser01.php?success=1');
    exit;
}

exit('DB error: '.$stmt->error);
?>
