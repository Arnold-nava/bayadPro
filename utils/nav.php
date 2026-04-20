<?php   
    require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
    require_once(ROOT_PATH . "config/db.php");

    $student_id = $_SESSION['id'];

    $sql = "SELECT * FROM student_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/asset/css/style.css">
</head>
<body>
    

<div class="container">
    <div class="profile">
        <span class="profile-img"><i class="fa-solid fa-user"></i></span>
        <span class="username"><?php echo $student['first_name']; ?></span>
        <span class="student_number"><p><b>Student Number:</b></p><?php echo $student['student_number']; ?></span>
    </div>

    <div class="nav-menu">
        <a href="<?= BASE_URL ?>index.php">Profile</a>
        <a href="<?= BASE_URL ?>app/views/client/payment_history.php">Payment History</a>
        <a href="<?= BASE_URL ?>app/views/client/balance.php">Balance</a>
    </div>

    <div class="action-btn">
        <a href="<?= BASE_URL ?>app/controllers/logout.php">Logout</a>
    </div>
</div>
</body>
</html>