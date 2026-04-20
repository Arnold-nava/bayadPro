<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");
require_once(ROOT_PATH . "app/model/tuition.php");

if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "app/views/client/payment.php");
    exit();
}

// ✅ Fake GCash validation
$gcash_number = $_POST['gcash_number'] ?? '';
$gcash_pin    = $_POST['gcash_pin'] ?? '';

if (!preg_match('/^09\d{9}$/', $gcash_number)) {
    $_SESSION['gcash_error'] = "Invalid GCash number. Must start with 09 and be 11 digits.";
    header("Location: " . BASE_URL . "app/views/client/gcash.php");
    exit();
}

if (strlen($gcash_pin) !== 6 || !ctype_digit($gcash_pin)) {
    $_SESSION['gcash_error'] = "Invalid PIN. Must be 6 digits.";
    header("Location: " . BASE_URL . "app/views/client/gcash.php");
    exit();
}

// ✅ Use session ID, not POST (prevents tampering)
$student_id = $_SESSION['id'];

// ✅ Recalculate amount server-side (prevents price tampering)
$result = calculateTuition($conn, $student_id, "1st Sem", "2026-2027");
$amount = $result['final'];

/* GET LATEST TUITION */
$sql = "SELECT id FROM student_tuition 
        WHERE student_id = ? 
        ORDER BY id DESC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$tuition = $stmt->get_result()->fetch_assoc();

if (!$tuition) {
    die("No tuition record found.");
}

$tuition_id = $tuition['id'];

// ✅ Check if already paid (prevents duplicate payments)
$sql = "SELECT id FROM payments 
        WHERE tuition_id = ? AND payment_status = 'paid' 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tuition_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    $_SESSION['payment_msg'] = "error:This tuition has already been paid.";
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

/* PAYMENT REF */
$reference = "REF-" . time() . "-" . $student_id;

/* INSERT PAYMENT */
$sql = "INSERT INTO payments 
        (student_id, tuition_id, payment_reference, amount_paid, payment_type, payment_status, payment_method)
        VALUES (?, ?, ?, ?, 'full', 'paid', 'GCash')";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisd", $student_id, $tuition_id, $reference, $amount);

if ($stmt->execute()) {
    $_SESSION['payment_msg'] = "success:Payment successful! Reference: " . $reference;
} else {
    $_SESSION['payment_msg'] = "error:Payment failed. Please try again.";
}

header("Location: " . BASE_URL . "app/views/client/balance.php");
exit();