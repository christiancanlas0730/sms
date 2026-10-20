<?php
session_start();
require "db.php";

// Protect page
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';
$username = htmlspecialchars($_SESSION['username']);

// ================= ADMIN DATA =================
if($role === 'admin'){

    // Count total students
    $stmt = $conn->query("SELECT COUNT(*) as total FROM students");
    $totalStudents = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Count total users
    $stmt_users = $conn->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt_users->fetch(PDO::FETCH_ASSOC)['total'];

    // Recent students
    $stmt2 = $conn->query("SELECT * FROM students ORDER BY id DESC LIMIT 5");
    $recentStudents = $stmt2->fetchAll(PDO::FETCH_ASSOC);

} else {

    // ================= NORMAL USER DATA =================

    $stmt3 = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt3->execute([$user_id]);
    $studentInfo = $stmt3->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(120deg, #0f172a, #1e293b);
    color: #e2e8f0;
}

/* HEADER */
.header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: rgba(15, 23, 42, 0.85);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    padding: 0 25px;
    border-bottom: 1px solid #334155;
    font-weight: 500;
}

/* SIDEBAR */
.sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    width: 220px;
    height: calc(100vh - 60px);
    background: rgba(30, 41, 59, 0.9);
    backdrop-filter: blur(10px);
    padding-top: 20px;
    border-right: 1px solid #334155;
}

.sidebar h3 {
    padding: 15px 20px;
    color: #a78bfa;
    font-size: 18px;
    letter-spacing: 1px;
}

.sidebar a {
    display: block;
    padding: 12px 20px;
    color: #cbd5e1;
    text-decoration: none;
    transition: 0.2s ease;
}

.sidebar a:hover {
    background: #334155;
    color: #ffffff;
    border-left: 3px solid #a78bfa;
}

/* CONTENT */
.content {
    margin-left: 220px;
    margin-top: 60px;
    padding: 40px;
}

/* CARDS */
.card-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 30px;
}

.card {
    flex: 1;
    min-width: 250px;
    background: rgba(30, 41, 59, 0.8);
    padding: 25px;
    border-radius: 14px;
    border: 1px solid #334155;
    transition: 0.2s ease;
}

.card:hover {
    transform: translateY(-4px);
}

.card h3 {
    color: #94a3b8;
    margin-bottom: 10px;
    font-size: 15px;
}

.card h1 {
    font-size: 32px;
    color: #a78bfa;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(30, 41, 59, 0.8);
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid #334155;
}

th {
    background: #a78bfa;
    padding: 12px;
    text-align: left;
    color: white;
}

td {
    padding: 12px;
    border-bottom: 1px solid #334155;
}

tr:hover {
    background: #334155;
}

/* BUTTON */
.button {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 16px;
    background: #a78bfa;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.2s ease;
}

.button:hover {
    background: #8b5cf6;
}
.sidebar a.active {
    background: #334155;
    border-left: 3px solid #a78bfa;
    color: white;
}
</style>
</head>
<body>

<div class="header">
    
    Welcome, <?php echo $username; ?> (<?php echo htmlspecialchars($role); ?>)
</div>

<div class="sidebar">
    <h3 style="padding:10px 20px;">SMS</h3>
    <a href="dashboard.php" class="active">Dashboard</a>

    <?php if($role === 'admin'): ?>
        <a href="students.php">View Students</a>
        <a href="add_student.php">Add Student</a>
        <a href="admin_users.php">Manage Users</a>
    <?php else: ?>
        <a href="profile.php">My Profile</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</div>

<div class="content">

<h2>Dashboard</h2>

<?php if($role === 'admin'): ?>

    <div class="card-container">
        <div class="card">
            <h3>Total Students</h3>
            <h1><?php echo $totalStudents; ?></h1>
        </div>

        <div class="card">
            <h3>Total Users</h3>
            <h1><?php echo $totalUsers; ?></h1>
        </div>
    </div>
    

    <h3>Recent Students</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Year Level</th>
        </tr>
        <?php foreach($recentStudents as $student): ?>
        <tr>
            <td><?php echo htmlspecialchars($student['id']); ?></td>
            <td><?php echo htmlspecialchars($student['name']); ?></td>
            <td><?php echo htmlspecialchars($student['course']); ?></td>
            <td><?php echo htmlspecialchars($student['year_level']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

<?php else: ?>

    <?php if($studentInfo): ?>
        <div class="card">
            <h3>Your Student Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($studentInfo['name']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($studentInfo['course']); ?></p>
            <p><strong>Department:</strong> <?php echo htmlspecialchars($studentInfo['department']); ?></p>
            <p><strong>Year Level:</strong> <?php echo htmlspecialchars($studentInfo['year_level']); ?></p>
            <p><strong>Age:</strong> <?php echo htmlspecialchars($studentInfo['age']); ?></p>
            <a href="profile.php" class="button">Edit Profile</a>
        </div>
    <?php else: ?>
        <p>No student record found.</p>
    <?php endif; ?>

<?php endif; ?>

</div>
</body>
</html>