<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

// CHECK LOGIN
if (!isset($_SESSION['id'])) {
    header("Location: " . BASE_URL . "app/views/client/register.php");
    exit();
}

// GET STUDENT
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
    <div class="header">
        <h1>Welcome, <?= htmlspecialchars($student['first_name']) ?> 👋</h1>
        <p class="subtext">Here is your student dashboard overview</p>
    </div>

    <div class="card-grid">
        <div class="card">
            <div class="card-header">
                <span class="icon">📘</span>
                <span class="label">Program</span>
            </div>
            <div class="value"><?= htmlspecialchars($student['program']) ?></div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="icon">🎓</span>
                <span class="label">Year Level</span>
            </div>
            <div class="value"><?= htmlspecialchars($student['year_level']) ?></div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="icon">🆔</span>
                <span class="label">Student ID</span>
            </div>
            <div class="value"><?= htmlspecialchars($student['student_number']) ?></div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="icon">💳</span>
                <span class="label">Payment Status</span>
            </div>
            <div class="value">
                <span class="badge badge-success">Active</span>
            </div>
        </div>
    </div>

    <div class="actions-section">
        <h3>Quick Actions</h3>
        <div class="btn-group">
            <a href="<?= BASE_URL ?>app/views/client/payment_history.php" class="btn btn-primary">
                View Payments
            </a>
        </div>
    </div>
</div>

</body>
</html>