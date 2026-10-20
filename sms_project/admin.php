<?php
session_start();
require "db.php";

if($_SESSION['role'] != 'admin'){
    die("Access denied.");
}

$sql = "SELECT * FROM users";
$stmt = $conn->query($sql);
?>

<h2>Admin Panel</h2>

Welcome Admin: <?php echo $_SESSION['username']; ?>

<table border="1">

<tr>
<th>ID</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
</tr>

<?php
while($row = $stmt->fetch()){
?>

<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['email']; ?></td>
<td><?php echo $row['role']; ?></td>
</tr>

<?php } ?>

</table>

<a href="logout.php">Logout</a>
