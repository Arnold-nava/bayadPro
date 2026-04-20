<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");
require_once(ROOT_PATH . "app/model/tuition.php");

if (!isset($_SESSION['id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

$student_id = $_SESSION['id'];
$result  = calculateTuition($conn, $student_id, "1st Sem", "2026-2027");
$balance = $result['final'];

/* TUITION ID */
$sql = "SELECT id FROM student_tuition WHERE student_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$tuitionRow = $stmt->get_result()->fetch_assoc();

if (!$tuitionRow) {
    die("No tuition record found.");
}

$tuition_id = $tuitionRow['id'];

/* CHECK IF FULLY PAID */
$sql = "SELECT id FROM payments 
        WHERE tuition_id = ? AND payment_type = 'full' AND payment_status = 'paid' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tuition_id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    $_SESSION['payment_msg'] = "error:Tuition is already fully paid.";
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

/* CHECK IF INSTALLMENTS EXIST */
$sql = "SELECT id FROM payment_installments WHERE tuition_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tuition_id);
$stmt->execute();
if ($stmt->get_result()->fetch_assoc()) {
    $_SESSION['payment_msg'] = "error:Installment plan already exists.";
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

$months  = 5;
$monthly = round($balance / $months, 2);

// Last month absorbs rounding remainder
$lastMonthAmount = $balance - ($monthly * ($months - 1));

$startDate = new DateTime();

for ($i = 1; $i <= $months; $i++) {
    $dueDate    = clone $startDate;
    $dueDate->modify("+{$i} month");
    $dueDateStr = $dueDate->format('Y-m-d');

    $amountDue = ($i === $months) ? $lastMonthAmount : $monthly;

    $sql = "INSERT INTO payment_installments 
            (student_id, tuition_id, month_number, amount_due, due_date, status)
            VALUES (?, ?, ?, ?, ?, 'unpaid')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiids", $student_id, $tuition_id, $i, $amountDue, $dueDateStr);
    $stmt->execute();
}

$_SESSION['payment_msg'] = "success:Installment plan created! Pay ₱" . number_format($monthly, 2) . " per month for 5 months.";
header("Location: " . BASE_URL . "app/views/client/balance.php");
exit();