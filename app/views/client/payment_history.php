<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

$student_id = $_SESSION['id'];


$sqlStudent = "SELECT * FROM student_list WHERE id = ?";
$stmt = $conn->prepare($sqlStudent);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();


$sql = "SELECT * FROM payments WHERE student_id = ? ORDER BY payment_date DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$payments = $stmt2->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>public/css/payment_history.css">

    <title>Payment History</title>
</head>
<body>
<?php include(ROOT_PATH . "utils/nav.php"); ?>

<div class="index">


    <div class="header-banner">
        <h1>📜 Payment History</h1>
        <p>View all your past transactions.</p>
    </div>

    <div class="payment-card">
        <?php if ($payments->num_rows > 0): ?>
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Method</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['payment_reference']) ?></td>
                            <td>₱<?= number_format($row['amount_paid'], 2) ?></td>
                            <td><?= ucfirst($row['payment_type']) ?></td>
                            <td><?= $row['payment_month'] ?? '-' ?></td>
                            <td>
                                <span class="badge <?= $row['payment_status'] ?>">
                                    <?= ucfirst($row['payment_status']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['payment_method']) ?></td>
                            <td><?= date("M d, Y", strtotime($row['payment_date'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No payment records found.</p>
        <?php endif; ?>
    </div>

</div>

<?php include(ROOT_PATH . "utils/footer.php"); ?>

</body>
</html>