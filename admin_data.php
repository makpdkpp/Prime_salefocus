<?php
require_once 'functions.php';
session_start();

// ตรวจสอบว่าล็อกอินและสิทธิ์เป็น admin (role_id 1)"เเก้พี่มาเล่นๆ" พพพพ rrrrr
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
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

// ฟังก์ชันรัน query คืนค่าเดียว (scalar) หรือ 0
function querySingle($db, $sql) {
    $res = $db->query($sql);
    if (!$res) {
        sendJsonError($db->error);
    }
    $row = $res->fetch_row();
    $value = $row[0] ?? 0;
    $res->free();
    return $value;
}

// ฟังก์ชันรัน query คืนหลายแถว
function queryList($db, $sql) {
    $res = $db->query($sql);
    if (!$res) {
        sendJsonError($db->error);
    }
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    $res->free();
    return $data;
}

$output = [];

// 1. estimatevalue (รวมทุก product_value)
$output['estimatevalue'] = querySingle($mysqli,
    "SELECT COALESCE(SUM(product_value),0) FROM transactional "
);

// 2. winvalue (sum product_value where win = 1)
$output['winvalue'] = querySingle($mysqli,
    "SELECT COALESCE(SUM(product_value),0) FROM transactional WHERE win = 1"
);

// 3. wincount (จำนวนรายการ Win เมื่อ win = 1)
$output['wincount'] = querySingle($mysqli,
    "SELECT COUNT(*) FROM transactional WHERE win = 1"
);

// 3.2. wincount (จำนวนรายการ Lost เมื่อ lost = 1)
$output['lostcount'] = querySingle($mysqli,
    "SELECT COUNT(*) FROM transactional WHERE lost = 1"
);

// 4. salestatus (สรุปสถานะต่างๆ แยกเดือน พร้อม Lost)
$output['salestatus'] = queryList($mysqli,
    "SELECT
  month,
  SUM(type = 'present')   AS present_count,
  SUM(type = 'budgeted')  AS budgeted_count,
  SUM(type = 'tor')        AS tor_count,
  SUM(type = 'bidding')    AS bidding_count,
  SUM(type = 'win')        AS win_count,
  SUM(type = 'lost')       AS lost_count
FROM (
  SELECT DATE_FORMAT(present_date,  '%Y-%m') AS month, 'present'  AS type
    FROM transactional
   WHERE present   = 1 AND present_date   IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(budgeted_date, '%Y-%m'), 'budgeted'
    FROM transactional
   WHERE budgeted  = 1 AND budgeted_date  IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(tor_date,      '%Y-%m'), 'tor'
    FROM transactional
   WHERE tor       = 1 AND tor_date      IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(bidding_date,  '%Y-%m'), 'bidding'
    FROM transactional
   WHERE bidding   = 1 AND bidding_date  IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(win_date,      '%Y-%m'), 'win'
    FROM transactional
   WHERE win       = 1 AND win_date      IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(lost_date,     '%Y-%m'), 'lost'
    FROM transactional
   WHERE lost      = 1 AND lost_date     IS NOT NULL
) AS events
GROUP BY
  month
ORDER BY
  month"
);




// 7. sumbyperson (ยอดรวมต่อผู้ใช้)
$output['sumbyperson'] = queryList($mysqli,
    "SELECT
        u.nname,
        COALESCE(SUM(t.product_value),0) AS total_value
     FROM `user` u
     LEFT JOIN transactional t ON t.user_id = u.user_id AND t.win = 1
     GROUP BY u.nname
     ORDER BY u.nname"
);

// 7. countbyperson (จำนวนขายต่อผู้ใช้)
$output['countbyperson'] = queryList($mysqli,
    "SELECT
        u.nname,
        COALESCE(COUNT(t.product_value),0) AS count_value
     FROM `user` u
     LEFT JOIN transactional t ON t.user_id = u.user_id AND t.win = 1
     GROUP BY u.nname
     ORDER BY u.nname"
);

