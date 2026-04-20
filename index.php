<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

// LOGIN
if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

// STUDENT
$student_id = $_SESSION['id'];

$sql = "SELECT * FROM student_list WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

?>

<?php include("utils/nav.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">

    <title>Dashboard</title>
</head>
<body>

    <div class="index">
        <div class="header-banner">
            <div class="header-text">
                <h1>Welcome back, <?= htmlspecialchars($student['first_name']) ?> 👋</h1>
                <p>You are currently enrolled in <?= htmlspecialchars($student['program']) ?>.</p>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-info-grid">
                <div class="info-item">
                    <span class="label">Program</span>
                    <div class="value"><?= htmlspecialchars($student['program']) ?></div>
                </div>

                <div class="info-item">
                    <span class="label">Year Level</span>
                    <div class="value"><?= htmlspecialchars($student['year_level']) ?></div>
                </div>

                <div class="info-item">
                    <span class="label">Student ID</span>
                    <div class="value"><?= htmlspecialchars($student['student_number']) ?></div>
                </div>

                <div class="info-item">
                    <span class="label">Status</span>
                    <div class="value">
                        <span class="badge-success"><?= htmlspecialchars($student['status']) ?></span>
                    </div>
                </div>
            </div>

            <div class="card-footer-actions">
                <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-primary">
                    View Payment History
                </a>
            </div>
        </div>
    </div>
    
    <?php include("utils/footer.php"); ?>

</body>
</html>