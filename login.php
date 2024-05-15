<?php
// Start the session
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the username and password
$valid_username = "admin";
$valid_password = "password";

// Check if the user is already logged in
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Perform user authentication
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        $_SESSION['login_success'] = true; // Set a session variable to indicate successful login
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
    <script>
        <?php if (isset($error)): ?>
        window.addEventListener('DOMContentLoaded', (event) => {
            alert("<?php echo $error; ?>");
        });
        <?php endif; ?>

        <?php if (isset($_SESSION['login_success'])): ?>
        window.addEventListener('DOMContentLoaded', (event) => {
            alert("Login successful. Welcome, <?php echo $_SESSION['username']; ?>!");
            <?php unset($_SESSION['login_success']); ?> // Unset the session variable after displaying the message
        });
        <?php endif; ?>
    </script>
</head>
<body>
<div class="backgrond">
    <div class="container">
      <h1>Login</h1>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="login-form">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required><br>
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
          <button type="submit">Login</button>
        </form>
      <p id="error-message"></p>
    </div>
</div>
<footer>
    <p class="text-footer">
        @ Copyright 2023 DG1-005 Priison Management System. All rights are reserved.
    </p>
</footer>
</body>
</html>