// 8. sumbyperteam (ยอดรวมต่อทีม)
$output['sumbyperteam'] = queryList($mysqli,
    "SELECT
        tc.team,
        COALESCE(SUM(t.product_value),0) AS sumvalue
     FROM team_catalog tc
     LEFT JOIN transactional t ON t.team_id = tc.team_id AND t.win = 1
     GROUP BY tc.team_id
     ORDER BY tc.team"
);

// 9. salestatusvalue (เปรียบเทียบ product_value กับแต่ละสถานะในแต่ละเดือน)
$output['salestatusvalue'] = queryList($mysqli,
    "SELECT
  month,
  SUM(CASE WHEN type = 'present'  THEN value ELSE 0 END) AS present_value,
  SUM(CASE WHEN type = 'budgeted' THEN value ELSE 0 END) AS budgeted_value,
  SUM(CASE WHEN type = 'tor'      THEN value ELSE 0 END) AS tor_value,
  SUM(CASE WHEN type = 'bidding'  THEN value ELSE 0 END) AS bidding_value,
  SUM(CASE WHEN type = 'win'      THEN value ELSE 0 END) AS win_value,
  SUM(CASE WHEN type = 'lost'     THEN value ELSE 0 END) AS lost_value
FROM (
  SELECT DATE_FORMAT(present_date,  '%Y-%m') AS month, product_value AS value, 'present'  AS type
    FROM transactional
   WHERE present   = 1 AND present_date   IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(budgeted_date, '%Y-%m'), product_value, 'budgeted'
    FROM transactional
   WHERE budgeted  = 1 AND budgeted_date  IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(tor_date,      '%Y-%m'), product_value, 'tor'
    FROM transactional
   WHERE tor       = 1 AND tor_date      IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(bidding_date,  '%Y-%m'), product_value, 'bidding'
    FROM transactional
   WHERE bidding   = 1 AND bidding_date  IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(win_date,      '%Y-%m'), product_value, 'win'
    FROM transactional
   WHERE win       = 1 AND win_date      IS NOT NULL
  UNION ALL
  SELECT DATE_FORMAT(lost_date,     '%Y-%m'), product_value, 'lost'
    FROM transactional
   WHERE lost      = 1 AND lost_date     IS NOT NULL
) AS values_by_status
GROUP BY
  month
ORDER BY
  month"
);

// 10. saleforecast (saleforecast)
$output['saleforecast'] = queryList($mysqli,
    "SELECT
	u.forecast AS Target,
  SUM(t.product_value) AS Forecast,
  SUM(
    CASE WHEN t.win = 1
         THEN t.product_value
         ELSE 0
    END
  ) AS Win,
  u.nname
FROM transactional t
JOIN `user` u ON u.user_id = t.user_id
GROUP by u.nname"
);

// 11. Productortderbywinrate (productwinrate)
$output['productwinrate'] = queryList($mysqli,
    "SELECT 
  t.Product_detail AS Product,
  p.priority
FROM 
  transactional AS t
join
  priority_level AS p 
  ON t.priority_id = p.priority_id
ORDER BY 
  t.priority_id DESC"
);

// 12. TOP10ProductGroup 
$output['TopProductGroup'] = queryList($mysqli,
    "SELECT
  transactional.Product_id,
  product_group.product,
  SUM(product_value) AS sum_value
FROM
  transactional
  JOIN product_group on transactional.Product_id = product_group.product_id
GROUP BY
  transactional.Product_id
ORDER BY
  sum_value DESC
LIMIT 10"
);

// 13. Top10custopmer
$output['TopCustopmer'] = queryList($mysqli,
    "SELECT
  company_catalog.company,
  SUM(product_value) AS sum_value
FROM
  transactional
  JOIN company_catalog on transactional.company_id = company_catalog.company_id
GROUP BY
  company_catalog.company
ORDER BY
  sum_value DESC
LIMIT 10"
);

$mysqli->close();

// ส่ง JSON response
header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
