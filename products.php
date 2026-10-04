<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

$select_products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC") or die('Query failed');
?>

<div class="container">
   <h1 class="heading">All Products</h1>

  <div class="products-section">
   <div class="box-container">

      <?php if (mysqli_num_rows($select_products) > 0): ?>
         <?php while ($product = mysqli_fetch_assoc($select_products)): ?>
            <form action="cart.php" method="post" class="box">
               
               <img src="/online-laptop-store/images/<?php echo htmlspecialchars($product['image']); ?>" 
                    alt="<?php echo htmlspecialchars($product['name']); ?>" 
                    class="product-image">

               <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
               <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
               
               <input type="number" name="product_quantity" value="1" min="1" class="form-control quantity-input">
               
               <!-- Hidden fields -->
               <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
               <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
               <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
               <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
               
               <input type="submit" name="add_to_cart" value="Add to Cart" class="btn-primary">
            </form>
         <?php endwhile; ?>
      <?php else: ?>
         <p class="message info">No products available.</p>
      <?php endif; ?>  

   </div>
</div>

   </div>
</div>

<?php include 'includes/footer.php'; ?>
