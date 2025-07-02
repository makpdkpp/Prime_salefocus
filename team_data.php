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
$q = "SELECT team_id FROM transactional_team WHERE transactional_team.user_id = $userId";
$res = $mysqli->query($q);
if (!$res) {
    sendJsonError($mysqli->error);
}
$teamIdArr = [];
while ($row = $res->fetch_assoc()) {
    $teamIdArr[] = (int)$row['team_id'];
}

// เตรียม output array
$output = [];

// 1. sumstep per level for current user
$sql = "
SELECT
  month,
  SUM(CASE WHEN type = '1.นำเสนอ Solution'  THEN value ELSE 0 END) AS present_value,
  SUM(CASE WHEN type = '2.ตั้งงบประมาณ'     THEN value ELSE 0 END) AS budgeted_value,
  SUM(CASE WHEN type = '3.ร่าง TOR'         THEN value ELSE 0 END) AS tor_value,
  SUM(CASE WHEN type = '4.Bidding '         THEN value ELSE 0 END) AS bidding_value,
  SUM(CASE WHEN type = '5.WIN'              THEN value ELSE 0 END) AS win_value,
  SUM(CASE WHEN type = '6.LOST'             THEN value ELSE 0 END) AS lost_value
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
$sql = "
SELECT
  t.Product_id,
  p.product,
  SUM(t.product_value) AS sum_value
FROM transactional_step ts
JOIN transactional t ON t.transac_id = ts.transac_id
JOIN product_group p ON t.Product_id = p.product_id
WHERE t.user_id = ?
GROUP BY t.Product_id
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);
$stmt->bind_param('i', $userId);
if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['sumvaluepercent'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();


// -------team
// 4. teamEstimatevalue 
$output['teamEstimatevalue'] = 0;
if (!empty($teamIdArr)) {
    // ถ้าต้องการรวมหลายทีม ให้ใช้ IN (...), ถ้าเอาแค่ทีมแรกใช้ $teamIdArr[0]
    $sql = "
    SELECT IFNULL(SUM(transactional.product_value), 0) AS total
    FROM transactional
    WHERE transactional.team_id = ?
    ";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) sendJsonError($mysqli->error);
    $stmt->bind_param('i', $teamIdArr[0]);
    if (!$stmt->execute()) sendJsonError($stmt->error);
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $output['teamEstimatevalue'] = $data['total'] ?? 0;
    $stmt->close();
}

// 4. teamWin" 

if (!empty($teamIdArr)) {
    // ถ้าต้องการรวมหลายทีม ให้ใช้ IN (...), ถ้าเอาแค่ทีมแรกใช้ $teamIdArr[0]
    $sql = "
   SELECT sum(transactional.product_value) AS total

    FROM transactional
    JOIN transactional_step on transactional_step.transac_id = transactional.transac_id
    WHERE transactional.team_id = ? and transactional_step.level_id = 5
    ";
  $stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);

// bind $userId one time (query เดียว)
$stmt->bind_param('i', $teamIdArr[0]);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$output['teamWinvalue'] = $data['total'] ?? 0;
$stmt->close();
}

// 4. teamCountWin" 
$sql = "
SELECT 
         COUNT(t.product_value) AS winCount
    FROM transactional_step ts
    JOIN transactional t ON t.transac_id = ts.transac_id
    JOIN step s ON s.level_id = ts.level_id
   WHERE t.team_id = ?
     AND ts.level_id IN (5)
     AND ts.date IS NOT NULL
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);
$stmt->bind_param('i', $teamIdArr[0]);
if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$output['teamWinCount'] = $data['winCount'] ?? 0;
$stmt->close();

// 4. teamCountLost" 
$sql = "
SELECT 
         COUNT(t.product_value) AS lostCount
    FROM transactional_step ts
    JOIN transactional t ON t.transac_id = ts.transac_id
    JOIN step s ON s.level_id = ts.level_id
   WHERE t.team_id = ?
     AND ts.level_id IN (7)
     AND ts.date IS NOT NULL
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);
$stmt->bind_param('i', $teamIdArr[0]);
if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$output['teamCountLost'] = $data['lostCount'] ?? 0;
$stmt->close();

