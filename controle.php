<?php
    session_start();

    // Verifica se o usuário está logado
    if (!isset($_SESSION['email']) || !isset($_SESSION['id_usuario']) || !isset($_SESSION['tipo']) || !isset($_SESSION['ultimo_acesso'])) {
        session_unset();
        session_destroy();
        header('Location: index.php?msg=nao_autorizado');
        exit();
    }

    // Tempo máximo de inatividade
    $tempo_limite = 900; // 15 minutos

    if (isset($_SESSION['ultimo_acesso'])) {
        $tempo_inativo = time() - $_SESSION['ultimo_acesso'];
        
        if ($tempo_inativo > $tempo_limite) {
            session_unset();
            session_destroy();
            header('Location: index.php?msg=timeout');
            exit();
        }
    }

    // Atualiza o tempo do último acesso
    $_SESSION['ultimo_acesso'] = time();

    // Conecta ao banco após verificação da sessão
    include_once('conect.php');
    $conexao = conectServer();
?>
