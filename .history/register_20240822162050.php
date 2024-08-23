<?php
session_start();
include "conn.php"; 
// sending Email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';
function sendEmail($user, $email, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0; // Enable verbose debug output
        $mail->isSMTP();                       // Send using SMTP
        $mail->Host = 'smtp.gmail.com';        // Set the SMTP server to send through
        $mail->SMTPAuth = true;                // Enable SMTP authentication
        $mail->Username = 'cabdulahii723@gmail.com'; // SMTP username
        $mail->Password = 'qkkrrcipbtmhmdui'; // Use your App Password or regular password if 2FA is off
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port = 587;                     // TCP port to connect to

        $mail->setFrom('cabdulahii723@gmail.com',$user);
        $mail->addAddress($email);  

        // Content of the email
        // $mail->Body = 'your verify code is : '.$code.'</b>';

        $mail->isHTML(true);                    // Set email format to HTML
        $mail->Subject = "Here '.$user.' is the subject";
        $mail->Body = '<html>
<body>
  <p>Hi [User Name],</p>
  <p>Please verify your email address by clicking the button below:</p>
  <a href="http://localhost/libray%20system/verify.php?token='.$code.'" style="background-color: #4CAF50; color: white; padding: 15px 25px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;">Verify Email</a>
</body>
</html>';

        $mail->send();
        echo 'Message has b een sent';
    } catch (Exception $e) {
        echo "Message could not be sent. system problem : {$mail->ErrorInfo}";
    }
}
$message = ""; // Initialize message variable

// Initialize variables for form inputs
$username = '';
$email = '';
$password = '';
$confirmPassword = '';

if (isset($_POST['Register'])) {
    $username = checkInput($_POST['username']);
    $email = checkInput($_POST['email']);
    $password = checkInput($_POST['password']);
    $confirmPassword = checkInput($_POST['confirmPassword']);

    // Check for empty fields
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['status'] = "<div class='alert alert-danger'>All fields are required!</div>";
    } elseif (!isValidEmail($email)) {
        $_SESSION['status'] = "<div class='alert alert-danger'>Invalid email format!</div>";
    } elseif ($password !== $confirmPassword) {
        $message = "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        // Check if email already exists using prepared statements
        $stmt =mysqli_query($conn,"SELECT * FROM users WHERE email ='$email'");
       if (mysqli_num_rows($stmt)> 0) {
       
            $message = "<div class='alert alert-danger'>Email already exists.</div>";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $hashedconfirm = password_hash($confirmPassword, PASSWORD_DEFAULT);

            // Insert user into the database
            // $stmt = mysqli_query($conn,"INSERT INTO users (username, password,confirm, email) VALUES ('$username','$hashedPassword','$hashedconfirm','$email')");
            // if ($stmt) {
            //     $message = "<div class='alert alert-success'>Registration successful. You can now <a href='login.php'>login</a>.</div>";
            // } else {
            //     $message = "<div class='alert alert-danger'>Registation is failed</div>";
            // }
            $code = rand(10000, 99999);
            $query = mysqli_query($connection,"INSERT INTO users (email,username,password,confirm,code) VALUES ('$email','$user','$password','$confirm','$code')");
    
            if ($query){
                sendEmail($user,$email,$code);
                $message = "<div class='alert alert-success'>Registration successful. You can now <a href='login.php'>login</a>.</div>";
            header("location: verify.php");
                exit(0);
            }else{
               
        $message = "<div class='alert alert-danger'>Registation is failed</div>";
            header("location: register.php");
                exit(0);
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
            <?php if ($message) echo $message; ?>
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

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <!-- Submit Button -->
                <button type="submit" name="Register" class="btn btn-primary">Register</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('registrationForm').addEventListener('submit', function(event) {
    let password = document.getElementById('password').value;
    let confirmPassword = document.getElementById('confirmPassword').value;

    // Simple password match validation
    if (password !== confirmPassword) {
        event.preventDefault(); 
        alert('Passwords do not match! Please try again.');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
