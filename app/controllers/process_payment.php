<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");
require_once(ROOT_PATH . "app/model/tuition.php");

if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "app/views/client/balance.php");
    exit();
}

// GCASH VALIDATION
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

$student_id   = $_SESSION['id'];
$payment_type = $_POST['payment_type'] ?? 'full';
$reference    = "REF-" . time() . "-" . $student_id;

if ($payment_type === 'installment') {

    // PAY ONE INSTALLMENT
    $installment_id = (int) ($_POST['installment_id'] ?? 0);

    // VERIFY IT BELONGS TO THIS STUDENT AND IS UNPAID
    $sql = "SELECT * FROM payment_installments 
            WHERE id = ? AND student_id = ? AND status = 'unpaid' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $installment_id, $student_id);
    $stmt->execute();
    $installment = $stmt->get_result()->fetch_assoc();

    if (!$installment) {
        $_SESSION['payment_msg'] = "error:Invalid or already paid installment.";
        header("Location: " . BASE_URL . "app/views/client/balance.php");
        exit();
    }

    // CHECK PREVIOUS MONTH IS PAID (block if unpaid)
    if ($installment['month_number'] > 1) {
        $prevMonth = $installment['month_number'] - 1;
        $sql = "SELECT status FROM payment_installments 
                WHERE tuition_id = ? AND month_number = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $installment['tuition_id'], $prevMonth);
        $stmt->execute();
        $prev = $stmt->get_result()->fetch_assoc();

        if ($prev && $prev['status'] === 'unpaid') {
            $_SESSION['payment_msg'] = "error:Please pay Month " . $prevMonth . " first.";
            header("Location: " . BASE_URL . "app/views/client/balance.php");
            exit();
        }
    }

    // MARK INSTALLMENT AS PAID
    $sql = "UPDATE payment_installments 
            SET status = 'paid', payment_reference = ?, paid_at = NOW()
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $reference, $installment_id);

    if ($stmt->execute()) {
        // LOG IN PAYMENTS TABLE WITH payment_month
        $sql = "INSERT INTO payments 
                (student_id, tuition_id, payment_reference, amount_paid, payment_type, payment_month, payment_status, payment_method)
                VALUES (?, ?, ?, ?, 'installment', ?, 'paid', 'GCash')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisdi", $student_id, $installment['tuition_id'], $reference, $installment['amount_due'], $installment['month_number']);
        $stmt->execute();

        $_SESSION['payment_msg'] = "success:Month " . $installment['month_number'] . " paid! Reference: " . $reference;
    } else {
        $_SESSION['payment_msg'] = "error:Payment failed. Please try again.";
    }

} else {

    // FULL PAYMENT
    $result = calculateTuition($conn, $student_id, "1st Sem", "2026-2027");
    $amount = $result['final'];

    $sql = "SELECT id FROM student_tuition WHERE student_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $tuitionRow = $stmt->get_result()->fetch_assoc();

    if (!$tuitionRow) {
        die("No tuition record found.");
    }

    $tuition_id = $tuitionRow['id'];

    // CHECK ALREADY FULLY PAID
    $sql = "SELECT id FROM payments 
            WHERE tuition_id = ? AND payment_type = 'full' AND payment_status = 'paid' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $tuition_id);
    $stmt->execute();

    if ($stmt->get_result()->fetch_assoc()) {
        $_SESSION['payment_msg'] = "error:This tuition has already been fully paid.";
        header("Location: " . BASE_URL . "app/views/client/balance.php");
        exit();
    }

    $sql = "INSERT INTO payments 
            (student_id, tuition_id, payment_reference, amount_paid, payment_type, payment_status, payment_method)
            VALUES (?, ?, ?, ?, 'full', 'paid', 'GCash')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisd", $student_id, $tuition_id, $reference, $amount);

    if ($stmt->execute()) {
        // MARK ALL INSTALLMENTS PAID TOO (if any exist)
        $sql = "UPDATE payment_installments SET status = 'paid' WHERE tuition_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $tuition_id);
        $stmt->execute();

        $_SESSION['payment_msg'] = "success:Full payment successful! Reference: " . $reference;
    } else {
        $_SESSION['payment_msg'] = "error:Payment failed. Please try again.";
    }
}

header("Location: " . BASE_URL . "app/views/client/balance.php");
exit();