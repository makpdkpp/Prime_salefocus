<?php
require_once 'functions.php';
session_start();

// ตรวจสอบสถานะล็อกอิน และบทบาท (role_id 2)
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    header('Location: index.php');
    exit;
}

// เชื่อมต่อฐานข้อมูล
$mysqli = connectDb();

// ฟังก์ชันช่วยส่ง JSON error และ exit
function sendJsonError($message) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Query Error: ' . $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// อ่าน user_id จาก session
$userId = (int)$_SESSION['user_id'];

// เตรียม output array
$output = [];

// 1. sumstep per level for current user
$sql = "
SELECT
  month,
  SUM(CASE WHEN type = 'present'  THEN value ELSE 0 END) AS present_value,
  SUM(CASE WHEN type = 'budgeted' THEN value ELSE 0 END) AS budgeted_value,
  SUM(CASE WHEN type = 'tor'      THEN value ELSE 0 END) AS tor_value,
  SUM(CASE WHEN type = 'bidding'  THEN value ELSE 0 END) AS bidding_value,
  SUM(CASE WHEN type = 'win'      THEN value ELSE 0 END) AS win_value,
  SUM(CASE WHEN type = 'lost'     THEN value ELSE 0 END) AS lost_value
FROM (
  SELECT DATE_FORMAT(present_date,  '%Y-%m') AS month,
         product_value AS value,
         'present'      AS type
    FROM transactional
   WHERE present = 1
     AND present_date IS NOT NULL
     AND user_id = ?
  UNION ALL
  SELECT DATE_FORMAT(budgeted_date, '%Y-%m'),
         product_value,
         'budgeted'
    FROM transactional
   WHERE budgeted = 1
     AND budgeted_date IS NOT NULL
     AND user_id = ?
  UNION ALL
  SELECT DATE_FORMAT(tor_date,      '%Y-%m'),
         product_value,
         'tor'
    FROM transactional
   WHERE tor = 1
     AND tor_date IS NOT NULL
     AND user_id = ?
  UNION ALL
  SELECT DATE_FORMAT(bidding_date,  '%Y-%m'),
         product_value,
         'bidding'
    FROM transactional
   WHERE bidding = 1
     AND bidding_date IS NOT NULL
     AND user_id = ?
  UNION ALL
  SELECT DATE_FORMAT(win_date,      '%Y-%m'),
         product_value,
         'win'
    FROM transactional
   WHERE win = 1
     AND win_date IS NOT NULL
     AND user_id = ?
  UNION ALL
  SELECT DATE_FORMAT(lost_date,     '%Y-%m'),
         product_value,
         'lost'
    FROM transactional
   WHERE lost = 1
     AND lost_date IS NOT NULL
     AND user_id = ?
) AS values_by_status
GROUP BY
  month
ORDER BY
  month
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);

// bind $userId six times (one per UNION)
$stmt->bind_param('iiiiii',
  $userId,
  $userId,
  $userId,
  $userId,
  $userId,
  $userId
);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['salestep'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2. personforecast: actual vs forecast for current user where win = 1
$sql = "
SELECT
  DATE_FORMAT(t.win_date, '%Y-%m') AS month,
  SUM(t.product_value)               AS win_value,
  u.forecast                         AS forecast
FROM transactional AS t
JOIN `user` AS u
  ON u.user_id = t.user_id
WHERE
  t.win = 1
  AND t.win_date IS NOT NULL
  AND t.user_id = ?
GROUP BY
  month,
  u.forecast
ORDER BY
  month
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    sendJsonError($mysqli->error);
}
$stmt->bind_param('i', $userId);
if (!$stmt->execute()) {
    sendJsonError($stmt->error);
}
$result = $stmt->get_result();
$output['winforecast'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ปิดการเชื่อมต่อ
$mysqli->close();

// ส่ง JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
