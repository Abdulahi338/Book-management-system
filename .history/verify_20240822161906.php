<?php
session_start();
require("conn.php");
$token = isset($_GET['token']);
if(empty($token)){
        $_SESSION['status']="Invalid Token!!";
        header("location:register.php");
        exit;


    }else{
        $checkToken =mysqli_query($conn,"SELECT * from users where code='$token'");
        if(mysqli_num_rows($checkToken)>0){
            $updateToken =mysqli_query($conn,"UPDATE users set status='1'");
            if($updateToken){
                $_SESSION['status']="your account has been verified!!";
                header("location:welcome.php");
                exit;

            }

        }else{
            $_SESSION['status']="Invalid Update!!";
            header("location:register.php");
            exit;
            
        }
    }


?>