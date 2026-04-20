<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

if (!isset($_SESSION['id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

$student_id = $_SESSION['id'];

/* GET TUITION ID */
$sql = "SELECT id FROM student_tuition WHERE student_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$tuitionRow = $stmt->get_result()->fetch_assoc();

if (!$tuitionRow) {
    $_SESSION['payment_msg'] = "error:No tuition record found.";
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

$tuition_id = $tuitionRow['id'];

/* DELETE ONLY UNPAID INSTALLMENTS */
$sql = "DELETE FROM payment_installments WHERE tuition_id = ? AND status = 'unpaid'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tuition_id);

if ($stmt->execute()) {
    $_SESSION['payment_msg'] = "success:Remaining installments cancelled. Please pay your remaining balance in full.";
} else {
    $_SESSION['payment_msg'] = "error:Failed to cancel installment plan.";
}

header("Location: " . BASE_URL . "app/views/client/balance.php");
exit();