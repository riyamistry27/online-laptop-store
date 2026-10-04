<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit;
}

$user_id = $_SESSION['user_id'];

// Get cart items for the user
$cart_query = mysqli_query($conn, "
   SELECT cart.product_id, cart.quantity, products.price
   FROM cart
   JOIN products ON cart.product_id = products.id
   WHERE cart.user_id = $user_id
") or die('Cart query failed');

$cart_items = [];
$total = 0;

while ($row = mysqli_fetch_assoc($cart_query)) {
   $cart_items[] = $row;
   $total += $row['price'] * $row['quantity'];
}

// If cart is empty
if (empty($cart_items)) {
   echo "<div class='container'><p>Your cart is empty. <a href='index.php'>Shop now</a>.</p></div>";
   include 'includes/footer.php';
   exit;
}

// Place order on form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
   foreach ($cart_items as $item) {
      $product_id = $item['product_id'];
      $quantity = $item['quantity'];
      $price = $item['price'];
      $total_price = $price * $quantity;

      $insert_order = mysqli_prepare($conn, "
         INSERT INTO orders (user_id, product_id, quantity, total_price)
         VALUES (?, ?, ?, ?)
      ");
      mysqli_stmt_bind_param($insert_order, "iiid", $user_id, $product_id, $quantity, $total_price);
      mysqli_stmt_execute($insert_order);
      mysqli_stmt_close($insert_order);
   }

   // Clear the cart
   mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id") or die('Failed to clear cart');

   // Redirect
   header("Location: user_dashboard.php?order=success");
   exit;
}
?>

<div class="container">
   <h1 class="heading">Checkout</h1>

   <p>Total Amount: <strong>₹<?php echo number_format($total, 2); ?></strong></p>

   <form method="post">
      <button type="submit" name="place_order" class="btn">Place Order</button>
   </form>
</div>

<?php include 'includes/footer.php'; ?>
