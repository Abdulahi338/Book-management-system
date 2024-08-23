<?php
session_start(); 
session_unset(); 
session_destroy();

header("Location: welcome.php"); // Redirect to login page
exit();
?>