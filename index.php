<?php
    session_start();
    require_once("config/db.php");

    // 1. CHECK LOGIN
    if (!isset($_SESSION['id'])) {
        header("Location: app/views/client/register.php");
        exit();
    }

    // 2. GET STUDENT ID
    $student_id = $_SESSION['id'];

    // 3. GET STUDENT DATA
    $sql = "SELECT * FROM student_list WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
?>

    <h1>Welcome, <?php echo $student['first_name']; ?></h1>
    <p>Program: <?php echo $student['program']; ?></p>
    <p>Year: <?php echo $student['year_level']; ?></p>
