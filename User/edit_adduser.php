<?php
/***********************************************************************
 *  edit_transaction.php (READY-TO-USE)
 *  แก้ไขเรคคอร์ดของตาราง transactional ได้เฉพาะ user ที่ล็อกอิน
 *  *** ไม่ต้องพึ่งตาราง step_catalog อีกต่อไป ***
 ***********************************************************************/
require_once '../functions.php';
session_start();

// ─────────────────── 1) Auth ───────────────────
if (empty($_SESSION['user_id']) || (int)$_SESSION['role_id'] !== 2) {
    header('Location: ../index.php');
    exit;
}

$mysqli   = connectDb();
$user_id  = (int)$_SESSION['user_id'];
$email    = htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8');
$nname    = htmlspecialchars($_SESSION['nname'] ?? '', ENT_QUOTES, 'UTF-8');

// ─────────────────── 2) Static dropdown data ───────────────────
$stepMap = [1=>'Present',2=>'Budget',3=>'TOR',4=>'Bidding',5=>'WIN',6=>'LOST'];
function getOpts(mysqli $db,$tbl,$id,$label){$r=$db->query("SELECT `$id`,`$label` FROM `$tbl` ORDER BY `$label`");return $r?$r->fetch_all(MYSQLI_ASSOC):[];}
$productOpts  = getOpts($mysqli,'product_group','product_id','product');
$companyOpts  = getOpts($mysqli,'company_catalog','company_id','company');
$teamOpts     = getOpts($mysqli,'team_catalog','team_id','team');
$priorityOpts = getOpts($mysqli,'priority_level','priority_id','priority');
$Source_budgeOpts = getOpts($mysqli, 'source_of_the_budget', 'Source_budget_id', 'Source_budge');
// flags label map
$subSteps = ['present'=>'Present','budgeted'=>'Budget','tor'=>'TOR','bidding'=>'Bidding','win'=>'WIN','lost'=>'LOST'];

// ─────────────────── 3) รับ ID และดึงข้อมูล ───────────────────
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) exit('Invalid id');
$tid=(int)$_GET['id'];
$stmt=$mysqli->prepare("SELECT * FROM transactional WHERE transac_id=? AND user_id=?");
$stmt->bind_param('ii',$tid,$user_id);
$stmt->execute();
$rec=$stmt->get_result()->fetch_assoc();
if(!$rec) exit('ไม่พบข้อมูล หรือไม่ใช่ข้อมูลของคุณ');

// ─────────────────── 4) Save ───────────────────
if($_SERVER['REQUEST_METHOD']==='POST'){
    // ----- รับค่าฟอร์ม -----
    $data=[
        'company_id'   => (int)$_POST['company_id'],
        'Product_id'   => (int)$_POST['Product_id'],
        'team_id'      => (int)$_POST['team_id'],
        'priority_id'  => $_POST['priority_id']===''?null:(int)$_POST['priority_id'],
        'step_id'      => (int)$_POST['step_id'],
        'Product_detail'=> trim($_POST['Product_detail']),
        'product_value'=> (int)str_replace(',','',$_POST['product_value']),
        'Source_budget_id' => (int)$_POST['Source_budget_id'],
        'fiscalyear'   => $_POST['fiscalyear'],
        'contact_start_date'     => $_POST['contact_start_date']     ?: null,
        'date_of_closing_of_sale'=> $_POST['date_of_closing_of_sale']?: null,
        'sales_can_be_close'     => $_POST['sales_can_be_close']     ?: null,
        'remark'       => trim($_POST['remark'])
    ];
    // flags & dates
    foreach($subSteps as $f=>$lb){
        $data[$f]      = isset($_POST[$f])&&$_POST[$f]==='1'?1:0;
        $data[$f.'_date'] = $_POST[$f.'_date']??null;
    }
    // ----- UPDATE SQL -----
    $sql="UPDATE transactional SET
            company_id=?, Product_id=?, team_id=?, priority_id=?, step_id=?,
            Product_detail=?, product_value=?, Source_budget_id=?, fiscalyear=?, contact_start_date=?,
            date_of_closing_of_sale=?, sales_can_be_close=?, remark=?,
            present=?,present_date=?, budgeted=?,budgeted_date=?, tor=?,tor_date=?,
            bidding=?,bidding_date=?, win=?,win_date=?, lost=?,lost_date=?
          WHERE transac_id=? AND user_id=?";
    $st=$mysqli->prepare($sql);
    $st->bind_param(
        // iiiissiissssssisisisisisiii (28 ตัว)
        'iiiissiissssssisisisisisiii',
        $data['company_id'],$data['Product_id'],$data['team_id'],$data['priority_id'],$data['step_id'],
        $data['Product_detail'],$data['product_value'],$data['Source_budget_id'],$data['fiscalyear'],$data['contact_start_date'],
        $data['date_of_closing_of_sale'],$data['sales_can_be_close'],$data['remark'],
        $data['present'],$data['present_date'],$data['budgeted'],$data['budgeted_date'],$data['tor'],$data['tor_date'],
        $data['bidding'],$data['bidding_date'],$data['win'],$data['win_date'],$data['lost'],$data['lost_date'],
        $tid,$user_id
    );
    if($st->execute()){header('Location: ../home_user.php?edit=ok');exit;}
    exit('Error: '.$st->error);
}

