<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../../../config/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_number = trim($_POST['student_number']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 1. VALIDATION
    if (empty($student_number) || empty($username) || empty($password)) {
        echo "Fill all required fields!";
        exit();
    }

    // 2. FIND STUDENT
    $sql = "SELECT id FROM student_list WHERE student_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "Student not found. Contact admin.";
        exit();
    }

    $student = $result->fetch_assoc();
    $student_id = $student['id'];

    // 3. CHECK IF ALREADY REGISTERED
    $check = "SELECT id FROM user_cred WHERE student_id = ?";
    $checkStmt = $conn->prepare($check);
    $checkStmt->bind_param("i", $student_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "Account already exists!";
        exit();
    }

    // 4. HASH PASSWORD
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = "student";

    // 5. INSERT ACCOUNT
    $insert = "INSERT INTO user_cred (student_id, username, password, role)
               VALUES (?, ?, ?, ?)";

    $insertStmt = $conn->prepare($insert);
    $insertStmt->bind_param("isss", $student_id, $username, $hashedPassword, $role);
    $insertStmt->execute();

    // 6. LOGIN USER
    $_SESSION['id'] = $student_id;

    // 7. REDIRECT
    header("Location: " . BASE_URL . "index.php");
    exit();
}
?>

<form method="POST">
    <h2>Student Register</h2>

    Student Number:
    <input type="text" name="student_number"><br>

    Username:
    <input type="text" name="username"><br>

    Password:
    <input type="password" name="password"><br>

    <button type="submit">Register</button>
</form>