<?php
session_start();
include "conn.php"; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function sendEmail($username, $email, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Use your email address
        $mail->Password = 'your-app-password'; // Use your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', $username);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Email Verification";
        $mail->Body = '<html>
<body>
  <p>Hi ' . htmlspecialchars($username) . ',</p>
  <p>Please verify your email address by clicking the button below:</p>
  <a href="http://localhost/libray%20system/verify.php?token=' . urlencode($code) . '" style="background-color: #4CAF50; color: white; padding: 15px 25px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;">Verify Email</a>
</body>
</html>';

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['Register'])) {
    $username = checkInput($_POST['username']);
    $email = checkInput($_POST['email']);
    $password = checkInput($_POST['password']);
    $confirmPassword = checkInput($_POST['confirmPassword']);

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['status'] = "<div class='alert alert-danger'>All fields are required!</div>";
    } elseif (!isValidEmail($email)) {
        $_SESSION['status'] = "<div class='alert alert-danger'>Invalid email format!</div>";
    } elseif ($password !== $confirmPassword) {
        $_SESSION['status'] = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['status'] = "<div class='alert alert-danger'>Email already exists.</div>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $code = rand(10000, 99999);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, code) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $code);

            if ($stmt->execute()) {
                sendEmail($username, $email, $code);
                $_SESSION['status'] = "<div class='alert alert-success'>Registration successful. Please check your email to verify your account.</div>";
                header("Location: register.php");
                exit();
            } else {
                $_SESSION['status'] = "<div class='alert alert-danger'>Registration failed.</div>";
            }
        }
    }
}

function checkInput($input) {
    return htmlspecialchars(trim(stripslashes($input)));
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 50px;
            border-radius: 1rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card shadow">
        <div class="card-body">
            <h2 class="text-center mb-4">User Registration</h2>
            <?php if (isset($_SESSION['status'])) echo $_SESSION['status']; ?>
            <form id="registrationForm" method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Enter your username" value="<?php echo htmlspecialchars($username); ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required placeholder="Confirm your password">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <button type="submit" name="Register" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('registrationForm').addEventListener('submit', function(event) {
    let password = document.getElementById('password').value;
    let confirmPassword = document.getElementById('confirmPassword').value;

    if (password !== confirmPassword) {
        event.preventDefault(); 
        alert('Passwords do not match! Please try again.');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
