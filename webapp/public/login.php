<?php

session_start();
include './components/loggly-logger.php'; // include this line

$MAX_ATTEMPTS = 10;
$LOCKOUT_SECONDS = 5 * 60;

// Initialize session tracking
if (!isset($_SESSION['failed_login_count'])) {
    $_SESSION['failed_login_count'] = 0;
}
if (!isset($_SESSION['lockout_until'])) {
    $_SESSION['lockout_until'] = 0;
}

// If POST and currently locked out, block further processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $now = time();
    if ($_SESSION['lockout_until'] > $now) {
        $remaining = $_SESSION['lockout_until'] - $now;
        $attemptedUser = $_POST['username'] ?? '(missing)';

        $logger->warning("Login blocked (lockout active). username={$attemptedUser}, remaining_seconds={$remaining}");

        // This will be shown in the HTML below
        $error_message = "Too many failed login attempts. Try again in " . ceil($remaining / 60) . " minute(s).";
    } else {
        // Lockout expired; clear it
        $_SESSION['lockout_until'] = 0;
    }
}

$hostname = 'backend-mysql-database';
$username = 'user';
$password = 'supersecretpw';
$database = 'password_manager';

$conn = new mysqli($hostname, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

unset($error_message);

if ($conn->connect_error) {
    $errorMessage = "Connection failed: " . $conn->connect_error;    
    die($errorMessage);
}

// Check if the form is submitted (ONLY if not locked out)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // OLD
    // $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND approved = 1";
    // $result = $conn->query($sql);

    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? AND approved = 1");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result && $result->num_rows > 0) {
       
        $userFromDB = $result->fetch_assoc();

        // Reset lockout counters on success
        $_SESSION['failed_login_count'] = 0;
        $_SESSION['lockout_until'] = 0;

        $_SESSION['authenticated'] = $username;
        $_SESSION['last_activity'] = time(); // Add this line here

        $logger->info("Login success for username: $username");

        if ($userFromDB['default_role_id'] == 1)
        {        
            $_SESSION['isSiteAdministrator'] = 1; // used to be setcookie('isSiteAdministrator', true, time() + 3600, '/');
        } else {
            /*
             *unset($_COOKIE['isSiteAdministrator']); 
             *setcookie('isSiteAdministrator', '', -1, '/');
             */
            unset($_SESSION['isSiteAdministrator']); // replace the top two lines with this line
        }

        header("Location: index.php");
        exit();
    } else {
        $error_message = 'Invalid username or password.';

        // Increment failed count and lock out if threshold reached
        $_SESSION['failed_login_count']++;

        $logger->warning("Login failed for username: $username. failed_count={$_SESSION['failed_login_count']}");

        if ($_SESSION['failed_login_count'] >= $MAX_ATTEMPTS) {
            $_SESSION['lockout_until'] = time() + $LOCKOUT_SECONDS;
            $logger->warning("User locked out after {$MAX_ATTEMPTS} failed attempts. username={$username}");
            $error_message = "Too many failed login attempts. You are locked out for 5 minutes.";
        }
    }

    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Login Page</title>
</head>
<body>
    <div class="container mt-5">
        <div class="col-md-6 offset-md-3">
            <h2 class="text-center">Login</h2>

            <!-- Message for when the user is automatically logged out -->
            <?php if (isset($_GET['timeout'])) : ?>
                <div class="alert alert-warning" role="alert">
                    You were logged out after 30 seconds of inactivity.
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" id="username" name="username" required
                        <?php if (isset($_SESSION['lockout_until']) && $_SESSION['lockout_until'] > time()) echo 'disabled'; ?>>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required
                        <?php if (isset($_SESSION['lockout_until']) && $_SESSION['lockout_until'] > time()) echo 'disabled'; ?>>
                </div>
                <button type="submit" class="btn btn-primary btn-block"
                    <?php if (isset($_SESSION['lockout_until']) && $_SESSION['lockout_until'] > time()) echo 'disabled'; ?>>
                    Login
                </button>
            </form>

            <div class="mt-3 text-center">
                <a href="./users/request_account.php" class="btn btn-secondary btn-block">Request an Account</a>
            </div>

            <?php
                // Optional: show remaining time when locked out
                if (isset($_SESSION['lockout_until']) && $_SESSION['lockout_until'] > time()) {
                    $remaining = $_SESSION['lockout_until'] - time();
                    echo '<div class="mt-3 alert alert-warning" role="alert">';
                    echo 'Locked out. Please try again in about ' . ceil($remaining / 60) . ' minute(s).';
                    echo '</div>';
                }
            ?>

        </div>
    </div>
</body>
</html>
