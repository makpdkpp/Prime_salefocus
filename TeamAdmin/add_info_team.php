<?php
/* ------------------------------------------------------------------
   add_info_team.php  –  รับข้อมูลจากฟอร์ม add_infoteamadmin.php
   (แก้ไข Redirect ไปหน้าตาราง)
   ------------------------------------------------------------------ */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$mysqli->begin_transaction();

try {
    // --- รับค่าจากฟอร์ม (ส่วนนี้เหมือนเดิม) ---
    $user_id            = (int)($_POST['user_id'] ?? 0);
    $company_id         = (int)($_POST['company_id'] ?? 0);
    $product_id         = (int)($_POST['Product_id'] ?? 0);
    $product_detail     = trim($_POST['Product_detail'] ?? '');
    $Source_budget_id   = (int)($_POST['Source_budget_id'] ?? 0);
    $fiscalyear         = $_POST['fiscalyear'] ?? null;
    $team_id            = (int)($_POST['team_id'] ?? 0);
    
    $date = fn($k) => ($_POST[$k] ?? '') ?: null;
    $contact_start_date   = $date('contact_start_date');
    $predict_close_date   = $date('date_of_closing_of_sale');
    $deal_close_date      = $date('sales_can_be_close');
    
    $priority_id          = $_POST['priority_id'] !== '' ? (int)$_POST['priority_id'] : null;
    $product_value        = (float)str_replace(',', '', $_POST['product_value'] ?? '0');
    $remark               = trim($_POST['remark'] ?? '');

    $steps      = $_POST['step'] ?? [];
    $step_dates = $_POST['step_date'] ?? [];

    $Step_id = 0;
    foreach ($steps as $level_id => $value) {
        if ($value != '0') {
            $Step_id = max($Step_id, (int)$level_id);
        }
    }
    
    if (!$user_id || !$company_id || !$product_id || !$team_id || !$contact_start_date || !$product_detail) {
        throw new Exception('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
    }

    // --- INSERT ข้อมูลหลัก (ส่วนนี้เหมือนเดิม) ---
    $sql_main = "INSERT INTO transactional (
        user_id, company_id, Product_id, Product_detail,
        Step_id, Source_budget_id, fiscalyear, team_id,
        contact_start_date, date_of_closing_of_sale, sales_can_be_close,
        priority_id, product_value, remark, timestamp
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp())";

    $stmt_main = $mysqli->prepare($sql_main);
    if (!$stmt_main) {
        throw new Exception('Prepare statement for transactional failed: ' . $mysqli->error);
    }
    
    $stmt_main->bind_param(
        "iiisisiisssids",
        $user_id, $company_id, $product_id, $product_detail,
        $Step_id, $Source_budget_id, $fiscalyear, $team_id,
        $contact_start_date, $predict_close_date, $deal_close_date,
        $priority_id, $product_value, $remark
    );

    if (!$stmt_main->execute()) {
        throw new Exception('Execute statement for transactional failed: ' . $stmt_main->error);
    }

    $transac_id = $mysqli->insert_id;
    $stmt_main->close();

    // --- INSERT ข้อมูลสถานะ (ส่วนนี้เหมือนเดิม) ---
    $sql_step = "INSERT INTO transactional_step (transac_id, level_id, date) VALUES (?, ?, ?)";
    $stmt_step = $mysqli->prepare($sql_step);
    if (!$stmt_step) {
        throw new Exception('Prepare statement for steps failed: ' . $mysqli->error);
    }

    foreach ($steps as $level_id => $value) {
        if ($value != '0') {
            $current_step_id = (int)$level_id;
            $completed_date = !empty($step_dates[$current_step_id]) ? $step_dates[$current_step_id] : date('Y-m-d');

            $stmt_step->bind_param("iis", $transac_id, $current_step_id, $completed_date);
            if (!$stmt_step->execute()) {
                throw new Exception('Execute statement for steps failed: ' . $stmt_step->error);
            }
        }
    }
    $stmt_step->close();
    
    // ---- ถ้าทุกอย่างสำเร็จ ----
    $mysqli->commit();
    $_SESSION['success_message'] = 'บันทึกข้อมูลเรียบร้อยแล้ว!';
    
    // ▼▼▼ แก้ไขบรรทัดนี้เพื่อไปที่หน้าตาราง ▼▼▼
    header('Location: home_admin_team_table.php');
    exit;

} catch (Exception $e) {
    // ---- ถ้ามีข้อผิดพลาดเกิดขึ้น ----
    $mysqli->rollback();
    $_SESSION['error_message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage();
    header('Location: add_infoteamadmin.php');
    exit();
}
?>