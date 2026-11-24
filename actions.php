<?php
require_once "config.php";

// إضافة طالب
if (isset($_POST['add_student'])) {
    $code = trim($_POST['student_code']);
    $name = trim($_POST['full_name']);
    $national = trim($_POST['national_id']);

    if (strlen($national) !== 11 || !is_numeric($national)) {
        header("Location: index.php?msg=error_national");
        exit;
    }
    if ($code === "") { $code = "EM-" . date("Y") . "-" . rand(100, 999); }

    $stmt = $pdo->prepare("INSERT INTO students (student_code, full_name, national_id, department, level_name, email, phone, admission_type, score, state_name, pay_method) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([$code, $name, $national, $_POST['department'], $_POST['level_name'], $_POST['email'], $_POST['phone'], $_POST['admission_type'], $_POST['score'] ?: null, $_POST['state_name'] ?? null, $_POST['pay_method'] ?? null]);
    header("Location: index.php?page=students&msg=added");
    exit;
}

// حذف طالب
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: index.php?page=students&msg=deleted");
    exit;
}

// قبول / رفض
if (isset($_GET['status']) && isset($_GET['id'])) {
    $stmt = $pdo->prepare("UPDATE students SET status=? WHERE id=?");
    $stmt->execute([$_GET['status'], $_GET['id']]);
    header("Location: index.php?page=students&msg=updated");
    exit;
}

// إضافة مقرر
if (isset($_POST['add_course'])) {
    $stmt = $pdo->prepare("INSERT INTO courses (course_name, department, hours) VALUES (?,?,?)");
    $stmt->execute([$_POST['course_name'], $_POST['department'], $_POST['hours']]);
    header("Location: index.php?page=courses&msg=added");
    exit;
}

// حذف مقرر
if (isset($_GET['delete_course'])) {
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id=?");
    $stmt->execute([$_GET['delete_course']]);
    header("Location: index.php?page=courses&msg=deleted");
    exit;
}

// دفع إلكتروني
if (isset($_POST['add_payment'])) {
    $stmt = $pdo->prepare("INSERT INTO payments (student_code, amount, ref_number) VALUES (?,?,?)");
    $stmt->execute([$_POST['student_code'], $_POST['amount'], $_POST['ref_number']]);
    header("Location: index.php?page=payment&msg=paid");
    exit;
}
?>