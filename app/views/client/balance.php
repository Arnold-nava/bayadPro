<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");
require_once(ROOT_PATH . "app/model/tuition.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

$student_id = $_SESSION['id'];

/* GET STUDENT */
$sql = "SELECT * FROM student_list WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

/* GET GPA */
$sql = "SELECT gpa FROM student_gpa WHERE student_id = ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$gpaRow = $stmt->get_result()->fetch_assoc();
$gpa = $gpaRow['gpa'] ?? 0;

/* TUITION */
$result   = calculateTuition($conn, $student_id, "1st Sem", "2026-2027");
$base     = $result['base'];
$discount = $result['discount'];
$balance  = $result['final'];

/* FLASH MESSAGE */
$flash = null;
if (isset($_SESSION['payment_msg'])) {
    [$flashType, $flashMsg] = explode(":", $_SESSION['payment_msg'], 2);
    $flash = ['type' => $flashType, 'msg' => $flashMsg];
    unset($_SESSION['payment_msg']);
}

/* GET TUITION ROW */
$tuitionRow = null;
$sqlTuition = "SELECT id FROM student_tuition WHERE student_id = ? ORDER BY id DESC LIMIT 1";
$stmtT = $conn->prepare($sqlTuition);
$stmtT->bind_param("i", $student_id);
$stmtT->execute();
$tuitionRow = $stmtT->get_result()->fetch_assoc();

/* CHECK IF ALREADY FULLY PAID */
$alreadyPaid = false;
if ($tuitionRow) {
    $sqlPaid = "SELECT id FROM payments 
                WHERE tuition_id = ? AND payment_type = 'full' AND payment_status = 'paid' LIMIT 1";
    $stmtP = $conn->prepare($sqlPaid);
    $stmtP->bind_param("i", $tuitionRow['id']);
    $stmtP->execute();
    $alreadyPaid = (bool) $stmtP->get_result()->fetch_assoc();
}

/* CHECK INSTALLMENTS */
$installments          = [];
$nextUnpaidInstallment = null;
$allInstallmentsPaid   = false;
$hasAnyPaidInstallment = false;

if ($tuitionRow && !$alreadyPaid) {
    $sqlInst = "SELECT * FROM payment_installments 
                WHERE tuition_id = ? ORDER BY month_number ASC";
    $stmtInst = $conn->prepare($sqlInst);
    $stmtInst->bind_param("i", $tuitionRow['id']);
    $stmtInst->execute();
    $installments = $stmtInst->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!empty($installments)) {
        foreach ($installments as $inst) {
            if ($inst['status'] === 'unpaid' && $nextUnpaidInstallment === null) {
                $nextUnpaidInstallment = $inst;
            }
            if ($inst['status'] === 'paid') {
                $hasAnyPaidInstallment = true;
            }
        }
        // ✅ Fix 1: require all 5 months before marking fully paid
        $allInstallmentsPaid = $nextUnpaidInstallment === null && count($installments) >= 5;
    }
}

/* CALCULATE REMAINING BALANCE FOR INSTALLMENTS */
if (!empty($installments)) {
    $paidSoFar = 0;
    foreach ($installments as $inst) {
        if ($inst['status'] === 'paid') {
            $paidSoFar += $inst['amount_due'];
        }
    }
    $balance = $balance - $paidSoFar;
}



/* STORE AMOUNT IN SESSION FOR GCASH PAGE */
$_SESSION['pay_amount'] = $balance;
?>

<?php include(ROOT_PATH . "utils/nav.php"); ?>

