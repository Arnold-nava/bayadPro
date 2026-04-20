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

    if ($result->num_rows == 1) {

        $row = $result->fetch_assoc();

        // SAFE PASSWORD CHECK (IMPORTANT FIX)
        if (password_verify($pass, $row['password'])) {

            $_SESSION['id'] = $row['student_id'];

            header("Location: " . BASE_URL . "index.php");
            exit();

        } else {
            echo "Wrong password!";
        }

    } else {
        echo "User not found!";
    }
}
?>

<form method="POST">
    <h2>Login</h2>

    username:<br>
    <input type="text" name="user"><br><br>

    password:<br>
    <input type="password" name="pass"><br><br>

    <button type="submit">login</button>
</form>