<?php
session_start();
require "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errorMessage = "";

/* =========================
   FETCH USER (for password verification)
========================= */
$userStmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

/* =========================
   UPDATE LOGIC
========================= */
if(isset($_POST['update'])){

    $name = $_POST['name'];
    $birthdate = $_POST['birthdate'];
    $age = $_POST['age'];
    $course = $_POST['course'];
    $department = $_POST['department'];
    $year_level = $_POST['year_level'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try{

        $conn->beginTransaction();

        /* ---- Update student table ---- */
        $updateStudent = $conn->prepare("UPDATE students 
            SET name=?, birthdate=?, age=?, course=?, department=?, year_level=?, email=?, contact=? 
            WHERE user_id=?");

        $updateStudent->execute([
            $name,
            $birthdate,
            $age,
            $course,
            $department,
            $year_level,
            $email,
            $contact,
            $user_id
        ]);

        /* ---- Update password if provided ---- */
        if(!empty($new_password)){

            if(!password_verify($current_password, $user['password'])){
                throw new Exception("Current password is incorrect.");
            }

            if($new_password !== $confirm_password){
                throw new Exception("New passwords do not match.");
            }

            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            $updateUser = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $updateUser->execute([$hashedPassword, $user_id]);
        }

        $conn->commit();

        header("Location: dashboard.php");
        exit();

    } catch(Exception $e){
        $conn->rollBack();
        $errorMessage = $e->getMessage();
    }
}

/* =========================
   FETCH STUDENT DATA
========================= */
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(120deg, #0f172a, #1e293b);
    color:#e2e8f0;
}

.container{
    max-width:750px;
    margin:80px auto;
    background:rgba(30,41,59,0.9);
    backdrop-filter:blur(10px);
    padding:40px;
    border-radius:20px;
    border:1px solid #334155;
}

h2{
    text-align:center;
    margin-bottom:30px;
    font-weight:600;
    color:#a78bfa;
}

.profile-row{
    display:flex;
    flex-direction:column;
    margin-bottom:18px;
}

.profile-row span:first-child{
    font-size:14px;
    margin-bottom:6px;
    color:#94a3b8;
}

.profile-row input{
    width:100%;
    padding:11px 14px;
    border-radius:10px;
    border:1px solid #334155;
    background:#1e293b;
    color:#fff;
    font-size:14px;
}

.profile-row input:focus{
    outline:none;
    border-color:#a78bfa;
    box-shadow:0 0 0 3px rgba(167,139,250,0.2);
}

.button{
    display:inline-block;
    margin-top:20px;
    padding:11px 20px;
    border-radius:10px;
    border:none;
    background:#a78bfa;
    color:white;
    font-weight:500;
    cursor:pointer;
    transition:0.2s ease;
}

.button:hover{
    background:#8b5cf6;
}

a.button{
    background:rgba(167,139,250,0.15);
    border:1px solid #a78bfa;
    color:#a78bfa;
    text-decoration:none;
}

a.button:hover{
    background:#a78bfa;
    color:white;
}

.error{
    background:rgba(220,38,38,0.15);
    border:1px solid rgba(220,38,38,0.4);
    padding:15px;
    border-radius:10px;
    text-align:center;
    color:#f87171;
    margin-bottom:20px;
}
</style>
</head>
<body>

<div class="container">
<h2>My Profile</h2>

<?php if(!empty($errorMessage)): ?>
<div class="error"><?php echo $errorMessage; ?></div>
<?php endif; ?>

<?php if($student): ?>

<form method="POST">

<div class="profile-row">
<span>Name</span>
<input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>">
</div>

<div class="profile-row">
<span>Birthdate</span>
<input type="date" name="birthdate" value="<?php echo htmlspecialchars($student['birthdate']); ?>">
</div>

<div class="profile-row">
<span>Age</span>
<input type="number" name="age" value="<?php echo htmlspecialchars($student['age']); ?>">
</div>

<div class="profile-row">
<span>Course</span>
<input type="text" name="course" value="<?php echo htmlspecialchars($student['course']); ?>">
</div>

<div class="profile-row">
<span>Department</span>
<input type="text" name="department" value="<?php echo htmlspecialchars($student['department']); ?>">
</div>

<div class="profile-row">
<span>Year Level</span>
<input type="number" name="year_level" value="<?php echo htmlspecialchars($student['year_level']); ?>">
</div>

<div class="profile-row">
<span>Email</span>
<input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
</div>

<div class="profile-row">
<span>Contact</span>
<input type="text" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>">
</div>

<hr style="margin:25px 0;border:1px solid #334155;">

<div class="profile-row">
<span>Current Password</span>
<input type="password" name="current_password" placeholder="Enter current password">
</div>

<div class="profile-row">
<span>New Password</span>
<input type="password" name="new_password" placeholder="Enter new password">
</div>

<div class="profile-row">
<span>Confirm New Password</span>
<input type="password" name="confirm_password" placeholder="Confirm new password">
</div>

<button type="submit" name="update" class="button">Update Profile</button>

</form>

<?php else: ?>
<div class="error">No student record found for your account.</div>
<?php endif; ?>

<br>
<a href="dashboard.php" class="button">Back to Dashboard</a>

</div>

<script>
document.addEventListener("DOMContentLoaded", function(){
    const birthdateInput = document.querySelector("input[name='birthdate']");
    const ageInput = document.querySelector("input[name='age']");

    birthdateInput.addEventListener("change", function(){
        let birthdate = new Date(this.value);
        let today = new Date();
        let age = today.getFullYear() - birthdate.getFullYear();
        let m = today.getMonth() - birthdate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }
        ageInput.value = age;
    });
});
</script>

</body>
</html>