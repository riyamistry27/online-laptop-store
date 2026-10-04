<?php
session_start();
include '../includes/db.php';

// Only allow admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit;
}

$error = '';
$success = '';

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image_path = '';

    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $image_name;
        } else {
            $error = "Image upload failed.";
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($conn, "INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sds", $name, $price, $image_path);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = "Product added successfully.";
    }
}

// Handle Delete
if (isset($_POST['delete_product'])) {
    $delete_id = intval($_POST['product_id']);
    mysqli_query($conn, "DELETE FROM products WHERE id = $delete_id");
    $success = "Product deleted.";
}

// Handle Update
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $image_path = $_POST['existing_image']; // fallback to existing

    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES['image']['name']);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $image_name;
        } else {
            $error = "Image upload failed.";
        }
    }

    if (!$error) {
        $stmt = mysqli_prepare($conn, "UPDATE products SET name = ?, price = ?, image = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sdsi", $name, $price, $image_path, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = "Product updated.";
    }
}

// Fetch Products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="/online-laptop-store/css/style.css">
    <style>
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        img {
            width: 100px;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    <h2>Manage Products</h2>

    <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?php echo $success; ?></p><?php endif; ?>

    <h3>Add New Product</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="file" name="image" accept="image/*">
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <h3>All Products</h3>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Price</th><th>Image</th><th>Created At</th><th>Actions</th>
        </tr>
        <?php while ($product = mysqli_fetch_assoc($products)): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </td>
                <td>
                        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>
                </td>
                <td>
                    <?php if ($product['image']): ?>
                        <img src="../uploads/<?php echo $product['image']; ?>" alt="">
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                        <input type="file" name="image" accept="image/*">
                        <input type="hidden" name="existing_image" value="<?php echo $product['image']; ?>">
                </td>
                <td><?php echo $product['created_at']; ?></td>
                <td>
                        <button type="submit" name="update_product">Update</button>
                    </form>
                    <form method="post" style="margin-top:10px;" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="delete_product">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>
