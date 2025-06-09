<?php
session_start();
require_once '../functions.php';
$mysqli = connectDb();

// ตรวจสอบว่ามีการส่งข้อมูลมาจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $add_Product = $_POST['Product'];
    $add_Product_detail = $_POST['Product_detail'];
    $add_Contact_start_date = $_POST['Contact_start_date'];
    $add_userId = $_POST['userId'];
    $add_Team = $_POST['Team'];
    $add_Product_value = $_POST['Product_value'];
    $add_company = $_POST['company'];
    $add_date_of_closing_of_sale = $_POST['date_of_closing_of_sale'];
    $add_sales_can_be_closed = $_POST['sales_can_be_closed'];
    $add_priority = $_POST['priority'];

    

    $add_Present   = isset($_POST['present'])   ? (int)$_POST['present']   : 0;
    $add_Budgeted  = isset($_POST['budgeted'])  ? (int)$_POST['budgeted']  : 0;
    $add_Tor       = isset($_POST['tor'])       ? (int)$_POST['tor']       : 0;
    $add_Bidding   = isset($_POST['bidding'])   ? (int)$_POST['bidding']   : 0;
    $add_Win       = isset($_POST['win'])       ? (int)$_POST['win']       : 0;
    $add_Lost      = isset($_POST['lost'])      ? (int)$_POST['lost']      : 0;

   

    $add_Present_date   = (!empty($_POST['present_date']))   ? $_POST['present_date']   : null;
    $add_Budgeted_date  = (!empty($_POST['budgeted_date']))  ? $_POST['budgeted_date']  : null;
    $add_Tor_date       = (!empty($_POST['tor_date']))       ? $_POST['tor_date']       : null;
    $add_Bidding_date   = (!empty($_POST['bidding_date']))   ? $_POST['bidding_date']   : null;
    $add_Win_date       = (!empty($_POST['win_date']))       ? $_POST['win_date']       : null;
    $add_Lost_date      = (!empty($_POST['lost_date']))      ? $_POST['lost_date']      : null;

    $add_remark = $_POST['remark'];

   
  
     // คำสั่ง SQL company
    $sqlcompany = "SELECT company_id FROM company_catalog WHERE company = '$add_company' ";
    $companyid = $mysqli->query($sqlcompany);
    $row = $companyid->fetch_assoc();
    $add_company_id = $row['company_id'];
      // คำสั่ง SQL Product
    $sqlpro = "SELECT product_id FROM product_group WHERE product = '$add_Product' ";
    $productid = $mysqli->query($sqlpro);
    $row = $productid->fetch_assoc();
    $add_product_id = $row['product_id'];

    $sqlteam = "SELECT team_id FROM team_catalog  WHERE team = '$add_Team' ";
    $industryid = $mysqli->query($sqlteam);
    $row = $industryid->fetch_assoc();
    $add_team_id = $row['team_id'];
     // คำสั่ง SQL priority_id
    $sqlpriority = "SELECT priority_id FROM priority_level  WHERE priority = '$add_priority' ";
    $priorityid = $mysqli->query($sqlpriority);
    $row = $priorityid->fetch_assoc();
    $add_priority_id = $row['priority_id'];

    
     // คำสั่ง SQL เพื่อเพิ่มข้อมูล
    //$sql = "INSERT INTO company_catalog (company, Industry_id) VALUES (?,?)";
    $sql = "INSERT INTO transactional (
                    user_id, 
                    company_id, 
                    Product_id, 
                    Product_detail,  
                    present,
                    present_date,
                    budgeted,
                    budgeted_date,
                    tor,
                    tor_date,
                    bidding,
                    bidding_date,
                    win,
                    win_date,
                    lost,
                    lost_date,
                    team_id, 
                    contact_start_date, 
                    date_of_closing_of_sale, 
                    sales_can_be_close,
                    priority_id, 
                    product_value, 
                    remark, 
                    timestamp) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, current_timestamp());";

    // เตรียมคำสั่ง SQL
    $stmt = $mysqli->prepare($sql);

    // ผูกค่าตัวแปรกับคำสั่ง SQL
    $stmt->bind_param("iiisisisisisisisisssiis", 
                  $add_userId, 
                  $add_company_id,
                  $add_product_id, 
                  $add_Product_detail,   
                  $add_Present,
                  $add_Present_date,
                  $add_Budgeted,
                  $add_Budgeted_date,
                  $add_Tor,
                  $add_Tor_date,
                  $add_Bidding,
                  $add_Bidding_date,
                  $add_Win,
                  $add_Win_date,
                  $add_Lost,
                  $add_Lost_date,
                  $add_team_id, 
                  $add_Contact_start_date, 
                  $add_date_of_closing_of_sale, 
                  $add_sales_can_be_closed, 
                  $add_priority_id, 
                  $add_Product_value, 
                  $add_remark);  // s = string, i = integer

    // เรียกใช้คำสั่ง SQL และตรวจสอบการเพิ่มข้อมูล
    if ($stmt->execute()) {
        echo "ข้อมูลถูกเพิ่มสำเร็จ!";
        header("location: adduser01.php");
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$mysqli->close();
?>
