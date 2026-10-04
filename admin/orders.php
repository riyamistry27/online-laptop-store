<?php
session_start();
include '../includes/db.php';

// Only allow admin access
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$error = '';
$success = '';

// Handle update order status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    $allowed_statuses = ['pending', 'processing', 'completed', 'cancel'];
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Order status updated successfully.";
        } else {
            $error = "Failed to update order status.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Invalid status selected.";
    }
}

// Fetch orders with user and product info
$query = "
SELECT o.*, u.name AS user_name, p.name AS product_name 
FROM orders o
JOIN user_form u ON o.user_id = u.id
JOIN products p ON o.product_id = p.id
ORDER BY o.created_at DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="/online-laptop-store/css/style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        select {
            padding: 5px;
        }
        .success { color: green; }
        .error { color: red; }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Manage Orders</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Ordered At</th>
                <th>Actions</th>
            </tr>
            <?php while ($order = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                    <td><?php echo $order['quantity']; ?></td>
                    <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                    <td><?php echo ucfirst($order['status']); ?></td>
                    <td><?php echo $order['created_at']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" required>
                                <?php
                                $statuses = ['pending', 'processing', 'completed', 'cancel'];
                                foreach ($statuses as $status) {
                                    $selected = ($order['status'] === $status) ? 'selected' : '';
                                    echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No orders found.</p>
    <?php endif; ?>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
