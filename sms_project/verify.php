<?php
require "db.php";

if(isset($_GET['code'])){
    $code = $_GET['code'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_code=?");
    $stmt->execute([$code]);
    $user = $stmt->fetch();

    if($user){
        $stmt = $conn->prepare("UPDATE users SET verified=1, verification_code=NULL WHERE verification_code=?");
        $stmt->execute([$code]);
        echo "Your account has been verified! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid verification code.";
    }
} else {
    echo "No verification code provided.";
}
?>
