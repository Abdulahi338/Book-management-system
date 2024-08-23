<?php
session_start();
include "conn.php";

$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    $_SESSION['status'] = "Invalid Token!";
    header("Location: register.php");
    exit();
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE code = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE users SET status = '1' WHERE code = ?");
        $updateStmt->bind_param("s", $token);

        if ($updateStmt->execute()) {
            $_SESSION['status'] = "Your account has been verified!";
            header("Location: welcome.php");
            exit();
        } else {
            $_SESSION['status'] = "Failed to update the status!";
        }
    } else {
        $_SESSION['status'] = "Invalid Token!";
    }

    header("Location: register.php");
    exit();
}
?>
