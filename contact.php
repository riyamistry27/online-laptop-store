<?php
// Start session
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$name = $email = $message = "";
$name_err = $email_err = $message_err = $success_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if (empty($name)) {
        $name_err = "Name is required.";
    }

    if (empty($email)) {
        $email_err = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    }

    if (empty($message)) {
        $message_err = "Message is required.";
    }

    if (empty($name_err) && empty($email_err) && empty($message_err)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $success_msg = "Message sent successfully!";
            $name = $email = $message = ""; // clear form
        } else {
            $success_msg = "Failed to send message.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
</head>
<body>
    <h2>Contact Us</h2>

    <?php if ($success_msg): ?>
        <p style="color:green;"><?= $success_msg ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>"><br>
        <small style="color:red;"><?= $name_err ?></small><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"><br>
        <small style="color:red;"><?= $email_err ?></small><br>

        <label>Message:</label><br>
        <textarea name="message"><?= htmlspecialchars($message) ?></textarea><br>
        <small style="color:red;"><?= $message_err ?></small><br>

        <button type="submit">Send</button>
    </form>
</body>
</html>
