<?php
session_start();
require "db.php";

if($_SESSION['role'] != 'admin'){
    die("Access denied.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM students WHERE id=?");
$stmt->execute([$id]);

header("Location: students.php");
?>
