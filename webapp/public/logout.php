<?php

session_start();

include './components/loggly-logger.php';

$wasTimeout = isset($_GET['timeout']);

if ($wasTimeout) {
    $logger->info("User was automatically logged out after inactivity");
} else {
    $logger->info("User has logged out");
}
// Destroy session completely
session_unset();
session_destroy();

// Redirect to login page
header('Location: /login.php' . (isset($_GET['timeout']) ? '?timeout=1' : ''));
exit();

?>