<?php
require_once 'functions.php';
session_start();

// ตรวจสอบว่าล็อกอินและสิทธิ์เป็น admin (role_id 1)
if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 1) {
    header('Location: index.php');
    exit;
}

$mysqli = connectDb();

function sendJsonError($message) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Query Error: ' . $message], JSON_UNESCAPED_UNICODE);
    exit;
}

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

// 2. winvalue (sum product_value where latest step is WIN)
$output['winvalue'] = querySingle($mysqli,
    "SELECT COALESCE(SUM(t.product_value),0)
     FROM transactional t
     JOIN (
       SELECT ts.transac_id
       FROM transactional_step ts
       JOIN step s ON s.level_id = ts.level_id
       WHERE s.level = 5
         AND (ts.transacstep_id, ts.transac_id) IN (
           SELECT MAX(ts2.transacstep_id), ts2.transac_id
           FROM transactional_step ts2
           GROUP BY ts2.transac_id
         )
     ) wintrans ON wintrans.transac_id = t.transac_id"
);

// 3. wincount (จำนวนรายการที่ latest step เป็น WIN)
$output['wincount'] = querySingle($mysqli,
    "SELECT COUNT(*)
     FROM transactional t
     JOIN (
       SELECT ts.transac_id
       FROM transactional_step ts
       JOIN step s ON s.level_id = ts.level_id
       WHERE s.level = 5
         AND (ts.transacstep_id, ts.transac_id) IN (
           SELECT MAX(ts2.transacstep_id), ts2.transac_id
           FROM transactional_step ts2
           GROUP BY ts2.transac_id
         )
     ) wintrans ON wintrans.transac_id = t.transac_id"
);

// 3.2. lostcount (จำนวนรายการที่ latest step เป็น LOST)
$output['lostcount'] = querySingle($mysqli,
    "SELECT COUNT(*)
     FROM transactional t
     JOIN (
       SELECT ts.transac_id
       FROM transactional_step ts
       JOIN step s ON s.level_id = ts.level_id
       WHERE s.level = 6
         AND (ts.transacstep_id, ts.transac_id) IN (
           SELECT MAX(ts2.transacstep_id), ts2.transac_id
           FROM transactional_step ts2
           GROUP BY ts2.transac_id
         )
     ) losttrans ON losttrans.transac_id = t.transac_id"
);

// 4. salestatus (pivot รายเดือน แยก step)
$output['salestatus'] = queryList($mysqli,
    "SELECT
      month,
      SUM(type = '1.นำเสนอ Solution') AS present_count,
      SUM(type = '2.ตั้งงบประมาณ') AS budgeted_count,
      SUM(type = '3.ร่าง TOR') AS tor_count,
      SUM(type = '4.Bidding / เสนอราคา') AS bidding_count,
      SUM(type = '5.WIN') AS win_count,
      SUM(type = '6.LOST') AS lost_count
    FROM (
      SELECT DATE_FORMAT(ts.date, '%Y-%m') AS month, s.level AS type
      FROM transactional_step ts
      JOIN step s ON s.level_id = ts.level_id
      WHERE ts.date IS NOT NULL
    ) AS events
    GROUP BY month
    ORDER BY month"
);

// 7. sumbyperson (ยอดรวมต่อผู้ใช้ เฉพาะที่ latest step เป็น WIN)
$output['sumbyperson'] = queryList($mysqli,
    "SELECT
        u.nname,
        COALESCE(SUM(t.product_value),0) AS total_value
     FROM `user` u
     LEFT JOIN transactional t ON t.user_id = u.user_id
     LEFT JOIN (
       SELECT ts.transac_id
       FROM transactional_step ts
       JOIN step s ON s.level_id = ts.level_id
       WHERE s.level = '5.WIN'
         AND (ts.transacstep_id, ts.transac_id) IN (
           SELECT MAX(ts2.transacstep_id), ts2.transac_id
           FROM transactional_step ts2
           GROUP BY ts2.transac_id
         )
     ) wintrans ON wintrans.transac_id = t.transac_id
     WHERE wintrans.transac_id IS NOT NULL
     GROUP BY u.nname
     ORDER BY u.nname"
);

// 7. countbyperson (จำนวนขายต่อผู้ใช้ เฉพาะที่ latest step เป็น WIN)
$output['countbyperson'] = queryList($mysqli,
    "SELECT
        u.nname,
        COALESCE(COUNT(t.product_value),0) AS count_value
     FROM `user` u
     LEFT JOIN transactional t ON t.user_id = u.user_id
     LEFT JOIN (
       SELECT ts.transac_id
       FROM transactional_step ts
       JOIN step s ON s.level_id = ts.level_id
       WHERE s.level = '5.WIN'
         AND (ts.transacstep_id, ts.transac_id) IN (
           SELECT MAX(ts2.transacstep_id), ts2.transac_id
           FROM transactional_step ts2
           GROUP BY ts2.transac_id
         )
     ) wintrans ON wintrans.transac_id = t.transac_id
     WHERE wintrans.transac_id IS NOT NULL
     GROUP BY u.nname
     ORDER BY u.nname"
);

