<?php
session_start();

// Max allowed attempts
$max_attempts = 3;

// If attempts not set, initialize
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
}

// Dummy admin credentials (for demo, replace with DB check)
$admin_email = "admin@example.com";
$admin_password = "Admin123";

// Error message holder
$error = "";

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Stop if reached max attempts
    if ($_SESSION['attempts'] >= $max_attempts) {
        $error = "Account locked! Too many failed attempts.";
    } else {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
            $_SESSION['attempts']++;
        }
        // Password validation (at least 6 chars, one letter, one number)
        elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $password)) {
            $error = "Password must be at least 6 characters, with letters and numbers.";
            $_SESSION['attempts']++;
        }
        // Check credentials
        elseif ($email === $admin_email && $password === $admin_password) {
            $_SESSION['attempts'] = 0; // reset attempts
            $_SESSION['admin'] = $email;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Wrong email or password!";
            $_SESSION['attempts']++;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
</head>
<body>
    <h2>Admin Login</h2>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <?php if ($_SESSION['attempts'] < $max_attempts): ?>
    <form method="post" action="">
        <label>Email:</label><br>
        <input type="text" name="email" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <input type="submit" value="Login">
    </form>
    <?php else: ?>
        <p style="color:red;">You have exceeded maximum login attempts. Please try again later.</p>
    <?php endif; ?>

    <p>Attempts left: <?php echo $max_attempts - $_SESSION['attempts']; ?></p>
</body>
</html>