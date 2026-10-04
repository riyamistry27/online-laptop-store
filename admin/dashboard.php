<?php
session_start();
include '../includes/db.php';

// ✅ Redirect to login if not logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// ✅ Fetch admin username
$query = "SELECT username FROM admin WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $admin_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $admin_username);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/online-laptop-store/css/style.css">
    <link rel="stylesheet" href="/online-laptop-store/css/admin-dashboard.css"> <!-- Add this -->
</head>
<body>

<div class="admin-header">
    <h1>Welcome Admin</h1>
</div>

<div class="admin-content">
    <h2>Hello, <?php echo htmlspecialchars($admin_username); ?>!</h2>
    <p>This is your dashboard. You can manage users, products, and orders from here.</p>

    <ul class="admin-nav">
        <li><a href="../admin/users.php">Manage Users</a></li>
        <li><a href="../admin/products.php">Manage Products</a></li>
        <li><a href="../admin/orders.php">Manage Orders</a></li>
        <li><a href="../logout.php" class="logout-link">Logout</a></li>
    </ul>
</div>

</body>
</html>
