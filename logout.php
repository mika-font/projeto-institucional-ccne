<?php
    include_once('./configs/rules.php');
    session_start();                 
    session_unset();
    session_destroy();

    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 42000, '/');
    }

    header('Location: ' . BASE_URL . '/index.php');
    exit();
?>