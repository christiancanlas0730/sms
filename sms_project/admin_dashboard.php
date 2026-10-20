<?php
session_start();
require "db.php";

// protect admin page
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit();
}

// count students
$stmt = $conn->query("SELECT COUNT(*) as total FROM students");
$totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// count users
$stmt2 = $conn->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];

// get latest students
$stmt3 = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");
$latestStudents = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* BODY */
body {
    font-family: "Segoe UI", sans-serif;
    background: #f1f5f9;
    display: flex;
    color: #1e293b;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    height: 100vh;
    position: fixed;
    background: #0f172a;
    color: #cbd5e1;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 22px;
    font-size: 18px;
    font-weight: 600;
    text-align: center;
    border-bottom: 1px solid #1e293b;
    letter-spacing: 0.5px;
}

.sidebar a {
    padding: 14px 22px;
    text-decoration: none;
    color: #94a3b8;
    transition: 0.2s ease;
    font-size: 14px;
}

.sidebar a:hover {
    background: #1e293b;
    color: #ffffff;
    padding-left: 28px;
}

/* MAIN */
.main {
    margin-left: 240px;
    width: 100%;
    padding: 30px;
}

/* HEADER */
.header {
    background: #ffffff;
    padding: 18px 25px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.welcome {
    font-size: 18px;
    font-weight: 600;
}

.logout-btn {
    background: #ef4444;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.2s ease;
}

.logout-btn:hover {
    background: #dc2626;
}

/* CARDS */
.card-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 35px;
}

.card {
    flex: 1;
    min-width: 240px;
    background: #ffffff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.05);
    transition: 0.2s ease;
}

.card:hover {
    transform: translateY(-4px);
}

.card-title {
    font-size: 14px;
    color: #64748b;
    margin-bottom: 8px;
}

.card-value {
    font-size: 32px;
    font-weight: 700;
    color: #0f172a;
}

/* TABLE BOX */
.table-box {
    background: #ffffff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.05);
}

.table-box h3 {
    margin-bottom: 18px;
    font-weight: 600;
    color: #0f172a;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 14px;
    text-align: left;
    font-size: 14px;
}

th {
    background: #0f172a;
    color: white;
    font-weight: 500;
}

td {
    border-bottom: 1px solid #e2e8f0;
}

tr:hover {
    background: #f8fafc;
}

/* SCROLL FIX FOR SMALL TABLES */
.table-box {
    overflow-x: auto;
}

/* RESPONSIVE */
@media (max-width: 768px) {

    .sidebar {
        width: 200px;
    }

    .main {
        margin-left: 200px;
        padding: 20px;
    }

    .card-container {
        flex-direction: column;
    }

    .header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
}
.sidebar a.active {
    background: #334155;
    border-left: 3px solid #a78bfa;
    color: white;
}
</style>
</head>
<body>

<div class="sidebar">

    <div class="sidebar-header">Admin Panel</div>

    <a href="admin_dashboard.php" class="active">Dashboard</a>
    <a href="add_student.php">Add Student</a>
    <a href="students.php">View Students</a>
    <a href="admin_users.php">Manage Users</a>
    <a href="logout.php">Logout</a>

</div>

<div class="main">

    <div class="header">
        <div class="welcome">Welcome, <?php echo $username; ?></div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>


    <div class="card-container">

        <div class="card">
            <div class="card-title">Total Students</div>
            <div class="card-value"><?php echo $totalStudents; ?></div>
        </div>

        <div class="card">
            <div class="card-title">Total Users</div>
            <div class="card-value"><?php echo $totalUsers; ?></div>
        </div>

    </div>


    


    <div class="table-box">

        <h3>Recent Students</h3>

        <table>

            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Course</th>
            </tr>

            <?php foreach($latestStudents as $student): ?>

            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['name']); ?></td>
                <td><?php echo htmlspecialchars($student['course']); ?></td>
            </tr>

            <?php endforeach; ?>

        </table>

    </div>


</div>

</body>
</html>