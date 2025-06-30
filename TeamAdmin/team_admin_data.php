<?php
// FILE: TeamAdmin/team_admin_data.php (ฉบับแก้ไขสมบูรณ์)
header('Content-Type: application/json');
require_once '../functions.php'; 
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role_id'] !== 2) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$db = connectDb();
$team_head_id = (int)$_SESSION['user_id'];

// สร้างเงื่อนไข WHERE กลาง สำหรับการหา user_id ทั้งหมดในทีมของ Head
$where_clause_for_users_in_team = "
    WHERE t.user_id IN (
        SELECT DISTINCT user_id
        FROM transactional_team
        WHERE team_id IN (
            SELECT team_id FROM transactional_team WHERE user_id = ?
        )
    )
";

// --- Summary Boxes ---
$sql_summary = "SELECT 
                    COALESCE(SUM(CASE WHEN s.level NOT IN ('Lost', 'Win') THEN t.product_value ELSE 0 END), 0) as estimatevalue,
                    COALESCE(SUM(CASE WHEN s.level = 'Win' THEN t.product_value ELSE 0 END), 0) as winvalue,
                    COUNT(CASE WHEN s.level = 'Win' THEN 1 END) as wincount,
                    COUNT(CASE WHEN s.level = 'Lost' THEN 1 END) as lostcount
                FROM transactional t
                JOIN step s ON t.Step_id = s.level_id
                $where_clause_for_users_in_team";
$stmt = $db->prepare($sql_summary);
$stmt->bind_param('i', $team_head_id);
$stmt->execute();
$summary_result = $stmt->get_result()->fetch_assoc();

// --- ยอดขายรายคนในทีม (sumbyperson) ---
// แก้ไขให้ WHERE ที่ u.user_id แทน
$sql_person = "SELECT u.nname, SUM(t.product_value) as total_value 
               FROM transactional t
               JOIN user u ON t.user_id = u.user_id
               WHERE u.user_id IN (
                    SELECT DISTINCT user_id
                    FROM transactional_team
                    WHERE team_id IN (
                        SELECT team_id FROM transactional_team WHERE user_id = ?
                    )
               ) AND t.Step_id = (SELECT level_id FROM step WHERE level = 'Win')
               GROUP BY u.nname";
$stmt = $db->prepare($sql_person);
$stmt->bind_param('i', $team_head_id);
$stmt->execute();
$person_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- ยอดขายรวมของทีม (กราฟแท่งเดียว) ---
$sql_team = "SELECT tc.team, SUM(t.product_value) as sumvalue
            FROM transactional t
            JOIN team_catalog tc ON t.team_id = tc.team_id
            $where_clause_for_users_in_team AND t.Step_id = (SELECT level_id FROM step WHERE level = 'Win')
            GROUP BY tc.team";
$stmt = $db->prepare($sql_team);
$stmt->bind_param('i', $team_head_id);
$stmt->execute();
$team_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- สถานะการขายในแต่ละขั้นตอน (นับจำนวน) ---
$sql_status_count = "SELECT 
                        MONTHNAME(t.contact_start_date) as month,
                        COUNT(CASE WHEN s.level = 'Present' THEN 1 END) as present_count,
                        COUNT(CASE WHEN s.level = 'Budget' THEN 1 END) as budgeted_count,
                        COUNT(CASE WHEN s.level = 'TOR' THEN 1 END) as tor_count,
                        COUNT(CASE WHEN s.level = 'Bidding' THEN 1 END) as bidding_count,
                        COUNT(CASE WHEN s.level = 'Win' THEN 1 END) as win_count,
                        COUNT(CASE WHEN s.level = 'Lost' THEN 1 END) as lost_count
                     FROM transactional t
                     JOIN step s ON t.Step_id = s.level_id
                     $where_clause_for_users_in_team
                     GROUP BY month ORDER BY t.contact_start_date";
$stmt = $db->prepare($sql_status_count);
$stmt->bind_param('i', $team_head_id);
$stmt->execute();
$status_count_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- ประมาณการมูลค่าในแต่ละขั้นตอน (นับมูลค่า) ---
$sql_status_value = "SELECT
                        MONTHNAME(t.contact_start_date) as month,
                        SUM(CASE WHEN s.level = 'Present' THEN t.product_value ELSE 0 END) as present_value,
                        SUM(CASE WHEN s.level = 'Budget' THEN t.product_value ELSE 0 END) as budgeted_value,
                        SUM(CASE WHEN s.level = 'TOR' THEN t.product_value ELSE 0 END) as tor_value,
                        SUM(CASE WHEN s.level = 'Bidding' THEN t.product_value ELSE 0 END) as bidding_value,
                        SUM(CASE WHEN s.level = 'Win' THEN t.product_value ELSE 0 END) as win_value,
                        SUM(CASE WHEN s.level = 'Lost' THEN t.product_value ELSE 0 END) as lost_value
                    FROM transactional t
                    JOIN step s ON t.Step_id = s.level_id
                    $where_clause_for_users_in_team
                    GROUP BY month ORDER BY t.contact_start_date";
$stmt = $db->prepare($sql_status_value);
$stmt->bind_param('i', $team_head_id);
$stmt->execute();
$status_value_result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// รวบรวมข้อมูลทั้งหมดเป็น Array
$data = [
    'estimatevalue'   => $summary_result['estimatevalue'],
    'winvalue'        => $summary_result['winvalue'],
    'wincount'        => $summary_result['wincount'],
    'lostcount'       => $summary_result['lostcount'],
    'sumbyperson'     => $person_result,
    'sumbyperteam'    => $team_result,
    'salestatus'      => $status_count_result,
    'salestatusvalue' => $status_value_result,
];

// ส่งข้อมูลกลับเป็น JSON
echo json_encode($data);

$db->close();
?>