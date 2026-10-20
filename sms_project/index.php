<?php
session_start();

// If user is logged in
if(isset($_SESSION['user_id'])){

    // Redirect based on role
    if($_SESSION['role'] == 'admin'){
        header("Location: admin.php");
        exit();
    } else {
        header("Location: login.php");
        exit();
    }

}

// If not logged in, show homepage
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management System</title>

    <style>

       <style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:"Segoe UI", sans-serif;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(120deg,#0f172a,#1e293b);
    color:white;
    overflow:hidden;
}

/* Background Glow */
body::before{
    content:"";
    position:absolute;
    width:600px;
    height:600px;
    background:radial-gradient(circle, rgba(99,102,241,0.3), transparent 70%);
    top:-200px;
    right:-200px;
    z-index:0;
}

.container{
    position:relative;
    z-index:1;
    background:rgba(15,23,42,0.85);
    backdrop-filter:blur(12px);
    padding:50px 45px;
    border-radius:20px;
    width:380px;
    text-align:center;
    border:1px solid rgba(255,255,255,0.08);
    box-shadow:0 20px 60px rgba(0,0,0,0.4);
    animation:fadeIn 0.5s ease;
}

h1{
    font-size:26px;
    margin-bottom:10px;
    font-weight:600;
    letter-spacing:0.5px;
}

p{
    font-size:14px;
    color:#cbd5e1;
    margin-bottom:30px;
}

/* Buttons */
.btn{
    display:block;
    width:100%;
    padding:13px;
    margin-bottom:15px;
    border-radius:10px;
    font-size:15px;
    font-weight:500;
    text-decoration:none;
    transition:0.25s ease;
}

/* Login Button */
.login-btn{
    background:#6366f1;
    color:white;
}

.login-btn:hover{
    background:#4f46e5;
    transform:translateY(-2px);
}

/* Register Button */
.register-btn{
    background:transparent;
    border:1px solid #6366f1;
    color:#c7d2fe;
}

.register-btn:hover{
    background:#6366f1;
    color:white;
}

/* Footer */
.footer{
    margin-top:20px;
    font-size:12px;
    color:#94a3b8;
}

/* Animation */
@keyframes fadeIn{
    from{opacity:0; transform:translateY(20px);}
    to{opacity:1; transform:translateY(0);}
}

/* Responsive */
@media(max-width:480px){
    .container{
        width:90%;
        padding:40px 25px;
    }
}
</style>

</head>
<body>

<div class="container">

    <h1>User Management System</h1>

    <p>Please choose an option</p>

    <a href="login.php" class="btn login-btn">Login</a>

    <a href="register.php" class="btn register-btn">Register</a>

    <div class="footer">
        Brought to you by Zian & Zach Efron
    </div>

</div>

</body>
</html>
