<?php
session_start();
include 'includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_type = $_POST['login_type'];
    $email_or_username = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($login_type) || empty($email_or_username) || empty($password)) {
        $errors[] = 'All fields are required.';
    } else {
        if ($login_type === 'user') {
            // User login
            $query = "SELECT id, password FROM user_form WHERE email = ?";
            $stmt  = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email_or_username);
        } else {
            // Admin login
            $query = "SELECT id, password FROM admin WHERE username = ?";
            $stmt  = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $email_or_username);
        }

        if (!$stmt) {
            die('Prepare failed: ' . mysqli_error($conn));
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
            mysqli_stmt_fetch($stmt);

            if (md5($password) === $hashed_password) {
                if ($login_type === 'user') {
                    $_SESSION['user_id'] = $user_id;
                    header("Location: index.php");
                } else {
                    $_SESSION['admin_id'] = $user_id;
                    header("Location: admin/dashboard.php");

                }
                exit;
            } else {
                $errors[] = 'Incorrect password.';
            }
        } else {
            $errors[] = ucfirst($login_type) . ' not found.';
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!-- HTML Part -->
<?php include 'includes/header.php'; ?>

<div class="container" style="max-width: 400px; margin: auto; padding: 20px;">
   <h2>Login</h2>

   <?php if (!empty($errors)): ?>
      <div style="color: red;">
         <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
         <?php endforeach; ?>
      </div>
   <?php endif; ?>

   <form action="login.php" method="post">
      <label>Login As:</label>
      <select name="login_type" required>
         <option value="">-- Select --</option>
         <option value="user">User</option>
         <option value="admin">Admin</option>
      </select><br><br>

      <label>Email / Username:</label>
      <input type="text" name="email" required><br><br>

      <label>Password:</label>
      <input type="password" name="password" required><br><br>

      <button type="submit">Login</button>
   </form>  

   <p>Don't have a user account? <a href="register.php">Register here</a>.</p>
</div>

<?php include 'includes/footer.php'; ?>
