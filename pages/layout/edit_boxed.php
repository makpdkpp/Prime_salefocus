<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

$row = []; // กำหนดตัวแปรเริ่มต้น

if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    $stmt = $mysqli->prepare("SELECT * FROM product_group WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();
}

if (isset($_POST['submit']) && isset($_GET['product_id'])) {
    $product = $_POST['product'];
    $product_id = intval($_GET['product_id']);

    $stmt = $mysqli->prepare("UPDATE product_group SET product = ? WHERE product_id = ?");
    $stmt->bind_param("si", $product, $product_id);

    if ($stmt->execute()) {
        header("Location: boxed.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product Group</title>
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <style>
        .container {
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        input[type="submit"], button[type="button"] {
            background-color: #4b03a4;
            color: #fff;
            padding: 15px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
        }
        input[type="submit"]:hover, button[type="button"]:hover {
            background-color: #e6d5f8;
            color: #000;
        }
        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <section class="content">
        <div class="container">
            <h2>แก้ไขข้อมูลกลุ่มสินค้า</h2>
            <form method="POST">
                <label for="product">กลุ่มสินค้า:</label>
                <input type="text" id="product" name="product" value="<?php echo htmlspecialchars($row['product'] ?? '', ENT_QUOTES); ?>" required>

                <input type="submit" name="submit" value="อัปเดตข้อมูล">
                <button type="button" onclick="window.location.href='boxed.php';">ย้อนกลับ</button>
            </form>
        </div>
    </section>
</body>
</html>
