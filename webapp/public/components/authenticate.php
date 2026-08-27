<?php

session_start();

$IDLE_TIMEOUT = 30; // 30 seconds

if (!isset($_SESSION['authenticated'])) {
    header('Location: /login.php');
    exit;
}

// Code for logging user out
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $IDLE_TIMEOUT)) {
    session_unset();
    session_destroy();
    header('Location: /login.php?timeout=1');
    exit;
}

$_SESSION['last_activity'] = time();

?>