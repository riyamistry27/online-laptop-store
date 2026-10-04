<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
$is_admin_logged_in = isset($_SESSION['admin_id']);

// Check if normal user is logged in
$is_user_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tech Nest</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300;400;700&display=swap" rel="stylesheet" />

  <!-- Your CSS file -->
  <link rel="stylesheet" href="/online-laptop-store/css/style.css" />
</head>

<body>

<div class="navbar">
   <h2>Tech Nest</h2>
   <ul>
      <li><a class="nav-link" href="/online-laptop-store/index.php">Home</a></li>
      <li><a class="nav-link" href="/online-laptop-store/products.php">Products</a></li>

      <?php if ($is_admin_logged_in): ?>
         <!-- Admin logged in: show only logout and maybe admin dashboard -->
         <li><a class="nav-link" href="/online-laptop-store/admin/dashboard.php">Admin Dashboard</a></li>
         <li><a class="nav-link" href="/online-laptop-store/logout.php">Logout</a></li>

      <?php elseif ($is_user_logged_in): ?>
         <!-- Normal user logged in -->
         <li><a class="nav-link" href="/online-laptop-store/user_dashboard.php">Dashboard</a></li>
         <li><a class="nav-link" href="/online-laptop-store/cart.php">Cart</a></li>
         <li><a class="nav-link" href="/online-laptop-store/logout.php">Logout</a></li>

      <?php else: ?>
         <!-- Guest -->
         <li><a class="nav-link" href="/online-laptop-store/login.php">Login</a></li>
         <li><a class="nav-link" href="/online-laptop-store/register.php">Register</a></li>
      <?php endif; ?>
   </ul>
</div>
