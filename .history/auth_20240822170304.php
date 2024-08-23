<?php
session_start();

// Function to check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if the user is an admin
function isUserAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to perform authorization check


    if (!isUserAdmin()) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        exit();
    }
}
?>
