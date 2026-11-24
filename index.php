<?php
require_once "config.php";
require_once "actions.php";
$page = $_GET['page'] ?? 'home';
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'الكل';

// إحصائيات موحدة - من قاعدة البيانات الحقيقية
$total = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$general = $pdo->query("SELECT COUNT(*) FROM students WHERE admission_type='عام'")->fetchColumn();
$direct = $pdo->query("SELECT COUNT(*) FROM students WHERE admission_type='مباشر'")->fetchColumn();
$accepted = $pdo->query("SELECT COUNT(*) FROM students WHERE status='مقبول'")->fetchColumn();

// قائمة الطلاب مع بحث وفلترة
$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
if ($search !== "") { $sql .= " AND (full_name LIKE ? OR student_code LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($filter !== 'الكل') { $sql .= " AND (admission_type=? OR status=?)"; $params[] = $filter; $params[] = $filter; }
$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql); $stmt->execute($params);
$students = $stmt->fetchAll();
$courses = $pdo->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>كلية الإمارات للعلوم والتكنولوجيا | نظام إدارة تسجيل الطلاب</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="brand"><div class="logo">E</div><div><b>كلية الإمارات</b><span>كلية الإمارات للعلوم والتكنولوجيا</span></div></div>
  <a href="index.php?page=login" class="btnGold" style="text-decoration:none">تسجيل الدخول</a>
</header>
<div class="banner">✓ يتم توريد الرسوم عبر <b>بنك فيصل الإسلامي</b> - رقم الحساب <b>134014447</b> - <b>تطبيق فوري</b></div>

<div class="container">
<?php if(isset($_GET['msg'])): ?>
  <div class="success">تمت العملية بنجاح ✓</div>
<?php endif; ?>

<?php if($page === 'home'): ?>
  <div style="text-align:center"><span style="background:#0a1f44;border:1px solid #2a4a80;color:#C9A227;padding:7px 14px;border-radius:20px;font-size:13px">● لوحة التحكم</span></div>
  <h1>نظام إدارة<br><span>تسجيل الطلاب</span></h1>
  <p class="sub">تطوير إدارة تسجيل الطلاب<br>دراسة حالة كلية الإمارات للعلوم والتكنولوجيا</p>
  <a href="index.php?page=add" class="bigBtn bigGold" style="text-decoration:none;text-align:center">+ إضافة طالب جديد</a>
  <a href="index.php?page=students" class="bigBtn bigOutline" style="text-decoration:none;text-align:center">عرض الطلاب</a>
  <div class="stats"><div><b>62,000</b><span>الرسوم المحصلة</span></div><div><b>84</b><span>عضو هيئة تدريس</span></div><div><b><?= $total ?></b><span>طالب</span></div></div>
  <a href="index.php?page=add" class="menuItem">إضافة طالب <small>‹</small></a>
  <a href="index.php?page=students" class="menuItem">إدارة الطلاب <small>‹</small></a>
  <a href="index.php?page=payment" class="menuItem">الدفع الإلكتروني <small>‹</small></a>
  <a href="index.php?page=reports" class="menuItem">عرض التقارير <small>‹</small></a>
  <a href="index.php?page=courses" class="menuItem">إدارة المقررات <small>‹</small></a>
  <a href="index.php?page=settings" class="menuItem">إعدادات النظام <small>‹</small></a>
  <div class="card">
    <h3>اختر نوع القبول</h3>
    <div class="two">
      <div class="typeCard"><b>🎓 القبول العام</b><br>5,000 جنيه<br>حسب النسبة</div>
      <div class="typeCard"><b>⚡ القبول المباشر</b><br>10,000 جنيه<br>قبول فوري</div>
    </div>
    <p>قبول عام: <?= $general ?> طالب | قبول مباشر: <?= $direct ?> طالب</p>
    <a href="index.php?page=add" class="btn btn-gold">قدم الآن</a>
  </div>

