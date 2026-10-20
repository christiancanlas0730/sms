<?php
session_start();
require "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

/* =========================
   ADD USER
========================= */
if(isset($_POST['add_user'])){
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->execute([$email]);

    if($check->rowCount() > 0){
        $error = "An account with this email address already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
        $stmt->execute([$username,$email,$password,$role]);
        $success = "User account created successfully.";
    }
}

/* =========================
   UPDATE USER INFO
========================= */
if(isset($_POST['update_user'])){
    $id       = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];

    if($id == $_SESSION['user_id']){
        $error = "You cannot modify your own account here.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
        $stmt->execute([$username,$email,$role,$id]);
        $success = "User information updated successfully.";
    }
}

/* =========================
   DELETE USER
========================= */
if(isset($_GET['delete'])){
    $del_id = $_GET['delete'];

    if($del_id == $_SESSION['user_id']){
        $error = "You cannot delete your own account.";
    } else {
        $stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
        $stmt->execute([$del_id]);
        $target = $stmt->fetch(PDO::FETCH_ASSOC);

        if($target && $target['role'] === 'admin'){
            $error = "You cannot delete another administrator.";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$del_id]);
            $success = "User deleted successfully.";
        }
    }
}

/* =========================
   FETCH SINGLE USER
========================= */
$viewUser = null;
$editUser = null;

if(isset($_GET['view'])){
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_GET['view']]);
    $viewUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_GET['edit'])){
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* =========================
   FETCH USERS
========================= */
$stmt = $conn->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{box-sizing:border-box;}

body{
    font-family:"Segoe UI", sans-serif;
    background:#f1f5f9;
    margin:0;
    padding:30px;
    color:#1e293b;
}

.container{
    max-width:1100px;
    margin:auto;
}

h2{
    margin-bottom:10px;
}

.back-btn{
    display:inline-block;
    padding:10px 16px;
    background:#0f172a;
    color:white;
    text-decoration:none;
    border-radius:8px;
    margin-bottom:20px;
}

.add-btn{
    background:#16a34a;
}

.message{
    padding:12px 15px;
    border-radius:8px;
    margin-bottom:20px;
    font-weight:500;
}

.error{
    background:#fef2f2;
    color:#7f1d1d;
    border:1px solid #ef4444;
}

.success{
    background:#ecfdf5;
    color:#065f46;
    border:1px solid #10b981;
}

.form-box{
    background:white;
    padding:25px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 6px 25px rgba(0,0,0,0.06);
}

input, select{
    width:100%;
    padding:10px;
    margin:8px 0 15px;
    border:1px solid #cbd5e1;
    border-radius:8px;
}

button{
    padding:10px 15px;
    border:none;
    border-radius:8px;
    background:#3b82f6;
    color:white;
    cursor:pointer;
}

button:hover{
    background:#2563eb;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 6px 25px rgba(0,0,0,0.06);
}

th{
    background:#0f172a;
    color:white;
    padding:14px;
    text-align:left;
}

td{
    padding:14px;
    border-bottom:1px solid #e2e8f0;
}

tr:hover{
    background:#f8fafc;
}

.action-btn{
    padding:6px 10px;
    border-radius:6px;
    text-decoration:none;
    font-size:13px;
    color:white;
    margin-right:5px;
}

.view{background:#10b981;}
.edit{background:#6366f1;}
.delete{background:#ef4444;}

@media(max-width:768px){
    body{padding:15px;}
    table{font-size:13px;}
}
</style>
</head>

<body>
<div class="container">

<h2>Admin – Manage Users</h2>
<a href="admin_dashboard.php" class="back-btn">Back to Dashboard</a>
<a href="?add=true" class="back-btn add-btn">Add User</a>

<?php if($error)   echo '<div class="message error">'.$error.'</div>'; ?>
<?php if($success) echo '<div class="message success">'.$success.'</div>'; ?>

<!-- ADD USER -->
<?php if(isset($_GET['add'])): ?>
<div class="form-box">
<h3>Add User</h3>
<form method="POST">
<input type="text" name="username" placeholder="Username" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>

<select name="role">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>

<button name="add_user">Create User</button>
</form>
</div>
<?php endif; ?>

<!-- EDIT USER -->
<?php if($editUser): ?>
<div class="form-box">
<h3>Edit User</h3>
<form method="POST">
<input type="hidden" name="user_id" value="<?= $editUser['id'] ?>">
<input type="text" name="username" value="<?= htmlspecialchars($editUser['username']) ?>" required>
<input type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required>
<?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $editUser['id']): ?>
    <input type="password" name="password" placeholder="Enter new password">
<?php endif; ?>

<select name="role">
<option value="user"  <?= $editUser['role']=='user'?'selected':'' ?>>User</option>
<option value="admin" <?= $editUser['role']=='admin'?'selected':'' ?>>Admin</option>
</select>

<button name="update_user">Update </button>
</form>
</div>
<?php endif; ?>

<!-- VIEW USER -->
<?php if($viewUser): ?>
<div class="form-box">
<h3>User Details</h3>
<p><strong>ID:</strong> <?= $viewUser['id'] ?></p>
<p><strong>Username:</strong> <?= htmlspecialchars($viewUser['username']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($viewUser['email']) ?></p>
<p><strong>Role:</strong> <?= htmlspecialchars($viewUser['role']) ?></p>
</div>
<?php endif; ?>

<table>
<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Action</th>
</tr>

<?php foreach($users as $u): ?>
<tr>
<td><?= htmlspecialchars($u['id']) ?></td>
<td><?= htmlspecialchars($u['username']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= htmlspecialchars($u['role']) ?></td>
<td>
<a href="?view=<?= $u['id'] ?>" class="action-btn view">View</a>
<a href="?edit=<?= $u['id'] ?>" class="action-btn edit">Edit</a>

<?php
if($u['id']==$_SESSION['user_id']){
    echo "(You)";
} elseif($u['role']=='admin'){
    echo "(Admin)";
} else {
    echo '<a href="?delete='.$u['id'].'" class="action-btn delete"
          onclick="return confirm(\'Delete this user?\')">Delete</a>';
}
?>
</td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>