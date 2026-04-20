<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SAFE DB PATH (no ../../../)
require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['user']);
    $pass = trim($_POST['pass']);

    if (empty($username) || empty($pass)) {
        echo "Please enter username or password!";
        exit();
    }

    $sql = "SELECT student_id, username, password 
            FROM user_cred 
            WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $error = "";

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        if (password_verify($pass, $row['password'])) {

            $_SESSION['id'] = $row['student_id'];

            header("Location: " . BASE_URL . "index.php");
            exit();

        } else {
            $error = "Wrong password!";
        }

    } else {
        $error = "User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/asset/css/login.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<form method="POST">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <div class="input-group">
        <label>Username</label>
        <input type="text" name="user" required>
    </div>
        

    <label>Password</label>
    <div class="input-group">
        <input type="password" name="pass" id="password" placeholder="Password" required>
        <span class="icon" onclick="togglePassword('password', this)">
            <i class="fa-solid fa-eye"></i>
        </span>
    </div>

    <button type="submit">Login</button>

    <p class="login-link">
        No account?
        <a href="<?= BASE_URL ?>app/views/client/register.php">Register</a>
    </p>
</form>

<script>
function togglePassword(id, iconWrapper) {
    const input = document.getElementById(id);
    const icon = iconWrapper.querySelector("i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>