<?php
session_start();


define('USER_DATA_FILE', 'users.json');

$users = [];
if (file_exists(USER_DATA_FILE)) {
    $users = json_decode(file_get_contents(USER_DATA_FILE), true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username])) {
        $signup_error = "Username is already taken.";
    } else {
        $users[$username] = password_hash($password, PASSWORD_DEFAULT);
        file_put_contents(USER_DATA_FILE, json_encode($users));
        $signup_success = "Registration successful! You can now log in.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (isset($users[$username]) && password_verify($password, $users[$username])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: ' . $_SERVER['PHP_SELF']); 
        exit;
    } else {
        $login_error = "Invalid username or password.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration Form</title>
    <link rel="stylesheet" type="text/css" href="style.css"> 
</head>
<body style="background: url('Beezzz.jpg.webp'); 
    background-size: cover; 
       background-position: center; 
            font-family: Arial, sans-serif; 
                text-align: center; 
                    background-color: #f4f4f4; 
                        align-items: center;">

    <div style="background-color:#ebcb65; 
        border-radius: 15px; 
           padding: 30px; 
               width: 355px; 
                   margin: 50px auto; 
                        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); 
                            border: 5px solid #df944f;">

    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
        <div style="display: flex; flex-direction: column; align-items: center;">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>You are now logged in to our homepage.</p>
            <div style="margin-top: 10px;">
                <a href="homepage.php" style="text-decoration: none; background-color: #df944f; color: white; padding: 10px 15px; border-radius: 5px; margin-right: 10px;">Home Profile</a>
                <a href="?logout=true" style="text-decoration: none; background-color: #df944f; color: white; padding: 10px 15px; border-radius: 5px;">Logout</a>
            </div>
        </div>
    <?php else: ?>
        <h2 style="text-align: center;">Login Form</h2>

        <?php if (isset($login_error)): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>

        <h2 style="text-align: center;">Register Form</h2>

        <?php if (isset($signup_error)): ?>
            <p class="error"><?php echo $signup_error; ?></p>
        <?php endif; ?>

        <?php if (isset($signup_success)): ?>
            <p class="success"><?php echo $signup_success; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="register">
            <label for="register_username">Username:</label>
            <input type="text" id="register_username" name="username" required>
            <br>
            <label for="register_password">Password:</label>
            <input type="password" id="register_password" name="password" required>
            <br>
            <button type="submit">Register</button>
        </form>
    <?php endif; ?>
    </div>
</body>
</html>
