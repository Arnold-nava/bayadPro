<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

$error = $_SESSION['gcash_error'] ?? null;
unset($_SESSION['gcash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GCash Payment</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/asset/css/gcash.css">

</head>
<body>

<div class="gcash-card">

    <div class="gcash-header">
        <div class="gcash-logo">G<span>Cash</span></div>
        <p>Simulated Payment Portal</p>
    </div>

    <div class="amount-box">
        <div class="label">Amount to Pay</div>
        <div class="amount">₱<?= number_format($_SESSION['pay_amount'] ?? 0, 2) ?></div>
    </div>

    <?php if ($error): ?>
        <div class="error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>app/controllers/process_payment.php" method="POST">

        <label>GCash Mobile Number</label>
        <input type="text" name="gcash_number" placeholder="09XX XXX XXXX" maxlength="11" required>

        <label>GCash PIN</label>
        <div class="pin-row">
            <input type="password" name="gcash_pin" placeholder="••••••" maxlength="6" required>
        </div>

        <button type="submit" class="btn-pay">Confirm Payment</button>

    </form>

    <a href="<?= BASE_URL ?>app/views/client/payment.php" class="btn-cancel">✕ Cancel</a>

    <div class="secure">🔒 Secured by BayadPro Simulation</div>

</div>

</body>
</html>