<?php elseif($page === 'add'): ?>
  <h1 style="font-size:27px">إضافة طالب جديد</h1>
  <div class="card">
    <form method="POST" action="actions.php">
      <label>اسم الطالب :</label><input name="full_name" required placeholder="الاسم الكامل">
      <label>الرقم الجامعي :</label><input name="student_code" placeholder="EM-2026-001">
      <label>الرقم الوطني (11 رقم) :</label><input name="national_id" maxlength="11" required placeholder="12345678901">
      <label>القسم :</label>
      <select name="department"><option>تقنية المعلومات</option><option>إدارة الأعمال</option><option>الهندسة</option><option>العلوم</option></select>
      <label>المستوى الدراسي :</label>
      <select name="level_name"><option>المستوى الأول</option><option>المستوى الثاني</option><option>المستوى الثالث</option><option>المستوى الرابع</option></select>
      <label>نوع القبول :</label>
      <select name="admission_type"><option value="عام">القبول العام - 5,000</option><option value="مباشر">القبول المباشر - 10,000</option></select>
      <label>نسبة الشهادة (للقبول العام) :</label><input name="score" type="number" placeholder="85">
      <label>الولاية :</label><input name="state_name" placeholder="الخرطوم">
      <label>طريقة الدفع (للقبول المباشر) :</label><input name="pay_method" placeholder="بنك فيصل الإسلامي - 134014447">
      <label>البريد الإلكتروني :</label><input name="email" type="email" placeholder="name@email.com">
      <label>رقم الهاتف :</label><input name="phone" placeholder="+249912584984">
      <div style="text-align:center"><button type="submit" name="add_student" class="btn btn-dark">حفظ البيانات</button><a href="index.php" class="btn btn-light">إلغاء</a></div>
    </form>
  </div>

<?php elseif($page === 'students'): ?>
  <h1 style="font-size:27px">إدارة الطلاب</h1>
  <div class="card">
    <form method="GET"><input type="hidden" name="page" value="students"><input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="بحث بالاسم أو الرقم..."><button class="btn btn-dark">بحث</button></form>
    <div style="margin-top:10px">
      <a href="index.php?page=students" class="btn btn-light">الكل</a>
      <a href="index.php?page=students&filter=عام" class="btn btn-gold">قبول عام</a>
      <a href="index.php?page=students&filter=مباشر" class="btn btn-dark">قبول مباشر</a>
      <a href="index.php?page=students&filter=مقبول" class="btn btn-light">مقبول</a>
    </div>
    <table><tr><th>رقم الطالب</th><th>اسم الطالب</th><th>القسم</th><th>نوع القبول</th><th>الحالة</th><th>العمليات</th></tr>
    <?php foreach($students as $s): ?>
    <tr>
      <td><?= htmlspecialchars($s['student_code']) ?></td>
      <td><?= htmlspecialchars($s['full_name']) ?></td>
      <td><?= htmlspecialchars($s['department']) ?></td>
      <td><?= $s['admission_type'] ?></td>
      <td><span class="badge <?= $s['status']=='مقبول'?'ok':'pend' ?>"><?= $s['status'] ?></span></td>
      <td>
        <a href="actions.php?status=مقبول&id=<?= $s['id'] ?>" class="btn btn-green">قبول</a>
        <a href="actions.php?status=مرفوض&id=<?= $s['id'] ?>" class="btn btn-light">رفض</a>
        <a href="actions.php?delete=<?= $s['id'] ?>" class="btn btn-red" onclick="return confirm('متأكد تحذف؟')">حذف</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </table>
  </div>
  <a href="index.php?page=add" class="btn btn-gold">+ إضافة طالب</a><a href="index.php" class="btn btn-dark">الرئيسية</a>

