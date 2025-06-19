<?php
session_start();
include("../../functions.php");
$mysqli = connectDb();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>ข้อมูลบริษัท | PrimeFocus</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 4 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="../../dist/js/app.min.js"></script>
  <style>
    body { background: #e9f2f9; }
    .container1 { max-width: 800px; margin: 40px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .table thead { background: #0056b3; color: #fff; }
    .btn-add { position: fixed; bottom: 30px; right: 30px; background: #0056b3; color: #fff; border-radius: 50%; width: 56px; height: 56px; font-size: 24px; border: none; z-index: 999; }
    .modal-content { border-radius: 10px; }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">
  <header class="main-header">
    <a href="../../home_admin.php" class="logo"><b>Prime</b>Focus</a>
    <nav class="navbar navbar-static-top">
      <div class="navbar-custom-menu ml-auto d-flex justify-content-end w-100">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../../dist/img/user2-160x160.jpg" class="user-image img-circle elevation-2">
              <span class="hidden-xs text-white"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" style="width:60px;height:60px">
                <p><?php echo $_SESSION['email'] ?? ''; ?> <small>Admin</small></p>
              </li>
              <li class="user-footer">
                <div class="d-flex justify-content-end w-100">
                  <a href="../../logout.php" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image" style="width: 45px; height: 45px;">
        </div>
        <div class="info pl-2">
          <p class="mb-0 text-white font-weight-bold" style="font-size:14px;">
            <?php echo $_SESSION['email'] ?? ''; ?> <span class="text-muted" style="font-size:12px;">(Admin)</span>
          </p>
          <a href="#" class="d-block text-success"><i class="fa fa-circle"></i> Online</a>
        </div>
      </div>

      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <li><a href="../../home_admin.php"><i class="fa fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li class="treeview active">
          <a href="#">
            <i class="fa fa-folder-open"></i> <span>เพิ่มข้อมูล....</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="../layout/top-nav.php"><i class="fas fa-building"></i> เพิ่มข้อมูลบริษัท</a></li>
            <li><a href="../layout/boxed.php"><i class="fas fa-boxes"></i> เพิ่มข้อมูลกลุ่มสินค้า</a></li>
            <li><a href="../layout/fixed.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลอุตสาหกรรม</a></li>
            <li><a href="../layout/Source_of_the_budget.php"><i class="fas fa-industry"></i> เพิ่มข้อมูลที่มาของงบประมาณ</a></li>
            <li><a href="../layout/collapsed-sidebar.php"><i class="fas fa-tasks"></i> ขั้นตอนการขาย</a></li>
            <li><a href="../layout/of_winning.php"><i class="fas fa-trophy"></i> โอกาสการชนะ</a></li>
            <li><a href="../layout/Saleteam.php"><i class="fas fa-users"></i> ทีมขาย</a></li>
            <li><a href="../layout/position_u.php"><i class="fas fa-user-tag"></i> ตำแหน่ง</a></li>
            <li><a href="../layout/Profile_user.php"><i class="fas fa-id-card"></i> รายละเอียดผู้ใช้งาน</a></li>
            <li><a href="../layout/newuser.php"><i class="fas fa-user-plus"></i> เพิ่มผู้ใช้งาน</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content">
      <div class="container1">
        <h3>ข้อมูลบริษัท</h3>
        <input class="form-control mb-3" id="searchInput" type="text" placeholder="ค้นหาบริษัท...">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>บริษัท</th>
              <th>อุตสาหกรรม</th>
              <th style="width:160px;">Actions</th>
            </tr>
          </thead>
          <tbody id="companyTable">
            <?php
            $limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$start = ($page - 1) * $limit;

// นับจำนวนทั้งหมด
$totalQuery = $mysqli->query("SELECT COUNT(*) as total FROM company_catalog");
$totalRow = $totalQuery->fetch_assoc()['total'];
$totalPages = ceil($totalRow / $limit);

// ดึงข้อมูลหน้าเฉพาะ
$companies = $mysqli->query("
  SELECT cc.company_id, cc.company, ig.Industry
  FROM company_catalog cc
  LEFT JOIN industry_group ig ON cc.Industry_id = ig.industry_id
  LIMIT $start, $limit
");
            while ($c = $companies->fetch_assoc()) {
              echo "<tr>
                      <td>{$c['company']}</td>
                      <td>{$c['Industry']}</td>
                      <td>
                        <button class='btn btn-sm btn-info btn-edit' 
                          data-id='{$c['company_id']}' 
                          data-name='{$c['company']}' 
                          data-industry='{$c['Industry']}'>
                          <i class='fa fa-edit'></i> Edit
                        </button>
                        <a href='delete_Top.php?company_id={$c['company_id']}' class='text-danger ml-2' onclick=\"return confirm('ยืนยันการลบ?')\"class='btn btn-sm btn-danger'>
                  <i class='fa fa-trash'></i>Delete
                        </a>
                      </td>
                    </tr>";
            }
            ?>
          </tbody>
        </table>
         <!-- ปุ่ม pagination ด้านล่าง -->
      <nav>
        <ul class="pagination justify-content-center mt-3">
          <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page - 1 ?>">ก่อนหน้า</a>
          </li>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=<?= $page + 1 ?>">ถัดไป</a>
          </li>
        </ul>
      </nav>
      </div>

      <button class="btn-add" data-toggle="modal" data-target="#addModal">
        <i class="fa fa-plus"></i>
      </button>

      <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="addModalLabel">เพิ่มชื่อข้อมูลบริษัท</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="add_company.php" method="POST">
                <div class="form-group">
                  <label>ชื่อบริษัท:</label>
                  <input type="text" name="company" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>กลุ่มอุตสาหกรรม:</label>
                  <select name="industry" class="form-control" required>
                    <option value="">-- เลือกกลุ่มอุตสาหกรรม --</option>
                    <?php
                    $result = $mysqli->query("SELECT Industry_id, Industry FROM industry_group");
                    while ($row = $result->fetch_assoc()) {
                      echo "<option value='{$row['Industry_id']}'>{$row['Industry']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">บันทึกข้อมูล</button>
              </form>
            </div>
          </div>
        </div>
      </div>


      <!-- Edit Modal -->
      <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title" id="editModalLabel">แก้ไขข้อมูลบริษัท</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="editForm" action="edit_top.php" method="POST">
                <input type="hidden" name="company_id" id="edit_company_id">
                <div class="form-group">
                  <label>ชื่อบริษัท:</label>
                  <input type="text" name="company" id="edit_company_name" class="form-control" required>
                </div>
                <div class="form-group">
                  <label>กลุ่มอุตสาหกรรม:</label>
                  <select name="industry" id="edit_industry" class="form-control" required>
                    <option value="">-- เลือกกลุ่มอุตสาหกรรม --</option>
                    <?php
                    $result = $mysqli->query("SELECT Industry_id, Industry FROM industry_group");
                    while ($row = $result->fetch_assoc()) {
                      echo "<option value='{$row['Industry_id']}'>{$row['Industry']}</option>";
                    }
                    ?>
                  </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">บันทึกการแก้ไข</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<script>
  document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.trim().toLowerCase();
    const rows = document.querySelectorAll('#companyTable tr');

    rows.forEach(row => {
      const cells = row.querySelectorAll('td');
      const match = Array.from(cells).some(cell => 
        cell.textContent.toLowerCase().includes(filter)
      );
      row.style.display = match ? '' : 'none';
    });
  });
</script>


<script>
  document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      const name = this.dataset.name;
      const industry = this.dataset.industry;
      document.getElementById('edit_company_id').value = id;
      document.getElementById('edit_company_name').value = name;
      const options = document.getElementById('edit_industry').options;
      for (let i = 0; i < options.length; i++) {
        options[i].selected = options[i].text === industry;
      }
      $('#editModal').modal('show');
    });
  });
</script>
<script>
  document.getElementById('searchInput').addEventListener('keyup', function () {
    const filter = this.value.trim().toLowerCase();
    const rows = document.querySelectorAll('#companyTable tr');

    rows.forEach(row => {
      const companyCell = row.cells[0]; // คอลัมน์แรก: ชื่อบริษัท
      const companyText = companyCell.textContent.toLowerCase();
      row.style.display = companyText.includes(filter) ? '' : 'none';
    });
  });
</script>

</body>
</html>
