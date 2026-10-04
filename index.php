<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

// Fetch latest 4 products for preview on homepage
$select_products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC LIMIT 4") or die('Query failed');
?>

<div class="hero">
  <h1>Welcome to Laptop Store</h1>
  <p>Find your perfect laptop with unbeatable deals and the latest models</p>
  <a href="products.php" class="btn-primary">Shop Now</a>
</div>

<div class="products-section">
  <h2 style="text-align:center; margin-bottom: 30px;">Featured Laptops</h2>
  <div class="box-container">
    <?php if (mysqli_num_rows($select_products) > 0): ?>
      <?php while ($product = mysqli_fetch_assoc($select_products)): ?>
        <div class="box">
  <img src="/online-laptop-store/images/<?php echo htmlspecialchars($product['image']); ?>" 
       alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
  <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
  <div class="price">₹<?php echo number_format($product['price'], 2); ?></div>
  <a href="products.php?id=<?php echo $product['id']; ?>" class="btn-primary">View Details</a>
</div>

      <?php endwhile; ?>
    <?php else: ?>
      <p class="message info">No laptops available at the moment.</p>
    <?php endif; ?>
  </div>
</div>

<a href="products.php" class="btn-primary" style="display:block; width:180px; margin: 30px auto 60px; text-align:center; border-radius:6px;">
  Explore More Laptops
</a>

<?php include 'includes/footer.php'; ?>
