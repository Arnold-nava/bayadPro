<?php
session_start();
require_once("../../../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_number = $_POST['student_number'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. VALIDATION
    if (empty($student_number) || empty($username) || empty($password)) {
        echo "Fill all required fields!";
        exit();
    }

    // HASH PASSWORD
    $password = password_hash($password, PASSWORD_DEFAULT);

    // 2. FIND STUDENT IN student_list
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

    // 4. CREATE LOGIN ACCOUNT
    $insert = "INSERT INTO user_cred (student_id, username, password)
               VALUES (?, ?, ?)";

    $insertStmt = $conn->prepare($insert);
    $insertStmt->bind_param("iss", $student_id, $username, $password);
    $insertStmt->execute();

    // 5. AUTO LOGIN
    $_SESSION['id'] = $student_id;

    // 6. REDIRECT DASHBOARD
    header("Location: ../../../index.php");
    exit();
}
?>

<!-- REGISTER FORM -->
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