<div class="index">

    <div class="header-banner">
        <h1>💳 Tuition Payment</h1>
        <p>Review your balance and pay your tuition</p>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= htmlspecialchars($flash['type']) ?>">
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
    <?php endif; ?>

    <div class="profile-card">

        <div class="profile-info-grid">

            <div class="info-item">
                <span class="label">Base Tuition</span>
                <div class="value">₱<?= number_format($base, 2) ?></div>
            </div>

            <div class="info-item">
                <span class="label">GPA</span>
                <div class="value"><?= $gpa ?></div>
            </div>

            <div class="info-item">
                <span class="label">Discount</span>
                <div class="value">₱<?= number_format($discount, 2) ?></div>
            </div>

            <div class="info-item">
                <span class="label">Balance</span>
                <div class="value" style="font-size:20px;font-weight:bold;color:green;">
                    ₱<?= number_format($balance, 2) ?>
                </div>
            </div>

        </div>

        <?php if ($alreadyPaid || $allInstallmentsPaid): ?>

            <!-- FULLY PAID -->
            <div class="card-footer-actions">
                <span class="btn btn-primary" style="background:gray;cursor:default;">
                    ✅ Fully Paid
                </span>
                <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-outline">
                    Payment History
                </a>
            </div>

        <?php elseif (!empty($installments) && $nextUnpaidInstallment !== null): ?>

            <!-- ON INSTALLMENT PLAN -->
            <div style="margin: 16px 25px; font-size:1.2rem; color:#555;">
                📅 Installment Plan — Month <?= $nextUnpaidInstallment['month_number'] ?> of 5
                &nbsp;|&nbsp; Due: <?= $nextUnpaidInstallment['due_date'] ?>
            </div>

            <div style="margin-bottom:16px;">
                <?php foreach ($installments as $inst): ?>
                    <span class="monthly-pay" style="
                        background: <?= $inst['status'] === 'paid' ? '#d4edda' : '#f8d7da' ?>;
                        color: <?= $inst['status'] === 'paid' ? '#155724' : '#721c24' ?>;">
                        Month <?= $inst['month_number'] ?>: <?= $inst['status'] === 'paid' ? '✅ Paid' : '⏳ Unpaid' ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <form action="<?= BASE_URL ?>app/views/client/gcash.php" method="POST">
                <input type="hidden" name="payment_type" value="installment">
                <input type="hidden" name="installment_id" value="<?= $nextUnpaidInstallment['id'] ?>">
                <?php $_SESSION['pay_amount'] = $nextUnpaidInstallment['amount_due']; ?>
                <div class="card-footer-actions">
                    <button type="submit" class="btn btn-primary">
                        💳 Pay Month <?= $nextUnpaidInstallment['month_number'] ?>
                        — ₱<?= number_format($nextUnpaidInstallment['amount_due'], 2) ?>
                    </button>
                    <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-outline">
                        Payment History
                    </a>
                </div>
            </form>

            <!-- CANCEL INSTALLMENT — always allowed -->
            <form action="<?= BASE_URL ?>app/controllers/cancel_installment.php" method="POST"
                  onsubmit="return confirm('Cancel remaining installments and pay ₱<?= number_format($balance, 2) ?> in full instead?')">
                <div class="cancel-installment">
                    <button type="submit" class="btn btn-outline" style="color:red;border-color:red;">
                        ⚠️ Cancel Remaining & Pay ₱<?= number_format($balance, 2) ?> in Full
                    </button>
                </div>
            </form>

        <?php else: ?>

            <!-- NO PLAN YET OR CANCELLED — CHOOSE PAYMENT TYPE -->
            <div class="card-footer-actions">

                <!-- FULL PAYMENT -->
                <form action="<?= BASE_URL ?>app/views/client/gcash.php" method="POST">
                    <input type="hidden" name="payment_type" value="full">
                    <button type="submit" class="btn btn-primary">
                        💳 Pay Full — ₱<?= number_format($balance, 2) ?>
                    </button>
                </form>

                <!-- SETUP INSTALLMENT -->
                <form action="<?= BASE_URL ?>app/controllers/setup_installments.php" method="POST">
                    <button type="submit" class="btn btn-outline">
                        📅 Pay Monthly — ₱<?= number_format($balance / 5, 2) ?>/mo × 5
                    </button>
                </form>

                <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-outline">
                    Payment History
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php include(ROOT_PATH . "utils/footer.php"); ?>