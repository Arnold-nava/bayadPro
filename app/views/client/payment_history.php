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

$student_id = $_SESSION['id'];

$sql = "SELECT * FROM payments WHERE student_id = ? ORDER BY payment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$payments = $stmt->get_result();
?>

<?php include(ROOT_PATH . "utils/nav.php"); ?>

<div class="index">

    <!-- HEADER -->
    <div class="header-banner">
        <h1>Payment History</h1>
        <p>All your completed transactions in one place</p>
    </div>

    <!-- TABLE CARD -->
    <div class="payment-card">

        <?php if ($payments->num_rows > 0): ?>

        <table class="payment-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Method</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $payments->fetch_assoc()): ?>
                <tr class="clickable-row"
                    data-ref="<?= htmlspecialchars($row['payment_reference']) ?>"
                    data-amount="<?= number_format($row['amount_paid'],2) ?>"
                    data-status="<?= ucfirst($row['payment_status']) ?>"
                    data-method="<?= htmlspecialchars($row['payment_method']) ?>"
                    data-date="<?= date("M d, Y", strtotime($row['payment_date'])) ?>"
                >
                    <td><?= htmlspecialchars($row['payment_reference']) ?></td>
                    <td>₱<?= number_format($row['amount_paid'],2) ?></td>
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
            <div class="empty">No payment records yet</div>
        <?php endif; ?>

    </div>
</div>

<!-- RECEIPT MODAL -->
<div id="receiptModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Receipt</h2>

        <p><b>Reference:</b> <span id="m-ref"></span></p>
        <p><b>Amount:</b> ₱<span id="m-amount"></span></p>
        <p><b>Status:</b> <span id="m-status"></span></p>
        <p><b>Method:</b> <span id="m-method"></span></p>
        <p><b>Date:</b> <span id="m-date"></span></p>
    </div>
</div>

<?php include(ROOT_PATH . "utils/footer.php"); ?>

<script>
const modal = document.getElementById("receiptModal");
const closeBtn = document.querySelector(".close");

document.querySelectorAll(".clickable-row").forEach(row => {
    row.addEventListener("click", () => {
        document.getElementById("m-ref").innerText = row.dataset.ref;
        document.getElementById("m-amount").innerText = row.dataset.amount;
        document.getElementById("m-status").innerText = row.dataset.status;
        document.getElementById("m-method").innerText = row.dataset.method;
        document.getElementById("m-date").innerText = row.dataset.date;

        modal.style.display = "flex";
    });
});

closeBtn.onclick = () => modal.style.display = "none";

window.onclick = (e) => {
    if (e.target == modal) modal.style.display = "none";
};
</script>