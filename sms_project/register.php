<?php
session_start();
require "db.php";

$error = "";
$success = "";
$username_error = false;

$username = "";
$email = "";

if(isset($_POST['register'])){

    // OFFLINE CAPTCHA CHECK
    if(empty($_POST['captcha'])){
        $error = "Please complete the CAPTCHA verification.";
    }
    elseif($_POST['captcha'] != $_SESSION['captcha_answer']){
        $error = "Incorrect CAPTCHA. Please try again.";
    }
    else {

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $raw_password = $_POST['password'];
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

        $role = 'user';

        if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
            $role = $_POST['role'] ?? 'user';
        }

        // CHECK USERNAME
        $checkUsername = $conn->prepare("SELECT id FROM users WHERE username=?");
        $checkUsername->execute([$username]);

        if($checkUsername->rowCount() > 0){

            $error = "This username is already in use. Please choose a different one.";
            $username_error = true;

        } else {

            // CHECK EMAIL
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email=?");
            $checkEmail->execute([$email]);

            if($checkEmail->rowCount() > 0){

                $error = "An account with this email address already exists.";

            } else {

                $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$username, $email, $password, $role]);

                $success = "Registration completed successfully.";

                // Clear values after success
                $username = "";
                $email = "";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: "Segoe UI", Arial, sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #141e30, #243b55);
}

.register-container {
    width: 360px;
    padding: 40px;
    border-radius: 18px;
    background: #1f2933;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    text-align: center;
    color: #f1f5f9;
}

.register-container h2 {
    margin-bottom: 25px;
    font-weight: 600;
    color: #e2e8f0;
}

.register-container input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #2f3e4d;
    font-size: 14px;
    background: #273444;
    color: #e2e8f0;
    transition: 0.2s ease;
}

.register-container input::placeholder {
    color: #94a3b8;
}

.register-container input:focus {
    border-color: #4f9da6;
    outline: none;
    background: #2c3e50;
}

/* Red highlight for duplicate username */
input.error-field {
    border-color: #ff6b6b !important;
    background: rgba(255, 99, 99, 0.08);
}

button {
    width: 100%;
    padding: 12px;
    border-radius: 25px;
    border: none;
    background: #6366f1;
    color: white;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: 0.2s ease;
}

button:hover {
    background: #4f46e5;
}

.captcha-box {
    background: #273444;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 16px;
    color: #e2e8f0;
}

.login-link {
    display: block;
    margin-top: 15px;
    color: #4f9da6;
    text-decoration: none;
    font-size: 14px;
}

.login-link:hover {
    text-decoration: underline;
}

.role-selection {
    display: flex;
    justify-content: space-between;
    margin-bottom: 18px;
}

.role-selection label {
    flex: 1;
    margin: 0 5px;
    cursor: pointer;
}

.role-selection input {
    display: none;
}

.role-selection span {
    display: block;
    padding: 10px;
    border-radius: 8px;
    background: #273444;
    border: 1px solid #2f3e4d;
    color: #e2e8f0;
    text-align: center;
    transition: 0.2s ease;
}

.role-selection input:checked + span {
    background: #4f9da6;
    border-color: #4f9da6;
    color: white;
}

.error-msg {
    background: rgba(255, 99, 99, 0.15);
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #ff6b6b;
}

.success-msg {
    background: rgba(72, 187, 120, 0.15);
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #48bb78;
}
</style>
</head>
<body>

<div class="register-container">
<h2>Register</h2>

<form method="POST">

<?php if(!empty($error)): ?>
    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if(!empty($success)): ?>
    <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<input 
    type="text" 
    name="username" 
    placeholder="Username" 
    value="<?php echo htmlspecialchars($username); ?>" 
    class="<?php echo $username_error ? 'error-field' : ''; ?>" 
    required>

<input 
    type="email" 
    name="email" 
    placeholder="Email" 
    value="<?php echo htmlspecialchars($email); ?>" 
    required>

<input type="password" name="password" placeholder="Password" required>

<?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
<div class="role-selection">
    <label>
        <input type="radio" name="role" value="user" checked>
        <span>User</span>
    </label>
    <label>
        <input type="radio" name="role" value="admin">
        <span>Admin</span>
    </label>
</div>
<?php endif; ?>

<div class="captcha-box">
<?php include "captcha.php"; ?>
</div>

<input type="number" name="captcha" placeholder="Enter CAPTCHA answer" required>

<button type="submit" name="register">Register</button>

</form>

<a href="login.php" class="login-link">
Already have an account? Login
</a>

</div>
</body>
</html>