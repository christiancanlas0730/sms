<?php
session_start();
require "db.php";

$error = "";

if(isset($_POST['login'])){

    // OFFLINE CAPTCHA CHECK
    if(empty($_POST['captcha'])){
        $error = "Please answer CAPTCHA.";
    }
    elseif($_POST['captcha'] != $_SESSION['captcha_answer']){
        $error = "Incorrect CAPTCHA.";
    }
    else {

        $email = $_POST['email'];
        $password = $_POST['password'];
        $login_role = $_POST['login_role'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){

            if($user['role'] != $login_role){
                $error = "Invalid role selected.";
            } else {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == 'admin'){
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            }

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

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

.login-container {
    width: 360px;
    padding: 40px;
    border-radius: 18px;
    background: #1f2933;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    text-align: center;
    color: #f1f5f9;
}

.login-container h2 {
    margin-bottom: 25px;
    font-weight: 600;
    color: #e2e8f0;
    letter-spacing: 0.5px;
}

.login-container input {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border-radius: 8px;
    border: 1px solid #2f3e4d;
    font-size: 14px;
    background: #273444;
    color: #e2e8f0;
    transition: all 0.2s ease;
}

.login-container input::placeholder {
    color: #94a3b8;
}

.login-container input:focus {
    border-color: #4f9da6;
    outline: none;
    background: #2c3e50;
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

/* Role Selection */
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

/* CAPTCHA */
.captcha-box {
    background: #273444;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 16px;
    color: #e2e8f0;
}

/* Register */
.register-link {
    display: block;
    margin-top: 15px;
    color: #4f9da6;
    text-decoration: none;
    font-size: 14px;
}

.register-link:hover {
    text-decoration: underline;
}

/* Error */
.error-msg {
    background: rgba(255, 99, 99, 0.15);
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #ff6b6b;
}
/* Forgot password styling */
.forgot-wrapper {
    text-align: right;
    margin-top: -5px;
    margin-bottom: 15px;
}

.forgot-link {
    font-size: 13px;
    color: #a78bfa;
    text-decoration: none;
    transition: 0.2s ease;
}

.forgot-link:hover {
    color: #c4b5fd;
    text-decoration: underline;
}

/* Light mode support */
body.light .forgot-link {
    color: #7c3aed;
}
</style>

</head>
<body>

<div class="login-container">

<h2>Login</h2>

<form method="POST">

<?php if(!empty($error)): ?>
    <div class="error-msg"><?php echo $error; ?></div>
<?php endif; ?>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="password" placeholder="Password" required>

<div class="forgot-wrapper">
    <a href="forgot_password.php" class="forgot-link">
        Forgot Password?
    </a>
</div>

<div class="role-selection">
    <label>
        <input type="radio" name="login_role" value="admin" required>
        <span>Admin</span>
    </label>

    <label>
        <input type="radio" name="login_role" value="user">
        <span>User</span>
    </label>
</div>

<div class="captcha-box">
<?php include "captcha.php"; ?>
</div>

<input type="number" name="captcha" placeholder="Enter CAPTCHA answer" required>

<button type="submit" name="login">Login</button>

</form>

<a href="register.php" class="register-link">
Don't have an account? Register
</a>

</div>

</body>
</html>