// 8. sumbyperteam (ยอดรวมต่อทีม เฉพาะที่ latest step เป็น WIN)
$output['sumbyperteam'] = queryList($mysqli,
    "SELECT
        tc.team,
        COALESCE(SUM(t.product_value),0) AS sumvalue
     FROM team_catalog tc
     LEFT JOIN transactional t ON t.team_id = tc.team_id
     LEFT JOIN (
       SELECT ts.transac_id
       FROM transactional_step ts
       JOIN step s ON s.level_id = ts.level_id
       WHERE s.level = '5.WIN'
         AND (ts.transacstep_id, ts.transac_id) IN (
           SELECT MAX(ts2.transacstep_id), ts2.transac_id
           FROM transactional_step ts2
           GROUP BY ts2.transac_id
         )
     ) wintrans ON wintrans.transac_id = t.transac_id
     WHERE wintrans.transac_id IS NOT NULL
     GROUP BY tc.team_id
     ORDER BY tc.team"
);

// 9. salestatusvalue (pivot รายเดือน แยก step, sum product_value)
$output['salestatusvalue'] = queryList($mysqli,
    "SELECT
      month,
      SUM(CASE WHEN type = '1.นำเสนอ Solution' THEN value ELSE 0 END) AS present_value,
      SUM(CASE WHEN type = '2.ตั้งงบประมาณ' THEN value ELSE 0 END) AS budgeted_value,
      SUM(CASE WHEN type = '3.ร่าง TOR' THEN value ELSE 0 END) AS tor_value,
      SUM(CASE WHEN type = '4.Bidding / เสนอราคา' THEN value ELSE 0 END) AS bidding_value,
      SUM(CASE WHEN type = '5.WIN' THEN value ELSE 0 END) AS win_value,
      SUM(CASE WHEN type = '6.LOST' THEN value ELSE 0 END) AS lost_value
    FROM (
      SELECT DATE_FORMAT(ts.date, '%Y-%m') AS month, t.product_value AS value, s.level AS type
      FROM transactional_step ts
      JOIN transactional t ON t.transac_id = ts.transac_id
      JOIN step s ON s.level_id = ts.level_id
      WHERE ts.date IS NOT NULL
    ) AS values_by_status
    GROUP BY month
    ORDER BY month"
);

// 10. saleforecast (Forecast, Target, Win per user)
$output['saleforecast'] = queryList($mysqli,
    "SELECT
      u.forecast AS Target,
      COALESCE(uf.TotalForecast, 0) AS Forecast,
      COALESCE(uw.TotalWin, 0) AS Win,
      u.nname
    FROM `user` u
    INNER JOIN ( -- << เปลี่ยนจาก LEFT JOIN เป็น INNER JOIN ตรงนี้
        -- Subquery 1: คำนวณยอด Forecast ทั้งหมด
        SELECT
            user_id,
            SUM(product_value) AS TotalForecast
        FROM transactional
        GROUP BY user_id
    ) AS uf ON u.user_id = uf.user_id
    LEFT JOIN (
        -- Subquery 2: คำนวณยอด Win
        SELECT
            t.user_id,
            SUM(t.product_value) AS TotalWin
        FROM transactional t
        JOIN transactional_step ts ON t.transac_id = ts.transac_id
        JOIN step s ON s.level_id = ts.level_id
        WHERE s.level = '5.WIN'
        GROUP BY t.user_id
    ) AS uw ON u.user_id = uw.user_id"
);

// 11. Productortderbywinrate (productwinrate, latest step is WIN)
$output['productwinrate'] = queryList($mysqli,
    "SELECT 
      t.Product_detail AS Product,
      p.priority
    FROM transactional t
    JOIN priority_level p ON t.priority_id = p.priority_id
    JOIN (
      SELECT ts.transac_id
      FROM transactional_step ts
      JOIN step s ON s.level_id = ts.level_id
      WHERE s.level = '5.WIN'
        AND (ts.transacstep_id, ts.transac_id) IN (
          SELECT MAX(ts2.transacstep_id), ts2.transac_id
          FROM transactional_step ts2
          GROUP BY ts2.transac_id
        )
    ) wintrans ON wintrans.transac_id = t.transac_id
    ORDER BY t.priority_id DESC"
);

// 12. TOP10ProductGroup (sum by product group, all steps)
$output['TopProductGroup'] = queryList($mysqli,
    "SELECT
      t.Product_id,
      pg.product,
      SUM(t.product_value) AS sum_value
    FROM transactional t
    JOIN product_group pg ON t.Product_id = pg.product_id
    GROUP BY t.Product_id
    ORDER BY sum_value DESC
    LIMIT 10"
);

// 13. Top10custopmer (sum by company, all steps)
$output['TopCustopmer'] = queryList($mysqli,
    "SELECT
    cc.company,
    SUM(t.product_value) AS sum_value
FROM
    transactional t
JOIN
    company_catalog cc ON t.company_id = cc.company_id
WHERE
    EXISTS (
        SELECT 1
        FROM transactional_step ts
        WHERE ts.transac_id = t.transac_id AND ts.level_id = 5
    )
GROUP BY
    cc.company
ORDER BY
    sum_value DESC
LIMIT 10;"
);

$mysqli->close();

header('Content-Type: application/json; charset=utf-8');
echo json_encode($output, JSON_UNESCAPED_UNICODE);