// 5. sumstep per level for current user teamSalestepValue
$sql = "
SELECT
  month,
  SUM(CASE WHEN type = '1.นำเสนอ Solution'  THEN value ELSE 0 END) AS present_value,
  SUM(CASE WHEN type = '2.ตั้งงบประมาณ'     THEN value ELSE 0 END) AS budgeted_value,
  SUM(CASE WHEN type = '3.ร่าง TOR'         THEN value ELSE 0 END) AS tor_value,
  SUM(CASE WHEN type = '4.Bidding '         THEN value ELSE 0 END) AS bidding_value,
  SUM(CASE WHEN type = '5.WIN'              THEN value ELSE 0 END) AS win_value,
  SUM(CASE WHEN type = '6.LOST'             THEN value ELSE 0 END) AS lost_value
FROM (
  SELECT DATE_FORMAT(ts.date, '%Y-%m') AS month,
         t.product_value AS value,
         s.level AS type
    FROM transactional_step ts
    JOIN transactional t ON t.transac_id = ts.transac_id
    JOIN step s ON s.level_id = ts.level_id
   WHERE t.team_id = ?
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
$stmt->bind_param('i', $teamIdArr[0]);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['teamSalestepValue'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 6. sumstep per level for current user teamSalestep
$sql = "
SELECT
  month,
  -- นับจำนวนครั้งของแต่ละขั้นตอน
  SUM(CASE WHEN type = '1.นำเสนอ Solution' THEN 1 ELSE 0 END) AS present_count,
  SUM(CASE WHEN type = '2.ตั้งงบประมาณ'    THEN 1 ELSE 0 END) AS budgeted_count,
  SUM(CASE WHEN type = '3.ร่าง TOR'        THEN 1 ELSE 0 END) AS tor_count,
  SUM(CASE WHEN type = '4.Bidding '        THEN 1 ELSE 0 END) AS bidding_count,
  SUM(CASE WHEN type = '5.WIN'             THEN 1 ELSE 0 END) AS win_count,
  SUM(CASE WHEN type = '6.LOST'            THEN 1 ELSE 0 END) AS lost_count
FROM (
  SELECT
    DATE_FORMAT(ts.date, '%Y-%m') AS month,
    s.level AS type
  FROM transactional_step ts
  JOIN transactional t ON t.transac_id = ts.transac_id
  JOIN step s           ON s.level_id   = ts.level_id
  WHERE t.team_id = ?
    AND ts.level_id IN (1,2,3,4,5,7)
    AND ts.date IS NOT NULL
) AS values_by_status
GROUP BY month
ORDER BY month
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);

// bind $userId one time (query เดียว)
$stmt->bind_param('i', $teamIdArr[0]);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['teamSalestep'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 7. teamSumMonth ยอดขายรวมของทีม(มูลค่า)
$sql = "
SELECT
  DATE_FORMAT(ts.date, '%Y-%m') AS month,           
  IFNULL(SUM(t.product_value), 0) AS total          
FROM transactional AS t
INNER JOIN transactional_step AS ts
  ON t.transac_id = ts.transac_id                     
WHERE t.team_id = ?                                
GROUP BY DATE_FORMAT(ts.date, '%Y-%m')               
ORDER BY month
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);


$stmt->bind_param('i', $teamIdArr[0]);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['teamSumMonth'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 8. teamSumByPerson (ยอดรวมต่อบุคคล เฉพาะที่ latest step เป็น WIN)  
$sql = "
SELECT sum(transactional.product_value) AS total,
user.nname AS NAME
    FROM transactional
    JOIN transactional_step on transactional_step.transac_id = transactional.transac_id
    JOIN user on user.user_id = transactional.user_id
    WHERE transactional.team_id = ? and transactional_step.level_id = 5
    GROUP by NAME
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);


$stmt->bind_param('i', $teamIdArr[0]);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['teamSumByPerson'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();        

// 9. teamSaleforecast กราฟเปรียบเทียบ Target/Forecast/Win  
$sql = "
SELECT
  u.forecast AS Target,
  SUM(t.product_value) AS Forecast,
  SUM(
    CASE WHEN s.level = '5.WIN'
         THEN t.product_value
         ELSE 0
    END
  ) AS Win,
  u.nname
FROM transactional_step ts
JOIN transactional t
  ON t.transac_id = ts.transac_id
  AND t.team_id = ?         -- กรองเฉพาะทีมที่ 4
JOIN `user` u
  ON u.user_id = t.user_id
JOIN step s
  ON s.level_id = ts.level_id
GROUP BY u.nname;
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) sendJsonError($mysqli->error);


$stmt->bind_param('i', $teamIdArr[0]);

if (!$stmt->execute()) sendJsonError($stmt->error);
$result = $stmt->get_result();
$output['teamSaleforecast'] = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();              


// ปิดการเชื่อมต่อ
$mysqli->close();

// ส่ง JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
