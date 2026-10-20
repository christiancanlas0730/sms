<?php
session_start();
require "db.php";

if($_SESSION['role'] != 'admin'){
    die("Access denied.");
}

if(!isset($_GET['id'])){
    die("Invalid ID");
}

$id = $_GET['id'];

// FETCH STUDENT
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Student not found");
}

// UPDATE STUDENT
if(isset($_POST['update'])){
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $full_name = $first_name . ' ' . $middle_name . ' ' . $last_name;

    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $course = $_POST['course'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $sql = "UPDATE students SET name=?, birthdate=?, age=?, course=?, department=?, year_level=?, email=?, contact=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$full_name,$birthdate,$age,$course,$department,$year,$email,$contact,$id]);

    header("Location: students.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:"Segoe UI", sans-serif;
    background:#f1f5f9;
    padding:40px 20px;
}

/* Container */
.container{
    max-width:600px;
    margin:auto;
    background:#ffffff;
    padding:35px;
    border-radius:16px;
    box-shadow:0 10px 40px rgba(0,0,0,0.06);
    animation:fadeIn 0.3s ease-in-out;
}

/* Title */
h2{
    text-align:center;
    margin-bottom:25px;
    font-weight:600;
    color:#0f172a;
}

/* Labels */
label{
    display:block;
    font-size:14px;
    font-weight:600;
    margin-bottom:6px;
    color:#475569;
}

/* Inputs */
input[type="text"],
input[type="number"],
input[type="email"],
input[type="date"],
select{
    width:100%;
    padding:11px 14px;
    margin-bottom:18px;
    border-radius:10px;
    border:1px solid #cbd5e1;
    background:#f8fafc;
    font-size:14px;
    transition:0.2s ease;
}

input:focus,
select:focus{
    outline:none;
    border-color:#2563eb;
    box-shadow:0 0 0 3px rgba(37,99,235,0.15);
}

input[readonly]{
    background:#e2e8f0;
    cursor:not-allowed;
}

/* Button */
button{
    width:100%;
    padding:13px;
    border:none;
    border-radius:10px;
    background:#0f172a;
    color:white;
    font-size:15px;
    font-weight:500;
    cursor:pointer;
    transition:0.2s ease;
}

button:hover{
    background:#1e293b;
    transform:translateY(-2px);
}

/* Back Link */
a.back-btn{
    display:inline-block;
    margin-top:18px;
    text-decoration:none;
    color:#2563eb;
    font-weight:500;
}

a.back-btn:hover{
    text-decoration:underline;
}

/* Animation */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(15px);}
    to{opacity:1; transform:translateY(0);}
}

/* Responsive */
@media(max-width:600px){
    .container{
        padding:25px;
    }
}
/* Button Group */
.button-group{
    display:flex;
    gap:12px;
    margin-top:10px;
}

/* Button Group (Stacked) */
.button-group{
    margin-top:15px;
}

/* Both buttons identical */
.button-group button,
.button-group a{
    display:block;
    width:100%;
    text-align:center;
    padding:13px;
    border:none;
    border-radius:10px;
    background:#0f172a;
    color:white;
    font-size:15px;
    font-weight:500;
    cursor:pointer;
    transition:0.2s ease;
    text-decoration:none;
    margin-bottom:12px;
}

/* Hover effect */
.button-group button:hover,
.button-group a:hover{
    background:#1e293b;
    transform:translateY(-2px);
}
</style>
</head>
<body>

<div class="container">
<h2>Edit Student Information</h2>
<form method="POST">

<label>First Name:</label>
<input type="text" name="first_name" value="<?php echo explode(' ', $student['name'])[0]; ?>" required>

<label>Middle Name:</label>
<input type="text" name="middle_name" value="<?php $parts = explode(' ', $student['name']); echo isset($parts[1])?$parts[1]:''; ?>">

<label>Last Name:</label>
<input type="text" name="last_name" value="<?php $parts = explode(' ', $student['name']); echo isset($parts[2])?$parts[2]:''; ?>" required>

<label>Birthdate:</label>
<input type="date" name="birthdate" id="birthdate" value="<?php echo $student['birthdate']; ?>" required>

<label>Age:</label>
<input type="number" name="age" id="age" value="<?php echo $student['age']; ?>" readonly>

<label>Course:</label>
<select name="course" id="course" required>
<option value="BSIT" <?php if($student['course']=='BSIT') echo 'selected'; ?>>BSIT</option>
<option value="BSCS" <?php if($student['course']=='BSCS') echo 'selected'; ?>>BSCS</option>
<option value="BSECE" <?php if($student['course']=='BSECE') echo 'selected'; ?>>BSECE</option>
<option value="BSED" <?php if($student['course']=='BSED') echo 'selected'; ?>>BSED</option>
</select>

<label>Department:</label>
<input type="text" name="department" id="department" value="<?php echo $student['department']; ?>" readonly>

<label>Year Level:</label>
<input type="number" name="year" value="<?php echo $student['year_level']; ?>" required>

<label>Email:</label>
<input type="email" name="email" value="<?php echo $student['email']; ?>">

<label>Contact:</label>
<input type="text" name="contact" value="<?php echo $student['contact']; ?>">

 <div class="button-group">
            <button type="submit" name="update">Update Student</button>
            <a href="students.php" class="cancel-button">Cancel</a>
        </div>

<script>
document.getElementById("birthdate").addEventListener("change", function(){
    let birthdate = new Date(this.value);
    let today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    let m = today.getMonth() - birthdate.getMonth();
    if(m<0||(m===0 && today.getDate()<birthdate.getDate())) age--;
    document.getElementById("age").value = age;
});

document.getElementById("course").addEventListener("change", function(){
    let map = {BSIT:"College of Information Technology", BSECE:"College of Engineering", BSCS:"College of Computer Science", BSED:"College of Education"};
    document.getElementById("department").value = map[this.value]||"";
});
</script>

</body>
</html>