<?php
    session_start();                // Inicia a sessão no arquivo 
    session_unset();                // Limpa todas as variáveis de sessão   
    session_destroy();              // Destroi a sessão

    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time() - 42000, '/');
    }

    header('Location: index.php');
    exit();
?>