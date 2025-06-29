<?php

/* ------------------------------------------------------------------
   add_user.php  –  รับข้อมูลจากฟอร์ม adduser01.php แล้วบันทึกลงตาราง transactional
   ------------------------------------------------------------------ */
require_once '../functions.php';
session_start();
// ตรวจสอบ session และ role
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] == !2 ) {
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
    team_id, contact_start_date, date_of_closing_of_sale, sales_can_be_close,
    priority_id, product_value, remark, timestamp
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp()
)";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    exit('Prepare failed: ' . $mysqli->error);
}

/* ------------------------------------------------------------------
   bind_param – 13 ค่า + timestamp
   ------------------------------------------------------------------ */
// Type string = 13 ตัวอักษร
// i = int, s = string, d = double
$stmt->bind_param(
    "iiisiiisisiids", // ← แก้ให้เป็น 13 ตัวเท่านั้น
    $user_id,
    $company_id,
    $product_id,
    $product_detail,
    $Step_id,
    $Source_budget_id,
    $fiscalyear, // ถ้า fiscalyear เป็น string ให้ใช้ s

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
    $transac_id = $mysqli->insert_id;

    // file_put_contents('debug_post.log', print_r($_POST, true));

    // รับค่าจากฟอร์มใหม่: step[level_id] และ step_date[level_id]
    $stepsChecked = $_POST['step'] ?? [];
    $stepsDate = $_POST['step_date'] ?? [];
    foreach ($stepsChecked as $level_id => $checked) {
        if ($checked && $level_id) {
            $date = $stepsDate[$level_id] ?? null;
            $stmt2 = $mysqli->prepare("INSERT INTO transactional_step (transac_id, level_id, date) VALUES (?, ?, ?)");
            $stmt2->bind_param('iis', $transac_id, $level_id, $date);
            $stmt2->execute();
            $stmt2->close();
        }
    }
    header('Location: adduser01.php?success=1');
    exit;
}

exit('DB error: ' . $stmt->error);
?>
