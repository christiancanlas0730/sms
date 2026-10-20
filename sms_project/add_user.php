<?php
session_start();
require "db.php";

if($_SESSION['role'] != 'admin'){
    die("Access denied.");
}

$successMessage="";
$errorMessage="";

if(isset($_POST['add'])){
    $username=$_POST['username'];
    $email=$_POST['email'];
    $contact=$_POST['contact'];
    $role=$_POST['role'];
    $password=password_hash($_POST['password'], PASSWORD_DEFAULT);

    try{
        $stmt=$conn->prepare("INSERT INTO users(username,email,contact,password,role) VALUES(?,?,?,?,?)");
        $stmt->execute([$username,$email,$contact,$password,$role]);
        $successMessage="User Added Successfully!";
    }catch(Exception $e){
        $errorMessage="Error adding user.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add User</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>

/* SAME DESIGN AS YOUR ADD STUDENT */
body{font-family:"Segoe UI",sans-serif;background:#f1f5f9;display:flex;justify-content:center;padding:40px 20px;}
.container{background:#fff;width:100%;max-width:520px;padding:35px;border-radius:16px;box-shadow:0 10px 35px rgba(0,0,0,0.06);}
h2{text-align:center;margin-bottom:25px;}
label{font-size:14px;font-weight:600;margin-bottom:6px;display:block;color:#475569;}
input,select{width:100%;padding:11px 14px;margin-bottom:18px;border-radius:10px;border:1px solid #cbd5e1;font-size:14px;background:#f8fafc;}
input:focus,select:focus{outline:none;border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.15);}
.button-group button,.button-group a{width:100%;padding:12px;border-radius:10px;font-size:14px;text-decoration:none;display:block;text-align:center;margin-top:10px;}
.button-group button{background:#0f172a;color:white;border:none;}
.button-group button:hover{background:#1e293b;}
.button-group a{background:#e2e8f0;color:#1e293b;}
.success{background:#ecfdf5;border:1px solid #10b981;padding:15px;border-radius:10px;color:#065f46;margin-bottom:20px;}
.error{background:#fef2f2;border:1px solid #ef4444;padding:15px;border-radius:10px;color:#7f1d1d;margin-bottom:20px;}
</style>
</head>
<body>

<div class="container">
<h2>Add User</h2>

<?php if($successMessage): ?><div class="success"><?= $successMessage ?></div><?php endif; ?>
<?php if($errorMessage): ?><div class="error"><?= $errorMessage ?></div><?php endif; ?>

<form method="POST">

<label>Username:</label>
<input type="text" name="username" required>

<label>Email:</label>
<input type="email" name="email" required>

<label>Contact:</label>
<input type="text" name="contact">

<label>Password:</label>
<input type="password" name="password" required>

<label>Role:</label>
<select name="role" required>
<option value="user">User</option>
<option value="admin">Admin</option>
</select>

<div class="button-group">
<button type="submit" name="add">Add User</button>
<a href="manage_users.php">Cancel</a>
</div>

</form>
</div>

</body>
</html>