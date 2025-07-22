<?php
    session_start();
    include_once('configs/rules.php');
    // Verifica se o usuário está logado
    if (!isset($_SESSION['email']) || !isset($_SESSION['id_user']) || !isset($_SESSION['type']) || !isset($_SESSION['last_access'])) {
        session_unset();
        session_destroy();
        header('Location:'. BASE_URL .'index.php?msg=nao_autorizado');
        exit();
    }

    // Tempo máximo de inatividade
    $timeout = 900; // 15 minutos

    if (isset($_SESSION['last_access'])) {
        $idle_time = time() - $_SESSION['last_access'];
        
        if ($idle_time > $timeout) {
            session_unset();
            session_destroy();
            header('Location: '. BASE_URL .'index.php?msg=timeout');
            exit();
        }
    }

    // Atualiza o tempo do último acesso
    $_SESSION['last_access'] = time();

    // Conecta ao banco após verificação da sessão
    include_once('conect.php');
    $conect = conectServer();
?>
