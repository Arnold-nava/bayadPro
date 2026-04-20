<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");
require_once(ROOT_PATH . "app/model/tuition.php"); // removed duplicate db.php require

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
$result = calculateTuition($conn, $student_id, "1st Sem", "2026-2027");
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

/* CHECK IF ALREADY PAID */
$alreadyPaid = false;
$sqlTuition = "SELECT id FROM student_tuition WHERE student_id = ? ORDER BY id DESC LIMIT 1";
$stmtT = $conn->prepare($sqlTuition);
$stmtT->bind_param("i", $student_id);
$stmtT->execute();
$tuitionRow = $stmtT->get_result()->fetch_assoc();

if ($tuitionRow) {
    $sqlPaid = "SELECT id FROM payments WHERE tuition_id = ? AND payment_status = 'paid' LIMIT 1";
    $stmtP = $conn->prepare($sqlPaid);
    $stmtP->bind_param("i", $tuitionRow['id']);
    $stmtP->execute();
    $alreadyPaid = (bool) $stmtP->get_result()->fetch_assoc();
}

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

        <?php if ($alreadyPaid): ?>
            <div class="card-footer-actions">
                <span class="btn btn-primary" style="background:gray;cursor:default;">
                    ✅ Already Paid
                </span>
                <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-outline">
                    Payment History
                </a>
            </div>

            
            

        <?php else: ?>
            <!-- PAYMENT FORM -->
            <form action="<?= BASE_URL ?>app/views/client/gcash.php" method="POST">
                <div class="card-footer-actions">
                    <button type="submit" class="btn btn-primary">
                        Pay Now
                    </button>
                    <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-outline">
                        Payment History
                    </a>
                </div>
            </form>

        <?php endif; ?>

    </div>

</div>

<?php include(ROOT_PATH . "utils/footer.php"); ?>