<?php elseif($page === 'courses'): ?>
  <h1 style="font-size:27px">إدارة المقررات</h1>
  <div class="card">
    <form method="POST" action="actions.php">
      <label>اسم المقرر</label><input name="course_name" required placeholder="برمجة 1">
      <label>القسم</label><select name="department"><option>تقنية المعلومات</option><option>إدارة الأعمال</option><option>الهندسة</option></select>
      <label>عدد الساعات</label><input name="hours" type="number" value="3">
      <button name="add_course" class="btn btn-dark">إضافة مقرر</button>
    </form>
    <table><tr><th>المقرر</th><th>القسم</th><th>الساعات</th><th>حذف</th></tr>
    <?php foreach($courses as $c): ?><tr><td><?= $c['course_name'] ?></td><td><?= $c['department'] ?></td><td><?= $c['hours'] ?></td><td><a href="actions.php?delete_course=<?= $c['id'] ?>" class="btn btn-red">حذف</a></td></tr><?php endforeach; ?>
    </table>
  </div>

<?php elseif($page === 'payment'): ?>
  <h1 style="font-size:27px">الدفع الإلكتروني</h1>
  <div class="card">
    <p style="background:#fffbe6;padding:12px;border-radius:12px"><b>بنك فيصل الإسلامي</b><br>رقم الحساب: <b>134014447</b><br>الاسم: كلية الإمارات للعلوم والتكنولوجيا</p>
    <form method="POST" action="actions.php">
      <label>رقم الطالب</label><input name="student_code" required placeholder="EM-2026-001">
      <label>المبلغ</label><input name="amount" required value="5000">
      <label>رقم الإشعار</label><input name="ref_number" required placeholder="123456">
      <button name="add_payment" class="btn btn-gold">تأكيد الدفع</button>
    </form>
  </div>

<?php elseif($page === 'reports'): ?>
  <h1 style="font-size:27px">عرض التقارير</h1>
  <div class="card">
    <p><b>عدد الطلاب الكلي:</b> <?= $total ?></p>
    <p><b>قبول عام:</b> <?= $general ?> | <b>قبول مباشر:</b> <?= $direct ?></p>
    <p><b>المقبولين:</b> <?= $accepted ?></p>
    <p><b>أعضاء هيئة التدريس:</b> 84</p>
    <p><b>الرسوم المحصلة:</b> 62,000 جنيه</p>
    <p><b>حساب البنك:</b> بنك فيصل الإسلامي - 134014447</p>
    <button class="btn btn-dark" onclick="window.print()">طباعة</button>
    <button class="btn btn-gold" onclick="window.print()">تصدير PDF</button>
  </div>

<?php elseif($page === 'settings'): ?>
  <h1 style="font-size:27px">إعدادات النظام</h1>
  <div class="card">
    <label>اسم الكلية</label><input value="كلية الإمارات للعلوم والتكنولوجيا" readonly>
    <label>رقم الهاتف</label><input value="+249912584984" readonly>
    <label>حساب البنك</label><input value="بنك فيصل الإسلامي - 134014447" readonly>
    <p>رسوم القبول العام: 5,000 | رسوم القبول المباشر: 10,000</p>
  </div>

<?php elseif($page === 'login'): ?>
  <h1 style="font-size:27px">تسجيل الدخول</h1>
  <div class="card">
    <div style="text-align:center"><div class="logo" style="margin:auto;width:70px;height:70px;font-size:32px">E</div><h2 style="text-align:center">نظام إدارة تسجيل الطلاب</h2></div>
    <form method="POST" action="index.php?page=home">
      <label>اسم المستخدم :</label><input name="username" placeholder="اسم المستخدم">
      <label>كلمة المرور :</label><input name="password" type="password" placeholder="كلمة المرور">
      <div style="text-align:center"><button class="btn btn-dark" style="padding:13px 50px">دخول</button></div>
      <p style="text-align:center;font-size:12px;color:#888">كلية الإمارات للعلوم والتكنولوجيا</p>
    </form>
  </div>
<?php endif; ?>
</div>

<div class="footer"><b>كلية الإمارات للعلوم والتكنولوجيا</b><br>الخرطوم - أم درمان - السودان | هاتف: <span dir="ltr">+249912584984</span><br>بنك فيصل الإسلامي - حساب: 134014447<br>© 2026 جميع الحقوق محفوظة - مشروع تخرج</div>
</body>
</html>