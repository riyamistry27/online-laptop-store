<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
   echo "<p style='text-align:center;'>Please <a href='login.php'>login</a> to view your cart.</p>";
   include 'includes/footer.php';
   exit;
}

$user_id = $_SESSION['user_id'];

// ===== ADD TO CART =====
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['product_quantity']));

    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = $user_id AND product_id = $product_id");

    if (mysqli_num_rows($check_cart) > 0) {
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + $quantity WHERE user_id = $user_id AND product_id = $product_id");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)");
    }

    header("Location: cart.php");
    exit;
}

// ===== UPDATE QUANTITY =====
if (isset($_POST['update_quantity'])) {
   $cart_id = intval($_POST['cart_id']);
   $new_qty = max(1, intval($_POST['quantity']));
   mysqli_query($conn, "UPDATE cart SET quantity = $new_qty WHERE id = $cart_id AND user_id = $user_id") or die('Update failed');
   header("Location: cart.php");
   exit;
}

// ===== REMOVE ITEM =====
if (isset($_POST['remove_item'])) {
   $cart_id = intval($_POST['cart_id']);
   mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id") or die('Remove failed');
   header("Location: cart.php");
   exit;
}

// ===== FETCH CART ITEMS =====
$cart_query = mysqli_query($conn, "
   SELECT cart.id AS cart_id, cart.quantity, products.*
   FROM cart
   JOIN products ON cart.product_id = products.id
   WHERE cart.user_id = $user_id
") or die('Query failed');

$total = 0;
?>

<div class="container">
   <h1 class="heading">Your Cart</h1>

   <?php if (mysqli_num_rows($cart_query) > 0): ?>
      <div class="products">

         <?php while ($item = mysqli_fetch_assoc($cart_query)): ?>
            <?php
               $sub_total = $item['price'] * $item['quantity'];
               $total += $sub_total;
            ?>
            <div class="cart-item">
               <form method="post">
                  <img class="product-image" src="/online-laptop-store/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                  
                  <div class="name"><?php echo htmlspecialchars($item['name']); ?></div>
                  <div class="price">₹<?php echo number_format($item['price'], 2); ?></div>

                  <label for="qty-<?php echo $item['cart_id']; ?>">Qty:</label>
                  <input type="number" id="qty-<?php echo $item['cart_id']; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">

                  <div class="subtotal">Subtotal: ₹<?php echo number_format($sub_total, 2); ?></div>

                  <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">

                  <div class="cart-buttons">
                     <button type="submit" name="update_quantity" class="btn btn-primary">Update</button>
                     <button type="submit" name="remove_item" class="btn btn-remove">Remove</button>
                  </div>
               </form>
            </div>
         <?php endwhile; ?>

         <div style="text-align: right; margin-top: 30px;">
            <h3>Total: ₹<?php echo number_format($total, 2); ?></h3>
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
         </div>
      </div>

   <?php else: ?>
      <p style="text-align: center;">Your cart is empty.</p>
   <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
