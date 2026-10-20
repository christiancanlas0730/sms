<?php
session_start();
require "db.php";

if($_SESSION['role'] != 'admin'){
    die("Access denied");
}

$id = $_GET['id'];

// prevent deleting yourself
if($id == $_SESSION['user_id']){
    die("You cannot delete your own account.");
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->execute([$id]);

header("Location: manage_user.php");
?>
