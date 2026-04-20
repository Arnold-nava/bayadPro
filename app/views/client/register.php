<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

$error = ""; // ✅ FIXED

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_number = trim($_POST['student_number']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($student_number) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Fill all required fields!";
    }

    if (empty($error) && $password !== $confirm_password) {
        $error = "Passwords do not match!";
    }

    if (empty($error)) {

        // 3. FIND STUDENT
        $sql = "SELECT id FROM student_list WHERE student_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $error = "Student not found. Contact admin.";
        } else {

            $student = $result->fetch_assoc();
            $student_id = $student['id'];

            // 4. CHECK IF ALREADY REGISTERED
            $check = "SELECT id FROM user_cred WHERE student_id = ?";
            $checkStmt = $conn->prepare($check);
            $checkStmt->bind_param("i", $student_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $error = "Account already exists!";
            } else {

                // 5. HASH PASSWORD
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $role = "student";

                // 6. INSERT ACCOUNT
                $insert = "INSERT INTO user_cred (student_id, username, password, role)
                           VALUES (?, ?, ?, ?)";

                $insertStmt = $conn->prepare($insert);
                $insertStmt->bind_param("isss", $student_id, $username, $hashedPassword, $role);
                $insertStmt->execute();

                // 7. LOGIN USER
                $_SESSION['id'] = $student_id;

                header("Location: " . BASE_URL . "index.php");
                exit();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>


    <link rel="stylesheet" href="<?= BASE_URL ?>public/asset/css/login.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<form method="POST">
    <h2>Student Register</h2>

    <!-- ERROR DISPLAY -->
    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <div class="input-group">
        <label>Student Number</label>
    <input type="text" name="student_number" required>
    </div>

    <div class="input-group">
        <label>Username</label>
        <input type="text" name="username" required>
    </div>

    <label>Password</label>
    <div class="input-group">
        <input type="password" name="password" id="password" required>
        <span class="icon" onclick="togglePassword('password', this)">
            <i class="fa-solid fa-eye"></i>
        </span>
    </div>

    <label>Confirm Password</label>
    <div class="input-group">
        <input type="password" name="confirm_password" id="confirm_password" required>
        <span class="icon" onclick="togglePassword('confirm_password', this)">
            <i class="fa-solid fa-eye"></i>
        </span>
    </div>

    <button type="submit">Register</button>

    <p class="login-link">
        Already have an account?
        <a href="<?= BASE_URL ?>app/views/client/login.php">Login</a>
    </p>
</form>

<script>
function togglePassword(id, iconWrapper) {
    const input = document.getElementById(id);
    const icon = iconWrapper.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}
</script>

</body>
</html>