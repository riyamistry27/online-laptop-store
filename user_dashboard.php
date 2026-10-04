<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = mysqli_prepare($conn, "SELECT name, email FROM user_form WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $name, $email);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
?>

<div class="container">
   <h1 class="heading">👋 Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
   <p>Your registered email: <strong><?php echo htmlspecialchars($email); ?></strong></p>

   <hr><h2>🛒 Your Cart</h2>

   <?php
   $cart_query = mysqli_query($conn, "
      SELECT cart.quantity, products.name, products.price, products.image
      FROM cart
      JOIN products ON cart.product_id = products.id
      WHERE cart.user_id = $user_id
   ") or die('Cart query failed');

   if (mysqli_num_rows($cart_query) > 0): ?>
      <table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:#fff;">
         <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price (₹)</th>
            <th>Qty</th>
            <th>Subtotal (₹)</th>
         </tr>
         <?php $cart_total = 0; ?>
         <?php while ($item = mysqli_fetch_assoc($cart_query)): ?>
            <tr>
               <td><img src="images/<?php echo $item['image']; ?>" width="80"></td>
               <td><?php echo htmlspecialchars($item['name']); ?></td>
               <td><?php echo number_format($item['price'], 2); ?></td>
               <td><?php echo $item['quantity']; ?></td>
               <td>
                  <?php 
                  $subtotal = $item['price'] * $item['quantity'];
                  $cart_total += $subtotal;
                  echo number_format($subtotal, 2);
                  ?>
               </td>
            </tr>
         <?php endwhile; ?>
         <tr>
            <td colspan="4" align="right"><strong>Total:</strong></td>
            <td><strong>₹<?php echo number_format($cart_total, 2); ?></strong></td>
         </tr>
      </table>
   <?php else: ?>
      <p>Your cart is empty.</p>
   <?php endif; ?>

   <hr><h2>📦 Your Orders</h2>

   <?php
   // Example order query, assuming an "orders" table exists
   $orders_query = mysqli_query($conn, "
      SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC
   ") or die('Orders query failed');

   if (mysqli_num_rows($orders_query) > 0): ?>
      <table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:#fff;">
         <tr>
            <th>Order ID</th>
            <th>Total Amount (₹)</th>
            <th>Status</th>
            <th>Date</th>
         </tr>
         <?php while ($order = mysqli_fetch_assoc($orders_query)): ?>
            <tr>
               <td><?php echo $order['id']; ?></td>
               <td>₹<?php echo number_format($order['total_price'], 2); ?></td>
               <td><?php echo htmlspecialchars($order['status']); ?></td>
               <td><?php echo $order['created_at']; ?></td>
            </tr>
         <?php endwhile; ?>
      </table>
   <?php else: ?>
      <p>No orders yet.</p>
   <?php endif; ?>

   <hr>
   <a href="logout.php" class="btn" style="background-color: crimson;">Logout</a>
</div>

<?php include 'includes/footer.php'; ?>
