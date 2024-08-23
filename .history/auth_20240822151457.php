<?php
include'';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

header('Content-Type: application/json');

// Function to check if the user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if the user is an admin
function isUserAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Function to perform authorization check
function checkAuthorization() {
    if (!isUserLoggedIn()) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit();
    }

    if (!isUserAdmin()) {
        echo json_encode(['status' => 'error', 'message' => 'Access denied']);
        exit();
    }
}
?>



