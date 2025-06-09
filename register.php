<?php
include 'connect.php';

// สมัครสมาชิก (signUp)
if (isset($_POST['signUp'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

 /*   if ($result->num_rows > 0) {
        // อีเมลซ้ำ
        header("Location: index.php?error=อีเมลนี้ถูกใช้แล้ว");
        exit();
    } else {
        $insertQuery = "INSERT INTO users (email, password, role) VALUES ('$email', '$password', '$role')";
        if ($conn->query($insertQuery) === TRUE) {
            $user_id = $conn->insert_id;
            $table_name = "user_" . $user_id;

            $sql_create_table = "CREATE TABLE $table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                Product_group VARCHAR(255) NOT NULL,
                Product_list VARCHAR(255) NOT NULL,
                Contact_start_date DATE NOT NULL,
                step VARCHAR(255) NOT NULL,
                salesperson VARCHAR(255) NOT NULL,
                Sales_team VARCHAR(255) NOT NULL,
                Product_value INT NOT NULL,
                company VARCHAR(255) NOT NULL,
                date_of_closing_of_sale DATE NOT NULL,
                sales_can_be_closed DATE NOT NULL,
                Priority_level VARCHAR(255) NOT NULL
            )";

            if (!$conn->query($sql_create_table)) {
                header("Location: index.php?error=สร้างตารางไม่สำเร็จ");
                exit();
            }

            header("Location: index.php?success=สมัครสมาชิกสำเร็จ");
            exit();
        } else {
            header("Location: index.php?error=ไม่สามารถสมัครสมาชิกได้");
            exit();
        }
    }
}
    */
}

// เข้าสู่ระบบ (signIn)
if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $role = $row['role'];

        $user_id = $row['id'];
        $table_name = "user_" . $user_id;

        /* ตรวจสอบว่าตารางผู้ใช้มีหรือยัง
        $sql_check_table = "SHOW TABLES LIKE '$table_name'";
        $check_result = $conn->query($sql_check_table);

        if ($check_result->num_rows == 0) {
            $sql_create_table = "CREATE TABLE $table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                Product_group VARCHAR(255) NOT NULL,
                Product_list VARCHAR(255) NOT NULL,
                Contact_start_date DATE NOT NULL,
                step VARCHAR(255) NOT NULL,
                salesperson VARCHAR(255) NOT NULL,
                Sales_team VARCHAR(255) NOT NULL,
                Product_value INT NOT NULL,
                company VARCHAR(255) NOT NULL,
                date_of_closing_of_sale DATE NOT NULL,
                sales_can_be_closed DATE NOT NULL,
                Priority_level VARCHAR(255) NOT NULL
            )";

            if (!$conn->query($sql_create_table)) {
                header("Location: index.php?error=สร้างตารางผู้ใช้ไม่สำเร็จ");
                exit();
            }
        }
            */

        // เปลี่ยนหน้าไปตาม role
        if ($role === 'Admin') {
            header("Location: home_admin.php");
        } else {
            header("Location: home_user.php");
        }
        exit();
    } else {
        header("Location: index.php?error=อีเมลหรือรหัสผ่านไม่ถูกต้อง");
        exit();
    }
}
?>
