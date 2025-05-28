<?php
session_start(); // Inicia a sessão
if (isset($_POST['cadastrar'])){
    include_once('../conect.php');
    $conexao = conectServer();

    $nome = mysqli_escape_string($conexao, $_POST['nome']);
    $email = mysqli_escape_string($conexao, $_POST['email']);
    $senha = mysqli_escape_string($conexao, $_POST['senha']);
    $repetirSenha = mysqli_escape_string($conexao, $_POST['confirmar_senha']);

    if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 4){
        $tipo = $_POST['tipo']; // Tipo de usuário selecionado no formulário pelo gerente master
        if ($tipo == 'financeiro') {
            $tipo = 3; // Cadastro para financeiro
        } else if ($tipo == 'orientador') {
            $tipo = 1; // Cadastro para orientador
        } else {
            $tipo = 2; // Cadastro para direção
        }
    } else {
        $tipo = 0; // Cadastro padrão para aluno
    }

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

        if ($senha == $repetirSenha) { // Verifica se as senhas coincidem
            $comando = "SELECT email FROM usuario WHERE email='$email'";
            $consulta = mysqli_query($conexao, $comando);

            if (!$consulta) {
                echo mysqli_errno($conexao) . ": " . mysqli_error($conexao);
                die();
            }

            if (mysqli_num_rows($consulta) == 0) { // Verifica se o e-mail já está cadastrado
                $senha_cript = password_hash($senha, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuario (nome, email, senha, tipo) VALUES ('$nome', '$email', '$senha_cript', '$tipo')";
                $resultado = mysqli_query($conexao, $sql);
                if ($resultado == true) {
                    if (isset($_SESSION) && $_SESSION['tipo'] == 4) {
                        // envio o gerente para algum lugar
                    } else {
                        header("Location: login.php?msg=1"); // Cadastro realizado com sucesso, redireciona para a página de login
                        exit();
                    }
                } else {
                    echo mysqli_errno($conexao) . ": " . mysqli_error($conexao);
                    die();
                }
            } else {
                header("Location: form_user.php?msg=3"); // E-mail já cadastrado
                exit();
            }
        } else {
            header("Location: form_user.php?msg=2"); // Senhas não coincidem
            exit();
        }
    } else {
        header("Location: ../forms/form_user.php?msg=1"); // Campos obrigatórios não preenchidos
        exit();
    }

} else if (isset($_POST['alterar'])){
    include_once("../controle.php");

} else if (isset($_POST['excluir'])){
    include_once("../controle.php");
    
}
?>