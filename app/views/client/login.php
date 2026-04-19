<?php
session_start();
require_once('../../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['user'];
    $pass = $_POST['pass'];

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

        if ($pass == $row['password']) {

            // IMPORTANT FIX
            $_SESSION['id'] = $row['student_id'];

            header("Location: ../../../index.php");
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
    username:<br>
    <input type="text" name="user"><br>

    password:<br>
    <input type="password" name="pass"><br>

    <button type="submit">login</button>
</form>