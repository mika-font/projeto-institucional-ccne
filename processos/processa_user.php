<?php
session_start(); // Inicia a sessão
if (isset($_POST['cadastrar'])){
    include_once('../conect.php');
    $conexao = conectServer();

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $repetirSenha = $_POST['confirmar_senha'];

    if (!empty($nome) && !empty($email) && !empty($senha) && !empty($repetirSenha)) {
        // Verifica se os campos obrigatórios estão preenchidos
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: form_user.php?msg=4"); // E-mail inválido
            exit();
        }

        $dominiosPermitidos = ['ufsm.br', 'acad.ufsm.br'];
        $dominioEmail = substr(strrchr($email, "@"), 1);

        if (!in_array($dominioEmail, $dominiosPermitidos)) {
            header("Location: form_user.php?msg=5"); // Domínio de e-mail não permitido
            exit();
        }

        if($senha != $repetirSenha) {
            header("Location: form_user.php?msg=2"); // Senhas não coincidem
            exit();
        }

        $tipo = 0; // Aluno por padrão
        if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 4) {
            $tipoInput = $_POST['tipo'];
            switch ($tipoInput) {
                case 'orientador':
                    $tipo = 1;
                    break;
                case 'direcao':
                    $tipo = 2;
                    break;
                default:
                    $tipo = 3; // financeiro
            }
        }

        $stmt = $conexao->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            header("Location: form_user.php?msg=3"); // E-mail já cadastrado
            exit();
        }

        $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

        $insert = $conexao->prepare("INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $insert->bind_param("sssi", $nome, $email, $senhaCriptografada, $tipo);

        if ($insert->execute()) {
            if (isset($_SESSION) && $_SESSION['tipo'] == 4) {
                // Redirecionar o gerente para algum lugar específico
            } else {
                header("Location: login.php?msg=1"); // Cadastro realizado com sucesso, redireciona para a página de login
                exit();
            }
        } else {
            echo mysqli_errno($conexao) . ": " . mysqli_error($conexao);
            die();
        }
    } else {
        header("Location: ../forms/form_user.php?msg=1"); // Campos obrigatórios não preenchidos
        exit();
    }
} else if (isset($_POST['alterar'])){
    include_once("../controle.php");

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $repetirSenha = $_POST['confirmar_senha'];
    $tipo = $_POST['tipo'] ?? null; // Tipo só é necessário se o usuário for gerente

    if(!empty($nome) && !empty($email)) {
        // Verifica se os campos obrigatórios estão preenchidos
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: form_user.php?msg=4"); // E-mail inválido
            exit();
        }

        $dominiosPermitidos = ['ufsm.br', 'acad.ufsm.br'];
        $dominioEmail = substr(strrchr($email, "@"), 1);

        if (!in_array($dominioEmail, $dominiosPermitidos)) {
            header("Location: form_user.php?msg=5"); // Domínio de e-mail não permitido
            exit();
        }

        if (!empty($senha) || !empty($repetirSenha)) {
            if ($senha != $repetirSenha) {
                header("Location: form_user.php?msg=2"); // Senhas não coincidem
                exit();
            }
            $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);
            // Adicione a senha no UPDATE
            $update = $conexao->prepare("UPDATE usuario SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id_usuario = ?");
            $update->bind_param("sssii", $nome, $email, $senhaCriptografada, $tipo, $_SESSION['id_usuario']);
        
        } else {
            $update = $conexao->prepare("UPDATE usuario SET nome = ?, email = ?, tipo = ? WHERE id_usuario = ?");
            $update->bind_param("ssii", $nome, $email, $tipo, $_SESSION['id_usuario']);
        }

        if ($update->execute()) {
            header("Location: central.php?msg=6"); // Alteração realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conexao) . ": " . mysqli_error($conexao);
            die();
        }
    } else {
        header("Location: ../forms/form_user.php?msg=1"); // Campos obrigatórios não preenchidos
        exit();
    }

} else if (isset($_POST['excluir'])){
    include_once("../controle.php");
    
    if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 4) {
        // Verifica se o usuário é gerente e pode excluir outros usuários
        $id_usuario = $_POST['id_usuario'] ?? null;
        if (empty($id_usuario)) {
            header("Location: ../list_users?msg=1");  //ID do usuário não encontrado
            exit();
        }

        $delete = $conexao->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $delete->bind_param("i", $id_usuario);

        if ($delete->execute()) {
            header("Location: ../list_users?msg=2"); // Exclusão realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conexao) . ": " . mysqli_error($conexao);
            die();
        }
    } else {
        header("Location: ../list_users?msg=3"); // Usuário não autorizado a excluir outros usuários
        exit();
    }
}
?>