$sel=function($cur,$id){return $cur==$id?'selected':'';};
$chk=function($v){return $v?'checked':'';};
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Edit Transaction</title>
<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css"><link rel="stylesheet" href="../dist/css/AdminLTE.min.css"><link rel="stylesheet" href="../dist/css/skins/_all-skins.min.css">
<style>.card{max-width:760px;margin:40px auto;padding:32px 40px;background:#fff;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,.1)}label{font-weight:500}#product_value{text-align:right}.btn-save{background:#c82333;color:#fff;border:none}</style></head>
<body class="hold-transition skin-red sidebar-mini">
<div class="card">
  <h3>แก้ไขโอกาสขาย #<?= $tid ?></h3>
  <form method="post" id="editForm">
    <div class="form-group"><label>ชื่อโครงการ</label><input name="Product_detail" class="form-control" value="<?= htmlspecialchars($rec['Product_detail']) ?>" required></div>
    <div class="row">
      <div class="col-sm-6 form-group"><label>บริษัท</label><select name="company_id" class="form-control"><?php foreach($companyOpts as $o):?><option value="<?= $o['company_id'] ?>" <?= $sel($rec['company_id'],$o['company_id']) ?>><?= htmlspecialchars($o['company']) ?></option><?php endforeach;?></select></div>
      <div class="col-sm-6 form-group"><label>มูลค่า (บาท)</label><input id="product_value" name="product_value" class="form-control" value="<?= number_format($rec['product_value']) ?>"></div>
    </div>
    <div class="row">
      <div class="col-sm-6 form-group">
        <label>แหล่งที่มาของงบประมาณ</label>
        <select name="Source_budget_id" class="form-control" required>
          <option value="">-- เลือกแหล่งที่มา --</option>
          <?php foreach($Source_budgeOpts as $o): ?>
            <option value="<?= $o['Source_budget_id'] ?>" <?= $sel($rec['Source_budget_id'],$o['Source_budget_id']) ?>><?= htmlspecialchars($o['Source_budge']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-6 form-group">
        <label>ปีงบประมาณ</label>
        <select name="fiscalyear" class="form-control" required>
          <?php
            $years = [2568,2569,2570,2571];
            foreach($years as $y):
          ?>
            <option value="<?= $y ?>" <?= $rec['fiscalyear']==$y?'selected':''; ?>><?= $y ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4 form-group"><label>สินค้า</label><select name="Product_id" class="form-control"><?php foreach($productOpts as $o):?><option value="<?= $o['product_id'] ?>" <?= $sel($rec['Product_id'],$o['product_id']) ?>><?= htmlspecialchars($o['product']) ?></option><?php endforeach;?></select></div>
      <div class="col-sm-4 form-group"><label>ทีมขาย</label><select name="team_id" class="form-control"><?php foreach($teamOpts as $o):?><option value="<?= $o['team_id'] ?>" <?= $sel($rec['team_id'],$o['team_id']) ?>><?= htmlspecialchars($o['team']) ?></option><?php endforeach;?></select></div>
      <div class="col-sm-4 form-group"><label>Priority</label><select name="priority_id" class="form-control"><option value="">--</option><?php foreach($priorityOpts as $o):?><option value="<?= $o['priority_id'] ?>" <?= $sel($rec['priority_id'],$o['priority_id']) ?>><?= htmlspecialchars($o['priority']) ?></option><?php endforeach;?></select></div>
    </div>
    <div class="row">
      <div class="col-sm-4 form-group"><label>Contact Start</label><input type="date" class="form-control" name="contact_start_date" value="<?= $rec['contact_start_date'] ?>"></div>
      <div class="col-sm-4 form-group"><label>Predict Close</label><input type="date" class="form-control" name="date_of_closing_of_sale" value="<?= $rec['date_of_closing_of_sale'] ?>"></div>
      <div class="col-sm-4 form-group"><label>Deal Close</label><input type="date" class="form-control" name="sales_can_be_close" value="<?= $rec['sales_can_be_close'] ?>"></div>
    </div>
    <div class="form-group"><label>ขั้นตอน (Step)</label><select name="step_id" class="form-control"><?php foreach($stepMap as $id=>$name):?><option value="<?= $id ?>" <?= $sel($rec['Step_id'],$id) ?>><?= $name ?></option><?php endforeach;?></select></div>
    <div class="form-group"><label>สถานะย่อย</label><br><?php foreach($subSteps as $f=>$lb):?><label style="margin-right:1rem"><input type="hidden" name="<?= $f ?>" value="0"><input type="checkbox" name="<?= $f ?>" value="1" <?= $chk($rec[$f]) ?>> <?= $lb ?></label><?php endforeach;?></div>
    <div class="form-group"><label>หมายเหตุ</label><textarea name="remark" rows="2" class="form-control"><?= htmlspecialchars($rec['remark']) ?></textarea></div>
    <button class="btn btn-save" type="submit">Save</button>
  </form>
</div>
<script src="../plugins/jQuery/jQuery-2.1.3.min.js"></script><script src="../bootstrap/js/bootstrap.min.js"></script>
<script>(function(){const el=document.getElementById('product_value');const fm=v=>{v=v.replace(/[^0-9.]/g,'');if(!v)return '';const[p,s]=v.split('.');return(+p).toLocaleString('en-US')+(s?'.'+s.slice(0,2):'');};el.addEventListener('input',()=>{const st=el.selectionStart,l=el.value.length;el.value=fm(el.value);el.setSelectionRange(st+(el.value.length-l),st+(el.value.length-l));});document.getElementById('editForm').addEventListener('submit',()=>{el.value=el.value.replace(/,/g,'');});})();</script>
</body></html>