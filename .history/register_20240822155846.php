<?php
include "conn.php"; // Include your database connection
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
        $message = "<div class='alert alert-danger'>All fields are required!</div>";
    } elseif (!isValidEmail($email)) {
        $message = "<div class='alert alert-danger'>Invalid email format!</div>";
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
            $stmt = mysqli_query($conn,"INSERT INTO users (username, password,confirm, email) VALUES ('$username','$hashedPassword','$hashedconfirm','$email')");
            if ($stmt) {
                $message = "<div class='alert alert-success'>Registration successful. You can now <a href='login.php'>login</a>.</div>";
            } else {
                $message = "<div class='alert alert-danger'>Insertion Error</div>";
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
