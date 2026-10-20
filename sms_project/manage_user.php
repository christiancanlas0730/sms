<?php
session_start();
require "db.php";

// Protect page
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch student info if exists
$stmt2 = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt2->execute([$user_id]);
$student = $stmt2->fetch(PDO::FETCH_ASSOC);

// Handle updates
if(isset($_POST['update'])){
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    // Optional password change
    $password_sql = '';
    $params = [$email, $contact, $user_id];
    if(!empty($_POST['password'])){
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ', password=?';
        $params = [$email, $contact, $password, $user_id];
    }

    $sql = "UPDATE users SET email=?, contact=? $password_sql WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $success = "Your info has been updated successfully!";

    // Refresh user info
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Your Account</title>
<style>
body{font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;background:#f4f6f9;margin:0;padding:20px;}
.container{max-width:600px;margin:40px auto;background:white;padding:30px;border-radius:12px;box-shadow:0 6px 25px rgba(0,0,0,0.1);}
h2{text-align:center;color:#2c3e50;}
label{display:block;margin-bottom:5px;color:#34495e;font-weight:600;}
input[type=text], input[type=email], input[type=password]{width:100%;padding:10px;margin-bottom:15px;border-radius:6px;border:1px solid #ccc;box-sizing:border-box;}
button{width:100%;padding:12px;background:#3498db;color:white;border:none;border-radius:6px;cursor:pointer;transition:0.3s;font-size:16px;}
button:hover{background:#2980b9;}
.success{background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:15px;}
.back-btn{display:inline-block;margin-top:20px;padding:10px 15px;background:#34495e;color:white;text-decoration:none;border-radius:6px;transition:0.3s;}
.back-btn:hover{background:#2c3e50;}
.student-info{background:#f9f9f9;padding:15px;border-radius:8px;margin-bottom:20px;}
</style>
</head>
<body>

<div class="container">
<h2>Manage Your Account</h2>
<?php if(isset($success)) echo '<div class="success">'.$success.'</div>'; ?>

<form method="POST">
<label>Username:</label>
<input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>

<label>Email:</label>
<input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

<label>Contact:</label>
<input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact'] ?? ''); ?>">

<label>New Password (leave blank to keep current):</label>
<input type="password" name="password">

<button name="update">Update Account</button>
</form>

<?php if($student): ?>
<div class="student-info">
<h3>Your Student Info</h3>
<p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>
<p><strong>Department:</strong> <?php echo htmlspecialchars($student['department']); ?></p>
<p><strong>Year Level:</strong> <?php echo htmlspecialchars($student['year_level']); ?></p>
<p><strong>Age:</strong> <?php echo htmlspecialchars($student['age']); ?></p>
</div>
<?php endif; ?>

<a href="dashboard.php" class="back-btn">Back to Dashboard</a>
</div>

</body>
</html>
