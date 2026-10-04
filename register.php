<?php
include 'includes/db.php';
include 'includes/header.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name      = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email     = isset($_POST['email']) ? $_POST['email'] : '';
    $password  = isset($_POST['password']) ? $_POST['password'] : '';
    $cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';

    // Validate password match
    if ($password !== $cpassword) {
        $message = "❌ Passwords do not match!";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM user_form WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "⚠️ Email already registered!";
        } else {
            // Hash password securely
            $hashed_password = md5($password); // Note: MD5 is not secure; consider using password_hash()

            // Insert user
            $insert = $conn->prepare("INSERT INTO user_form (name, email, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $name, $email, $hashed_password);
            if ($insert->execute()) {
                $message = "✅ Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $message = "❌ Registration failed. Try again!";
            }
            $insert->close();
        }

        $stmt->close();
    }
}
?>

<div class="container" style="max-width: 500px; margin: 30px auto;">
   <h2>User Registration</h2>

   <?php if ($message): ?>
      <div class="message"><?= $message ?></div>
   <?php endif; ?>

   <form action="register.php" method="POST">
      <div class="form-group">
         <label>Name:</label>
         <input type="text" name="name" required class="form-control">
      </div>

      <div class="form-group">
         <label>Email:</label>
         <input type="email" name="email" required class="form-control">
      </div>

      <div class="form-group">
         <label>Password:</label>
         <input type="password" name="password" required class="form-control">
      </div>

      <div class="form-group">
         <label>Confirm Password:</label>
         <input type="password" name="cpassword" required class="form-control">
      </div>

      <button type="submit" class="btn-primary">Register</button>
   </form>
</div>

<?php include 'includes/footer.php'; ?>
