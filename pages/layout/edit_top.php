<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

if (isset($_GET['company_id'])) {
    $company_id = intval($_GET['company_id']);
    $sql = "SELECT * FROM company_catalog WHERE company_id = $company_id";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
}

if (isset($_POST['submit'])) {
    $company = mysqli_real_escape_string($mysqli, $_POST['company']);
    $industry_id = intval($_POST['industry']);

    $update_sql = "UPDATE company_catalog SET company='$company', Industry_id='$industry_id' WHERE company_id=$company_id";
    
    if ($mysqli->query($update_sql) === TRUE) {
        header("Location: top-nav.php");
        exit();
    } else {
        echo "Error: " . $update_sql . "<br>" . $mysqli->error;
    }
}
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
        <h2>แก้ไขข้อมูลบริษัท</h2>
        <form method="POST">
          <label>ชื่อบริษัท :</label>
          <input type="text" name="company" maxlength="50" required value="<?php echo htmlspecialchars($row['company'] ?? ''); ?>">

          <label>กลุ่มอุตสาหกรรม :</label>
          <select name="industry" class="form-control select2" required>
            <option value="">เลือกอุตสาหกรรม</option>
            <?php
            $industries = $mysqli->query("SELECT Industry_id, Industry FROM Industry_group");
            while ($ind = $industries->fetch_assoc()) {
              $selected = ($ind['Industry_id'] == $row['Industry_id']) ? 'selected' : '';
              echo "<option value='{$ind['Industry_id']}' $selected>{$ind['Industry']}</option>";
            }
            ?>
          </select>

          <input type="submit" name="submit" value="อัปเดตข้อมูล">
          <button type="button" onclick="window.location.href='top-nav.php';">ย้อนกลับ</button>
        </form>
      </div>
    </section>
</html>


</body>
</html>

<?php $mysqli->close(); ?>
