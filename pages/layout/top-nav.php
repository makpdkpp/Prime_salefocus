<?php
session_start();
include("../../connect.php");

include("../../functions.php");

$mysqli = connectDb();
$sql = "SELECT Industry_id, Industry FROM Industry_group";
$result = $mysqli->query($sql);



//if (isset($_POST['submit'])) {
//  $add_company = mysqli_real_escape_string($mysqli, $_POST['company']);
//  $industry = mysqli_real_escape_string($mysqli, $_POST['industry']);
//  mysqli_query($mysqli, "INSERT INTO company_catalog (company_id, company, Industry_id) VALUES ('$add_company', '$industry')");
//}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Prime Focus 25 V1 (admin)</title>
  <link rel="icon" type="image/png" href="../../images/logo.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS -->
  <link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" />
  <link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" />
  <link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" />

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
  <style>
    .container {
      max-width: 500px;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    button[type="submit"] {
      background-color: #007bff;
      color: white;
      padding: 15px;
      font-size: 16px;
      border: none;
      border-radius: 4px;
      width: 100%;
      cursor: pointer;
      margin-top: 25px !important;
    }

    button[type="submit"]:hover {
      background: #0056b3;
    }

    .container1 {
      max-width: 950px;
      margin: 50px auto 0;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      margin-top: 20px;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background: #0056b3;
      color: white;
    }

    tr:hover {
      background-color: #f9f9f9;
    }

    .select2-container--bootstrap4 .select2-selection {
      height: 38px;
      padding: 6px 12px;
      font-size: 16px;
      border-radius: 4px;
    }
  </style>
</head>

<body class="skin-blue fixed">
  <div class="wrapper">
    <header class="main-header">
      <a href="../../home_admin.php" class="logo"><b>Prime</b>Focus</a>
      <nav class="navbar navbar-static-top">
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <img src="../../dist/img/user2-160x160.jpg" class="user-image" />
                <span class="hidden-xs"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <img src="../../dist/img/user2-160x160.jpg" class="img-circle" />
                  <p><?php echo $_SESSION['email'] ?? ''; ?><small>Admin</small></p>
                </li>
                <li class="user-footer">
                  <div class="pull-right"><a href="../../logout.php" class="btn btn-default btn-flat">Sign out</a></div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>

    <aside class="main-sidebar">
      <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
            <img src="../../dist/img/user2-160x160.jpg" class="img-circle" />
          </div>
          <div class="pull-left info">
            <p><?php echo $_SESSION['email'] ?? ''; ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>
        <ul class="sidebar-menu">
          <li class="header">MAIN NAVIGATION</li>
          <li><a href="../../home_admin.php"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
          <li class="treeview active">
            <a href="#"><i class="fa fa-files-o"></i> <span>เพิ่มข้อมูล....</span></a>
            <ul class="treeview-menu">
              <li class="active"><a href="../layout/top-nav.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลบริษัท</a></li>
              <li><a href="../layout/boxed.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
              <li><a href="../layout/fixed.php"><i class="fa fa-circle-o"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
              <li><a href="../layout/collapsed-sidebar.php"><i class="fa fa-circle-o"></i> ขั้นตอนการขาย</a></li>
              <li><a href="../layout/of_winning.php"><i class="fa fa-circle-o"></i> โอกาสการชนะ</a></li>
              <li><a href="../layout/Saleteam.php"><i class="fa fa-circle-o"></i> ทีมขาย</a></li>
              <li><a href="../layout/position_u.php"><i class="fa fa-circle-o"></i> ตำแหน่ง</a></li>
              <li><a href="../layout/Profile_user.php"><i class="fa fa-circle-o"></i> รายละเอียดผู้ใช้งาน</a></li>
            </ul>
          </li>
        </ul>
      </section>
    </aside>

    <div class="content-wrapper">
      <section class="content">
        <div class="container">
          <h2>เพิ่มชื่อข้อมูลบริษัท</h2>
          <form action="add_company.php" method="POST">
            <label>ชื่อบริษัท :</label>
            <input type="text" name="company" maxlength="20" required>

            <label>กลุ่มอุตสาหกรรม :</label>
            <select name="industry" class="form-control select2" data-placeholder="เลือกหรือพิมพ์เพื่อค้นหา..." required>
              <option value=""></option>
              <?php
              $product_industry = mysqli_query($mysqli, "SELECT industry_id, industry FROM Industry_group");
              while($c = mysqli_fetch_array($product_industry)) {
                echo "<option value='{$c['industry']}'>{$c['industry']}</option>";
              }
              ?>
            </select>

            <button type="submit" name="submit">Submit</button>
          </form>
        </div>

        <div class="container1">
          <h2>ข้อมูลบริษัท</h2>
          <table>
            <thead>
              <tr>
                <th>บริษัท</th>
                <th>อุตสาหกรรม</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
             // $sql = "SELECT id, add_office, company FROM company_catalog";
             $sql = "SELECT company_catalog.*,industry_group.* FROM company_catalog LEFT JOIN industry_group ON company_catalog.Industry_id=industry_group.Industry_id;";
              $result = $mysqli->query($sql);
              if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['company']}</td>
                          <td>{$row['Industry']}</td>
                          <td>
                            <a href='edit_top.php?company_id={$row['company_id']}'><i class='fa fa-pencil-square-o' style='color: #4CAF50;'></i> Edit</a> |
                            <a href='delete_Top.php?company_id={$row['company_id']}' onclick=\"return confirm('คุณต้องการลบหรือไม่')\"><i class='fa fa-trash' style='color: #f44336;'></i> Delete</a>
                          </td>
                        </tr>";
                }
              } else {
                echo "<tr><td colspan='3'>-- ไม่พบข้อมูลในระบบ --</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>

  <!-- JS -->
  <script src="../../plugins/jQuery/jQuery-2.1.3.min.js"></script>
  <script src="../../bootstrap/js/bootstrap.min.js"></script>
  <script src="../../plugins/slimScroll/jquery.slimScroll.min.js"></script>
  <script src="../../plugins/fastclick/fastclick.min.js"></script>
  <script src="../../dist/js/app.min.js"></script>

  <!-- Select2 -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true
      });
    });
  </script>
</body>
</html>
