<?php
require "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
        $stmt->execute([$hashedPassword, $username]);

        if ($stmt->rowCount() > 0) {
            $message = "Password successfully updated!";
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(120deg, #0f172a, #1e293b);
    color: #e2e8f0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    transition: 0.3s ease;
}

/* Card container */
.form-box {
    background: rgba(30, 41, 59, 0.9);
    padding: 35px;
    border-radius: 16px;
    width: 350px;
    border: 1px solid #334155;
    backdrop-filter: blur(10px);
    animation: fadeIn 0.4s ease-in-out;
}

h3 {
    margin-bottom: 20px;
    font-weight: 500;
}

input {
    width: 100%;
    padding: 11px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #334155;
    background: #1e293b;
    color: white;
}

input:focus {
    outline: none;
    border-color: #a78bfa;
}

button {
    width: 100%;
    padding: 11px;
    border-radius: 8px;
    border: none;
    background: #a78bfa;
    color: white;
    cursor: pointer;
    transition: 0.2s ease;
}

button:hover {
    background: #8b5cf6;
}

.message {
    background: #16a34a;
    padding: 8px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

.error {
    background: #dc2626;
    padding: 8px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

a {
    display: inline-block;
    margin-top: 15px;
    color: #a78bfa;
    font-size: 14px;
    text-decoration: none;
}

/* LIGHT MODE SUPPORT */
body.light {
    background: linear-gradient(120deg, #f1f5f9, #e2e8f0);
    color: #1e293b;
}

body.light .form-box {
    background: white;
    border: 1px solid #cbd5e1;
}

body.light input {
    background: #f8fafc;
    color: #1e293b;
    border: 1px solid #cbd5e1;
}

body.light input:focus {
    border-color: #8b5cf6;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
</head>

<body>

<div class="form-box">
    <h3>Reset Password</h3>

    <?php if($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Enter Username" required>
        <input type="password" name="password" placeholder="New Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button type="submit">Reset Password</button>
    </form>

    <a href="login.php">Back to Login</a>
</div>

<script>
// Apply saved theme automatically
window.onload = function() {
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "light") {
        document.body.classList.add("light");
    }
}
</script>

</body>
</html>