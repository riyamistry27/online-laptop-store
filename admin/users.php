<?php
session_start();
include '../includes/db.php';

// Only allow admin access
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

// Handle deletion
if (isset($_POST['delete_user'])) {
    $delete_id = intval($_POST['user_id']); // Ensure integer

    if ($delete_id !== $_SESSION['admin_id']) { // Prevent deleting self
        $stmt = mysqli_prepare($conn, "DELETE FROM user_form WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: users.php");
        exit;
    } else {
        $error = "You cannot delete yourself!";
    }
}

// Fetch users
$result = mysqli_query($conn, "SELECT id, name, email, created_at FROM user_form ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="/online-laptop-store/css/style.css">
    <style>
        /* Simplified internal styles */
        .container {
            width: 90%;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
        }
        .header h1 {
            text-align: center;
            margin: 30px 0;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="header"><h1>Manage Users</h1></div>
<div class="container">
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Registered On</th><th>Action</th></tr>
            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <?php if ($user['id'] !== $_SESSION['admin_id']): ?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to delete this user?');">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user">Delete</button>
                        </form>
                        <?php else: ?>
                        <em>(You)</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
