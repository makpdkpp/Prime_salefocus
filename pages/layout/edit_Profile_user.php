<?php
include("../../connect.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the data to be edited
    $sql = "SELECT * FROM profile_user WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

if (isset($_POST['submit'])) {
    $F_name = $_POST['F_name'];
    $email = $_POST['email'];
    $team = $_POST['team'];
    $Job_position = $_POST['Job_position'];


    // Update the record
    $update_sql = "UPDATE profile_user SET F_name='$F_name' , email='$email', team='$team' , position='$Job_position'  WHERE id=$id";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "Record updated successfully";
        header("Location: Profile_user.php"); // Redirect back to index page
    } else {
        echo "Error: " . $update_sql . "<br>" . $conn->error;
    }
}

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
            background-color: #4b03a4;
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
            background-color: #4b03a4;
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
              <label>ชื่อ :  </label>
              <input type="text" id="F_name" name="F_name" value="<?php echo $row['F_name']; ?>" required><br>

              <label>E-mail :  </label>
              <select name="email">
                <?php
                $users = mysqli_query($conn,"SELECT id, email FROM users");
                echo '<option value=""> -- Select email -- </option>';
                while($c = mysqli_fetch_array($users)) {
                  ?>
                  <option value="<?php echo $c['email']?>"><?php echo $c['email']?></option>
                  <?php } 
                  ?>
                  </select>

                  <label>ตำแหน่ง :  </label>
              <select name="Job_position">
                <?php
                $position = mysqli_query($conn,"SELECT id, Job_position FROM position");
                echo '<option value=""> -- Select ตำแหน่ง -- </option>';
                while($c = mysqli_fetch_array($position)) {
                  ?>
                  <option value="<?php echo $c['Job_position']?>"><?php echo $c['Job_position']?></option>
                  <?php } 
                  ?>
                  </select>

                  <label>ทีมขาย :  </label>
              <select name="team">
                <?php
                $team_s = mysqli_query($conn,"SELECT id, team FROM team_s");
                echo $c['team'];
                while($c = mysqli_fetch_array($team_s)) {
                  ?>
                  <option value="<?php echo $c['team']?>"><?php echo $c['team']?></option>
                  <?php } 
                  ?>
                  </select>

                  

                  <input type="submit" name="submit" value="Update">
                  <!-- ปุ่มย้อนกลับ -->
                   <button type="button" onclick="window.location.href='Profile_user.php';">
                    ย้อนกลับ
                </button>
              </form>
          </div>
</body>
</html>

<?php
$conn->close();
?>
