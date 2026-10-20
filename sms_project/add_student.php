<?php
session_start();
require "db.php";

if($_SESSION['role'] != 'admin'){
    die("Access denied.");
}

$successMessage = "";
$errorMessage = "";

if(isset($_POST['add'])){

    $name = $_POST['name'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $course = $_POST['course'];
    $department = $_POST['department'];
    $year = $_POST['year'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    // Auto username (remove spaces, lowercase)
    $username = strtolower(str_replace(" ", "", $name));

    // Default password
    $defaultPassword = "student123";
    $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

    try {

        $conn->beginTransaction();

        // Check duplicate email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if($check->rowCount() > 0){
            throw new Exception("An account with this email address already exists. Please use a different email address.");
        }

        // Insert into users
        $stmtUser = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmtUser->execute([$username, $email, $hashedPassword]);

        $user_id = $conn->lastInsertId();

        // Insert into students
        $stmtStudent = $conn->prepare("INSERT INTO students 
            (user_id, name, birthdate, age, course, department, year_level, email, contact) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtStudent->execute([
            $user_id,
            $name,
            $birthdate,
            $age,
            $course,
            $department,
            $year,
            $email,
            $contact
        ]);

        $conn->commit();

        $successMessage = "Student Added Successfully!<br>
        <strong>Login Details:</strong><br>
        Username: $username<br>
        Password: $defaultPassword";

    } catch(Exception $e){
        $conn->rollBack();
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Student</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: "Segoe UI", sans-serif;
    background: #f1f5f9;
    display: flex;
    justify-content: center;
    padding: 40px 20px;
    color: #1e293b;
}

/* Container */
.container {
    background: #ffffff;
    width: 100%;
    max-width: 520px;
    padding: 35px;
    border-radius: 16px;
    box-shadow: 0 10px 35px rgba(0,0,0,0.06);
    animation: fadeIn 0.3s ease-in-out;
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    font-weight: 600;
}

/* Labels */
label {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 6px;
    display: block;
    color: #475569;
}

/* Inputs */
input, select {
    width: 100%;
    padding: 11px 14px;
    margin-bottom: 18px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 14px;
    transition: 0.2s ease;
    background: #f8fafc;
}

input:focus, select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
}

/* Button Group */
.button-group {
    margin-top: 10px;
}

.button-group button,
.button-group a {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 10px;
    transition: 0.2s ease;
}

/* Primary */
.button-group button {
    background: #0f172a;
    color: white;
    border: none;
}

.button-group button:hover {
    background: #1e293b;
}

/* Cancel */
.button-group a.cancel-button {
    background: #e2e8f0;
    color: #1e293b;
}

.button-group a.cancel-button:hover {
    background: #cbd5e1;
}

/* Success */
.success {
    background: #ecfdf5;
    border: 1px solid #10b981;
    padding: 15px;
    border-radius: 10px;
    color: #065f46;
    margin-bottom: 20px;
    font-size: 14px;
}

/* Error */
.error {
    background: #fef2f2;
    border: 1px solid #ef4444;
    padding: 15px;
    border-radius: 10px;
    color: #7f1d1d;
    margin-bottom: 20px;
    font-size: 14px;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 600px) {
    .container {
        padding: 25px;
    }
}
</style>
</head>
<body>

<div class="container">
    <h2>Add Student</h2>

    <?php if($successMessage): ?>
        <div class="success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <?php if($errorMessage): ?>
        <div class="error"><?php echo $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Birthdate:</label>
        <input type="date" name="birthdate" id="birthdate" required>

        <label>Age:</label>
        <input type="number" name="age" id="age" readonly>

        <label>Course:</label>
        <select name="course" id="course" required>
            <option value="">Select Course</option>
            <option value="BSIT">BSIT</option>
            <option value="BSCS">BSCS</option>
            <option value="BSECE">BSECE</option>
            <option value="BSED">BSED</option>
        </select>

        <label>Department:</label>
        <input type="text" name="department" id="department" readonly>

        <label>Year Level:</label>
        <input type="number" name="year" min="1" max="4" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Contact:</label>
        <input type="text" name="contact" required>

        <div class="button-group">
            <button type="submit" name="add">Add Student</button>
            <a href="admin_dashboard.php" class="cancel-button">Cancel</a>
        </div>

    </form>
</div>

<script>
document.getElementById("birthdate").addEventListener("change", function(){
    let birthdate = new Date(this.value);
    let today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    let m = today.getMonth() - birthdate.getMonth();
    if(m < 0 || (m === 0 && today.getDate() < birthdate.getDate())){
        age--;
    }
    document.getElementById("age").value = age;
});

document.getElementById("course").addEventListener("change", function(){
    let map = {
        BSIT: "College of Information Technology",
        BSCS: "College of Computer Science",
        BSECE: "College of Engineering",
        BSED: "College of Education"
    };
    document.getElementById("department").value = map[this.value] || "";
});
</script>

</body>
</html>