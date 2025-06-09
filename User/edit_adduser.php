<?php
session_start();
include("../connect.php");

// ดึงข้อมูลของผู้ใช้จากเซสชัน
$email = $_SESSION['email'];

// ตรวจสอบว่าอีเมล์ในเซสชันมีอยู่หรือไม่
if (isset($email)) {
    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล โดยการ JOIN ตาราง users และ profile_user
    $sql = "SELECT users.id, users.email, profile_user.team, profile_user.F_name FROM users 
            INNER JOIN profile_user ON users.email = profile_user.email 
            WHERE users.email = '$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];  // ID ของผู้ใช้ที่ล็อกอิน
        $team = $row['team'];
        $F_name = $row['F_name'];
        $table_name = "user_" . $user_id; // ชื่อตารางที่เก็บข้อมูลของผู้ใช้

        // รับค่า 'id' จาก URL
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = $_GET['id'];

            

            // ตรวจสอบว่า $id เป็นตัวเลข และเป็นข้อมูลของผู้ใช้ที่ล็อกอิน
            if (is_numeric($id)) {
                // ใช้ email หรือ user_id ที่เหมาะสมแทนการใช้ user_id ใน WHERE clause
                // เพิ่มเงื่อนไขในการตรวจสอบผู้ใช้
                $check_sql = "SELECT * FROM $table_name WHERE id = '$id' AND salesperson = '$F_name'";  // เช็คข้อมูลเฉพาะของผู้ใช้ที่ล็อกอิน
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) {
                    $row = $check_result->fetch_assoc(); // ดึงข้อมูลแถวที่ต้องการแก้ไข
                } else {
                    echo "ไม่พบข้อมูลที่ต้องการแก้ไข หรือ คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้";
                    echo $table_name;
                    echo $id;
                    echo $email;
                    exit;
                }
            } else {
                echo "ID ที่ส่งมาผิดพลาด";
                exit;
            }
        } else {
            echo "ไม่พบการระบุ ID ใน URL";
            exit;
        }

        // เช็คว่าเมื่อกี้มีการกดปุ่มส่งข้อมูลหรือไม่
        if (isset($_POST['submit'])) {
            // รับค่าจากฟอร์ม
            $Product_list = $_POST['Product_list'];
            $Contact_start_date = $_POST['Contact_start_date'];
            $Product_value = $_POST['Product_value'];
            $date_of_closing_of_sale = $_POST['date_of_closing_of_sale'];
            $sales_can_be_closed = $_POST['sales_can_be_closed'];
            $Product = $_POST['Product'];
            $Level = $_POST['Level'];
            $add_office = $_POST['add_office'];
            $Priority = $_POST['Priority'];

            // สร้างคำสั่ง SQL สำหรับการอัปเดตข้อมูล
            $update_sql = "UPDATE $table_name 
                           SET Product_group = '$Product', 
                               Product_list = '$Product_list', 
                               Contact_start_date = '$Contact_start_date', 
                               step = '$Level', 
                               salesperson = '$F_name', 
                               Sales_team = '$team', 
                               Product_value = '$Product_value', 
                               company = '$add_office', 
                               date_of_closing_of_sale = '$date_of_closing_of_sale', 
                               sales_can_be_closed = '$sales_can_be_closed', 
                               Priority_level = '$Priority'
                           WHERE id = '$id'";  // ใช้ id จาก URL เป็นตัวเลือกในการอัปเดตข้อมูล

            // ตรวจสอบการอัปเดตข้อมูล
            if ($conn->query($update_sql) === TRUE) {
                echo "<script>alert('ข้อมูลถูกแก้ไขสำเร็จ'); window.location.href='../home_user.php';</script>";
            } else {
                echo "Error: " . $conn->error;
            }
        }
    } else {
        echo "ไม่พบข้อมูลผู้ใช้";
    }
} else {
    echo "กรุณาล็อกอินก่อนที่จะทำการแก้ไขข้อมูล";
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>AdminLTE 2 | Fixed Layout</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <style>
        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input[type="text"], input[type="email"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #4b03a4;
            color: #fff;
            padding: 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 45px;
        }

        button[type="submit"]:hover {
            background-color: #e6d5f8;
        }
    </style>
</head>
<body class="skin-blue fixed">
    <div class="container">
        <h2>แก้ไขข้อมูล</h2>
        <form method="POST">
            <label>กลุ่มสินค้า : </label>
            <select name="Product">
                <option value="<?php echo $row['Product_group']; ?>"><?php echo $row['Product_group']; ?></option>
                <?php
                    $product_industry = mysqli_query($conn, "SELECT id, Product FROM product_industry");
                    while($c = mysqli_fetch_array($product_industry)) {
                ?>
                    <option value="<?php echo $c['Product']?>"><?php echo $c['Product']?></option>
                <?php } ?>
            </select><br/>

            <label>รายการสินค้า : </label>
            <input type="text" name="Product_list" value="<?php echo $row['Product_list']; ?>"><br/>

            <label>วันที่ติดต่อ : </label>
            <input type="date" name="Contact_start_date" value="<?php echo $row['Contact_start_date']; ?>"><br/>

            <label>ขั้นตอน : </label>
            <select name="Level">
                <option value="<?php echo $row['step']; ?>"><?php echo $row['step']; ?></option>
                <?php
                    $step = mysqli_query($conn, "SELECT id, Level FROM step");
                    while($c = mysqli_fetch_array($step)) {
                ?>
                    <option value="<?php echo $c['Level']?>"><?php echo $c['Level']?></option>
                <?php } ?>
            </select><br/>

            <label>พนักงานขาย : </label>
            <p style="display: inline; font-size: 16px;"><?php echo $F_name; ?></p><br/>

            <label>ทีมขาย : </label>
            <p style="display: inline; font-size: 16px;"><?php echo $team; ?></p><br/>

            <label>มูลค่า : </label>
            <input type="text" name="Product_value" value="<?php echo $row['Product_value']; ?>"><br/>

            <label>บริษัท : </label>
            <select name="add_office">
                <option value="<?php echo $row['company']; ?>"><?php echo $row['company']; ?></option>
                <?php
                    $office = mysqli_query($conn, "SELECT id, add_office FROM office");
                    while($c = mysqli_fetch_array($office)) {
                ?>
                    <option value="<?php echo $c['add_office']?>"><?php echo $c['add_office']?></option>
                <?php } ?>
            </select><br/>

            <label>คาดว่าจะปิดการขาย : </label>
            <input type="date" name="date_of_closing_of_sale" value="<?php echo $row['date_of_closing_of_sale']; ?>"><br/>

            <label>วันที่ปิดการขายได้ : </label>
            <input type="date" name="sales_can_be_closed" value="<?php echo $row['sales_can_be_closed']; ?>"><br/>

            <label>ระดับความสำคัญ : </label>
            <select name="Priority">
                <option value="<?php echo $row['Priority_level']; ?>"><?php echo $row['Priority_level']; ?></option>
                <?php
                    $Priority_level = mysqli_query($conn, "SELECT id, Priority FROM Priority_level");
                    while($c = mysqli_fetch_array($Priority_level)) {
                ?>
                    <option value="<?php echo $c['Priority']?>"><?php echo $c['Priority']?></option>
                <?php } ?>
            </select><br/>

            <button type="submit" name="submit">Submit</button>
        </form>
    </div>
</body>
</html>
