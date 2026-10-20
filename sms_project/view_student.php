<?php
session_start();
require "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(!isset($_GET['id'])){
    die("Invalid ID");
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Student not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    font-family:"Segoe UI", sans-serif;
    background:#f1f5f9;
    margin:0;
}

.header{
    background:#0f172a;
    color:white;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header a{
    background:#2563eb;
    color:white;
    padding:8px 14px;
    text-decoration:none;
    border-radius:6px;
}

.header a:hover{
    background:#1d4ed8;
}

.container{
    max-width:700px;
    margin:40px auto;
    background:white;
    padding:35px;
    border-radius:16px;
    box-shadow:0 10px 40px rgba(0,0,0,0.08);
}

h2{
    margin-bottom:25px;
    color:#0f172a;
    text-align:center;
}

.profile-row{
    display:flex;
    justify-content:space-between;
    padding:12px 0;
    border-bottom:1px solid #e2e8f0;
}

.profile-row span:first-child{
    font-weight:600;
    color:#475569;
}

.profile-row span:last-child{
    color:#0f172a;
}

.button-group{
    margin-top:25px;
}

.button-group a{
    display:block;
    width:100%;
    text-align:center;
    padding:12px;
    border-radius:10px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    font-weight:500;
    margin-bottom:10px;
    transition:0.2s;
}

.button-group a:hover{
    background:#1e293b;
    transform:translateY(-2px);
}
</style>

</head>
<body>

<div class="header">
    <div>Student Profile</div>
    <a href="students.php">Back</a>
</div>

<div class="container">
<h2><?php echo htmlspecialchars($student['name']); ?></h2>

<div class="profile-row">
    <span>ID</span>
    <span><?php echo htmlspecialchars($student['id']); ?></span>
</div>

<div class="profile-row">
    <span>Email</span>
    <span><?php echo htmlspecialchars($student['email']); ?></span>
</div>

<div class="profile-row">
    <span>Birthdate</span>
    <span><?php echo htmlspecialchars($student['birthdate']); ?></span>
</div>

<div class="profile-row">
    <span>Age</span>
    <span><?php echo htmlspecialchars($student['age']); ?></span>
</div>

<div class="profile-row">
    <span>Course</span>
    <span><?php echo htmlspecialchars($student['course']); ?></span>
</div>

<div class="profile-row">
    <span>Department</span>
    <span><?php echo htmlspecialchars($student['department']); ?></span>
</div>

<div class="profile-row">
    <span>Year Level</span>
    <span><?php echo htmlspecialchars($student['year_level']); ?></span>
</div>

<div class="profile-row">
    <span>Contact</span>
    <span><?php echo htmlspecialchars($student['contact']); ?></span>
</div>

<div class="button-group">
    <a href="edit_student.php?id=<?php echo $student['id']; ?>">Edit Student</a>
    <a href="students.php">Back to List</a>
</div>

</div>

</body>
</html>