<?php
session_start();
require_once '../../functions.php';
$mysqli = connectDb();

$row = []; // ป้องกัน warning หากไม่มีข้อมูล

if (isset($_GET['team_id'])) {
    $team_id = intval($_GET['team_id']);

    // Query แบบปลอดภัย
    $stmt = $mysqli->prepare("SELECT * FROM team_catalog WHERE team_id = ?");
    $stmt->bind_param("i", $team_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
    $stmt->close();
}

if (isset($_POST['submit']) && isset($_GET['team_id'])) {
    $team = $_POST['team'];
    $team_id = intval($_GET['team_id']);

    // Update แบบปลอดภัย
    $stmt = $mysqli->prepare("UPDATE team_catalog SET team = ? WHERE team_id = ?");
    $stmt->bind_param("si", $team, $team_id);

    if ($stmt->execute()) {
        header("Location: Saleteam.php");
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
    <title>Edit Data</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
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

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button[type="button"] {
            background-color: #007bff;
            color: #fff;
            padding: 20px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
        }

        button[type="button"]:hover {
            background-color: #e6d5f8;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 20px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 45px;
        }

        input[type="submit"]:hover {
            background-color: #e6d5f8;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            text-decoration: none;
            color: #e6d5f8;
            font-size: 16px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        select {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 5px;
    border: 1px solid #ddd;
    font-size: 16px;
}
</style>
</head>
<body>
    <!-- Main content -->
    <section class="content">
          <!-- Main content -->
          <div class="container">
            <h2>เเก้ไขข้อมูล</h2><br>
            <form method="POST">
              <label>ทีมขาย : </label>
              <input type="text" team_id="team" name="team" value="<?php echo $row['team']; ?>" required>
                  <input type="submit" name="submit" value="Update">
                  <!-- ปุ่มย้อนกลับ -->
                   <button type="button" onclick="window.location.href='Saleteam.php';">
                    ย้อนกลับ
                </button>
              </form>
          </div>
</body>
</html>

