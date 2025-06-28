<?php
// Migration script: transactional -> transactional_step
require_once '../functions.php';
$mysqli = connectDb();

// Map: field => level_id
$stepMap = [
    'present'   => 1,
    'budgeted'  => 2,
    'tor'       => 3,
    'bidding'   => 4,
    'win'       => 5,
    'lost'      => 7,
];
$dateMap = [
    'present'   => 'present_date',
    'budgeted'  => 'budgeted_date',
    'tor'       => 'tor_date',
    'bidding'   => 'bidding_date',
    'win'       => 'win_date',
    'lost'      => 'lost_date',
];

$sql = "SELECT transac_id, present, present_date, budgeted, budgeted_date, tor, tor_date, bidding, bidding_date, win, win_date, lost, lost_date FROM transactional";
$res = $mysqli->query($sql);
if (!$res) die('Query failed: ' . $mysqli->error);
$count = 0;
while ($row = $res->fetch_assoc()) {
    foreach ($stepMap as $field => $level_id) {
        if (!empty($row[$field]) && $row[$field] == 1) {
            $dateField = $dateMap[$field];
            $date = $row[$dateField] ?? null;
            if ($date && $date !== '0000-00-00') {
                $stmt = $mysqli->prepare("INSERT INTO transactional_step (transac_id, level_id, date) VALUES (?, ?, ?)");
                $stmt->bind_param('iis', $row['transac_id'], $level_id, $date);
                $stmt->execute();
                $stmt->close();
                $count++;
            }
        }
    }
}
echo "Migration complete. Inserted $count records.";
