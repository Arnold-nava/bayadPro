<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/bayadPro/config/root.php");
require_once(ROOT_PATH . "config/db.php");

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['user']);
    $pass = trim($_POST['pass']);

    $sql = "SELECT student_id, username, password FROM user_cred WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

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
<meta charset="UTF-8">
<title>Login</title>

<link rel="stylesheet" href="<?= BASE_URL ?>public/asset/css/login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="auth-wrapper" id="wrapper">

    <!-- LOGIN -->
    <div class="panel login-panel">

        <form method="POST">
            <h2>Login</h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <input type="text" name="user" placeholder="Username" required>

            <div class="input-group">
                <input type="password" name="pass" id="pass" placeholder="Password" required>
                <span class="icon" onclick="togglePass()">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <button type="submit">Login</button>

            <p class="switch-text">
                No account?
                <a onclick="slide('register')">Create one</a>
            </p>

        </form>

    </div>

    <!-- REGISTER PROMO PANEL -->
    <div class="panel register-panel">

        <div class="info-box">
            <h2>New here?</h2>
            <p>Create an account to manage your tuition easily.</p>

            <a href="<?= BASE_URL ?>app/views/client/register.php" class="btn">
                Create Account
            </a>

            <p class="switch-text">
                Already have an account?
                <a onclick="slide('login')">Login</a>
            </p>
        </div>

    </div>

</div>

<script>
function togglePass() {
    const input = document.getElementById("pass");
    const icon = document.querySelector(".icon i");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    }
}

function slide(type) {
    const wrapper = document.getElementById("wrapper");

    if (type === "register") {
        wrapper.classList.add("slide");
    } else {
        wrapper.classList.remove("slide");
    }
}
</script>

</body>
</html>