<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$edit_id  = (int) $_GET['id'];
$admin_id = (int) $_SESSION['user_id'];
$errorMessage = "";

/* ========================
   FETCH USER BEING EDITED
======================== */
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$edit_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

/* ========================
   UPDATE LOGIC
======================== */
if (isset($_POST['update'])) {

    $email   = $_POST['email'];
    $contact = $_POST['contact'];
    $role    = $_POST['role'];

    try {

        $conn->beginTransaction();

        // Update basic info
        $update = $conn->prepare(
            "UPDATE users SET email = ?, contact = ?, role = ? WHERE id = ?"
        );
        $update->execute([$email, $contact, $role, $edit_id]);

        // Allow password change ONLY if admin edits themselves
        if ($edit_id === $admin_id) {

            $new_password     = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (!empty($new_password)) {

                if ($new_password !== $confirm_password) {
                    throw new Exception("Passwords do not match.");
                }

                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

                $updatePass = $conn->prepare(
                    "UPDATE users SET password = ? WHERE id = ?"
                );
                $updatePass->execute([$hashedPassword, $edit_id]);
            }
        }

        $conn->commit();

        header("Location: manage_users.php");
        exit();

    } catch (Exception $e) {
        $conn->rollBack();
        $errorMessage = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }
        .container {
            background: #fff;
            width: 100%;
            max-width: 520px;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.06);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #475569;
        }
        input, select {
            width: 100%;
            padding: 11px 14px;
            margin-bottom: 18px;
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background: #f8fafc;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .button-group button,
        .button-group a {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
        }
        .button-group button {
            background: #0f172a;
            color: white;
            border: none;
        }
        .button-group button:hover {
            background: #1e293b;
        }
        .button-group a {
            background: #e2e8f0;
            color: #1e293b;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #ef4444;
            padding: 15px;
            border-radius: 10px;
            color: #7f1d1d;
            margin-bottom: 20px;
        }
        .notice {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 10px;
            color: #475569;
            margin-bottom: 15px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>

    <?php if (!empty($errorMessage)): ?>
        <div class="error"><?= $errorMessage; ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Username:</label>
        <input type="text" value="<?= htmlspecialchars($user['username']) ?>" readonly>

        <label>Email:</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>Contact:</label>
        <input type="text" name="contact"
               value="<?= htmlspecialchars($user['contact']) ?>">

        <label>Role:</label>
        <select name="role">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>
                User
            </option>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                Admin
            </option>
        </select>

        <?php if ($edit_id === $admin_id): ?>
            <hr>
            <label>New Password</label>
            <input type="password" name="new_password"
                   placeholder="Enter new password">

            <label>Confirm New Password</label>
            <input type="password" name="confirm_password"
                   placeholder="Confirm new password">
        <?php else: ?>
            <div class="notice">
                Only users can change their own password.
            </div>
        <?php endif; ?>

        <div class="button-group">
            <button type="submit" name="update">Update User</button>
            <a href="manage_users.php">Cancel</a>
        </div>

    </form>
</div>

</body>
</html>