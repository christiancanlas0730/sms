<?php
session_start();
require "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// PAGINATION
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// FILTER BY COURSE (DROPDOWN)
$course_filter = "";
$where = "";

if(isset($_GET['course']) && $_GET['course'] != ""){
    $course_filter = $_GET['course'];
    $where = "WHERE course = :course";
}

// DEFINE COURSES MANUALLY
$courses = ['BSIT', 'BSCS', 'BSECE', 'BSED'];

// COUNT
$count_sql = "SELECT COUNT(*) FROM students $where";
$count_stmt = $conn->prepare($count_sql);

if($where){
    $count_stmt->bindValue(':course', $course_filter);
}

$count_stmt->execute();
$total_students = $count_stmt->fetchColumn();
$total_pages = ceil($total_students / $limit);

// FETCH
$sql = "SELECT * FROM students $where ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);

if($where){
    $stmt->bindValue(':course', $course_filter);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Students Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{font-family: Arial;background:#f4f6f9;margin:0;}
.header{background:#2c3e50;color:white;padding:15px 25px;display:flex;justify-content:space-between;align-items:center;}
.header-title{font-size:20px;font-weight:bold;}
.back-btn{background:#e74c3c;color:white;padding:8px 15px;text-decoration:none;border-radius:5px;}
.back-btn:hover{background:#c0392b;}
.container{width:95%;max-width:1200px;margin:25px auto;}
.card{background:white;padding:20px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;flex-wrap:wrap;}
.filter-box select{padding:10px;border:1px solid #ccc;border-radius:5px;font-size:14px;}
.add-btn{background:#27ae60;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;}
.add-btn:hover{background:#1e8449;}
.table-container{overflow-x:auto;}
table{width:100%;border-collapse:collapse;}
th, td{padding:12px;border-bottom:1px solid #ddd;}
th{background:#2c3e50;color:white;}
tr:hover{background:#f2f2f2;}
.action-btn{padding:6px 10px;border-radius:4px;text-decoration:none;color:white;font-size:13px;}
.view{background:#3498db;}.edit{background:#f39c12;}.delete{background:#e74c3c;}
.view:hover{background:#21618c;}.edit:hover{background:#ca6f1e;}.delete:hover{background:#922b21;}
.pagination{margin-top:20px;text-align:center;}
.pagination a{padding:8px 14px;margin:3px;border-radius:5px;text-decoration:none;border:1px solid #3498db;color:#3498db;}
.pagination a.active{background:#3498db;color:white;}
.pagination a:hover{background:#3498db;color:white;}
</style>

<script>
function filterCourse(select){
    const course = select.value;
    const url = new URL(window.location.href);
    if(course === ""){
        url.searchParams.delete("course");
    } else {
        url.searchParams.set("course", course);
    }
    url.searchParams.delete("page");
    window.location.href = url.toString();
}
</script>

</head>
<body>
<div class="header">
    <div class="header-title">Students Management</div>
    <a href="admin_dashboard.php" class="back-btn">Dashboard</a>
</div>
<div class="container">
<div class="card">
<div class="top-bar">
<div class="filter-box">
<select name="course" onchange="filterCourse(this)">
<option value="">All Courses</option>
<?php foreach($courses as $course): ?>
<option value="<?php echo htmlspecialchars($course); ?>" <?php if($course_filter == $course) echo 'selected'; ?>><?php echo htmlspecialchars($course); ?></option>
<?php endforeach; ?>
</select>
</div>
<a href="add_student.php" class="add-btn">Add Student</a>
</div>
<div class="table-container">
<table>
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Actions</th></tr>
<?php foreach($students as $student): ?>
<tr>
<td><?php echo htmlspecialchars($student['id']); ?></td>
<td><?php echo htmlspecialchars($student['name']); ?></td>
<td><?php echo htmlspecialchars($student['email']); ?></td>
<td><?php echo htmlspecialchars($student['course']); ?></td>
<td><?php echo htmlspecialchars($student['year_level']); ?></td>
<td>
<a class="action-btn view" href="view_student.php?id=<?php echo $student['id']; ?>">View</a>
<a class="action-btn edit" href="edit_student.php?id=<?php echo $student['id']; ?>">Edit</a>
<a class="action-btn delete" href="delete_student.php?id=<?php echo $student['id']; ?>" onclick="return confirm('Delete this student?')">Delete</a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
<div class="pagination">
<?php for($i=1; $i<=$total_pages; $i++): ?>
<a class="<?php if($i==$page) echo 'active'; ?>" href="?page=<?php echo $i; ?>&course=<?php echo urlencode($course_filter); ?>"><?php echo $i; ?></a>
<?php endfor; ?>
</div>
</div>
</div>
</body>
</html>