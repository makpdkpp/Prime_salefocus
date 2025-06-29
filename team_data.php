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

// ตรวจสอบ transactioonal_team        
$q = "SELECT team_id FROM transactional_team t WHERE t.user_id = $userId";
$res = $mysqli->query($q);
$teamIDs = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $teamIDs[] = (int)$row['team_id'];
    }
}

// เตรียม output array
$output = [];

// 1. sumstep per level for current user
$sql = "
SELECT
  month,
  SUM(CASE WHEN type = 'นำเสนอ Solution'  THEN value ELSE 0 END) AS present_value,
  SUM(CASE WHEN type = 'ตั้งงบประมาณ'     THEN value ELSE 0 END) AS budgeted_value,
  SUM(CASE WHEN type = 'ร่าง TOR'         THEN value ELSE 0 END) AS tor_value,
  SUM(CASE WHEN type = 'Bidding '         THEN value ELSE 0 END) AS bidding_value,
  SUM(CASE WHEN type = 'WIN'              THEN value ELSE 0 END) AS win_value,
  SUM(CASE WHEN type = 'LOST'             THEN value ELSE 0 END) AS lost_value
FROM (
  SELECT DATE_FORMAT(ts.date, '%Y-%m') AS month,
         t.product_value AS value,
         s.level AS type
    FROM transactional_step ts
    JOIN transactional t ON t.transac_id = ts.transac_id
    JOIN step s ON s.level_id = ts.level_id
   WHERE t.user_id = ?
     AND ts.level_id IN (1,2,3,4,5,7)
     AND ts.date IS NOT NULL
) AS values_by_status
GROUP BY
  month
ORDER BY
  month
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);

// bind $userId one time (query เดียว)
$stmt->bind_param('i', $userId);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['salestep'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2. personforecast: actual vs forecast for current user
$sql = "
SELECT
  SUM(t.product_value) AS Forecast,
  u.forecast AS Target,
  SUM(CASE WHEN ts.level_id = 5 THEN t.product_value ELSE 0 END) AS Win
FROM transactional_step ts
JOIN transactional t ON t.transac_id = ts.transac_id
JOIN `user` u ON u.user_id = t.user_id
WHERE t.user_id = ?
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);
$stmt->bind_param('i', $userId);
if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['winforecast'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 3. sumvaluepercent by person (ใช้ transactional_step)

$output['teamsumvaluepercent'] = [];
if (!empty($teamIDs)) {
    $in = implode(',', array_fill(0, count($teamIDs), '?'));
    $sql = "
    SELECT
      p.product,
      SUM(t.product_value) AS sum_value
    FROM transactional_step ts
    JOIN transactional t ON t.transac_id = ts.transac_id
    JOIN product_group p ON t.Product_id = p.product_id
    JOIN transactional_team ON transactional_team.team_id IN ($in)
    GROUP BY t.Product_id
    ";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) sendJsonError($mysqli->error);
    $types = str_repeat('i', count($teamIDs));
    $stmt->bind_param($types, ...$teamIDs);
    if (!$stmt->execute()) sendJsonError($stmt->error);
    $result = $stmt->get_result();
    $output['teamsumvaluepercent'] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
// 4. sumvaluepercent by team (ใช้ transactional_team)
$output['sumvaluepercent_team'] = [];
if (!empty($teamIDs)) {
    $sql = "
    SELECT
          u.forecast AS Target,
          SUM(t.product_value) AS Forecast,
          SUM(CASE WHEN s.level = 'WIN' THEN t.product_value ELSE 0 END) AS Win,
          u.nname,
          team_catalog.team
        FROM transactional_step ts
        JOIN transactional t ON t.transac_id = ts.transac_id
        JOIN `user` u ON u.user_id = t.user_id
        JOIN step s ON s.level_id = ts.level_id
        JOIN transactional_team ON transactional_team.team_id = ?
        JOIN team_catalog ON team_catalog.team_id = transactional_team.team_id
        GROUP BY u.nname
    ";
    foreach ($teamIDs as $teamID) {
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) sendJsonError($mysqli->error);
        $stmt->bind_param('i', $teamID);
        if (!$stmt->execute()) sendJsonError($stmt->error);
        $result = $stmt->get_result();
        $teamData = $result->fetch_all(MYSQLI_ASSOC);
        if ($teamData) {
            $output['sumvaluepercent_team'][] = [
                'team_id' => $teamID,
                'data' => $teamData
            ];
        }
        $stmt->close();
    }
}

// 5. WinTeam by person 
$output['WinTeam'] = [];
if (!empty($teamIDs)) {
    $in = implode(',', array_fill(0, count($teamIDs), '?'));
    $sql = "
    SELECT
          SUM(CASE WHEN s.level = 'WIN' THEN t.product_value ELSE 0 END) AS Win,
          u.nname
        FROM transactional_step ts
        JOIN transactional t ON t.transac_id = ts.transac_id
        JOIN `user` u ON u.user_id = t.user_id
        JOIN step s ON s.level_id = ts.level_id
        JOIN transactional_team ON transactional_team.team_id IN ($in)
        GROUP BY u.nname
    ";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) sendJsonError($mysqli->error);
    $types = str_repeat('i', count($teamIDs));
    $stmt->bind_param($types, ...$teamIDs);
    if (!$stmt->execute()) sendJsonError($stmt->error);
    $result = $stmt->get_result();
    $output['WinTeam'] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// 6. sumstep per level for current user Team
$output['salestepteam'] = [];
if (!empty($teamIDs)) {
    $in = implode(',', array_fill(0, count($teamIDs), '?'));
    $sql = "
    SELECT
      month,
      SUM(CASE WHEN type = 'นำเสนอ Solution'  THEN value ELSE 0 END) AS present_value,
      SUM(CASE WHEN type = 'ตั้งงบประมาณ'     THEN value ELSE 0 END) AS budgeted_value,
      SUM(CASE WHEN type = 'ร่าง TOR'         THEN value ELSE 0 END) AS tor_value,
      SUM(CASE WHEN type = 'Bidding '         THEN value ELSE 0 END) AS bidding_value,
      SUM(CASE WHEN type = 'WIN'              THEN value ELSE 0 END) AS win_value,
      SUM(CASE WHEN type = 'LOST'             THEN value ELSE 0 END) AS lost_value
    FROM (
      SELECT DATE_FORMAT(ts.date, '%Y-%m') AS month,
             t.product_value AS value,
             s.level AS type
        FROM transactional_step ts
        JOIN transactional t ON t.transac_id = ts.transac_id
        JOIN step s ON s.level_id = ts.level_id
        JOIN transactional_team ON transactional_team.team_id IN ($in)
       WHERE ts.level_id IN (1,2,3,4,5,7)
         AND ts.date IS NOT NULL
    ) AS values_by_status
    GROUP BY month
    ORDER BY month
    ";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) sendJsonError($mysqli->error);
    $types = str_repeat('i', count($teamIDs));
    $stmt->bind_param($types, ...$teamIDs);
    if (!$stmt->execute()) sendJsonError($stmt->error);
    $result = $stmt->get_result();
    $output['salestepteam'] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// ปิดการเชื่อมต่อ
$mysqli->close();

// ส่ง JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
