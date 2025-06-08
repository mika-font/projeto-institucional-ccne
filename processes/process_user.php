<?php

if (isset($_POST['register'])){
    include_once('../conect.php');
    $conect = conectServer();

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];

    if (!empty($name) && !empty($email) && !empty($password) && !empty($repeat_password)) {
        // Verifica se os campos obrigatórios estão preenchidos
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../forms/form_user.php?msg=4"); // E-mail inválido
            exit();
        }

        $allowed_domains = ['ufsm.br', 'acad.ufsm.br'];
        $emaildomain = substr(strrchr($email, "@"), 1);

        if (!in_array($emaildomain, $allowed_domains)) {
            header("Location: ../forms/form_user.php?msg=5"); // Domínio de e-mail não permitido
            exit();
        }

        if($password != $repeat_password) {
            header("Location: ../forms/form_user.php?msg=2"); // Senhas não coincidem
            exit();
        }

        $type = 0; // Aluno por padrão
        if (isset($_SESSION['type']) && $_SESSION['type'] == 4) {
            $type = $_POST['type'];
        }

        $stmt = $conect->prepare("SELECT id_user FROM usuario WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            header("Location: ../forms/form_user.php?msg=3"); // E-mail já cadastrado
            exit();
        }

        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

        $insert = $conect->prepare("INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
        $insert->bind_param("sssi", $name, $email, $encrypted_password, $type);

        if ($insert->execute()) {
            if (isset($_SESSION) && $_SESSION['type'] == 4) {
                header("Location: ../central.php?msg=1"); // Cadastro realizado com sucesso (gerente)
            } else {
                header("Location: ../index.php?msg=1"); // Cadastro realizado com sucesso, redireciona para a página de login
                exit();
            }
        } else {
            echo mysqli_errno($conect) . ": " . mysqli_error($conect);
            die();
        }
    } else {
        header("Location: ../forms/form_user.php?msg=1"); // Campos obrigatórios não preenchidos
        exit();
    }
} else if (isset($_POST['edit'])){
    include_once("../control.php");

    $id_user = intval($_POST['id']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $type = $_POST['type'];

    if(!empty($id_user) && !empty($name) && !empty($email) && $type != null && $type != '') {
        // Verifica se os campos obrigatórios estão preenchidos

        $type = intval($type);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: ../forms/form_user.php??id_user=$id_user&msg=4"); // E-mail inválido
            exit();
        }

        $allowed_domains = ['ufsm.br', 'acad.ufsm.br'];
        $emaildomain = substr(strrchr($email, "@"), 1);

        if (!in_array($emaildomain, $allowed_domains)) {
            header("Location: ../forms/form_user.php??id_user=$id_user&msg=5"); // Domínio de e-mail não permitido
            exit();
        }

        if (!empty($password) || !empty($repeat_password)) {
            // Caso o usuário deseja alterar a senha também.
            if ($password != $repeat_password) {
                header("Location: ../forms/form_user.php??id_user=$id_user&msg=2"); // Senhas não coincidem
                exit();
            }

            $encrypted_password = password_hash($password, PASSWORD_DEFAULT);

            // Adicione a senha no UPDATE
            $update = $conect->prepare("UPDATE usuario SET nome = ?, email = ?, senha = ?, tipo = ? WHERE id_user = ?");
            $update->bind_param("sssii", $name, $email, $encrypted_password, $type, $id_user);
        
        } else {
            $update = $conect->prepare("UPDATE usuario SET nome = ?, email = ?, tipo = ? WHERE id_user = ?");
            $update->bind_param("ssii", $name, $email, $type, $id_user);
        }

        if ($update->execute()) {
            header("Location: ../central.php?msg=6"); // Alteração realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conect) . ": " . mysqli_error($conect);
            die();
        }
    } else {
        var_dump($_POST);
        //header("Location: ../forms/form_user.php?id_user=$id_user&msg=1"); // Campos obrigatórios não preenchidos
        exit();
    }

} else if (isset($_POST['delete'])){
    include_once("../control.php");
    
    if(isset($_SESSION['type']) && $_SESSION['type'] == 4) {
        // Verifica se o usuário é gerente e pode excluir outros usuários
        $id_user = $_POST['id_user'] ?? null;
        if (empty($id_user)) {
            header("Location: ../list_user?msg=1");  //ID do usuário não encontrado
            exit();
        }

        $delete = $conect->prepare("DELETE FROM usuario WHERE id_user = ?");
        $delete->bind_param("i", $id_user);

        if ($delete->execute()) {
            header("Location: ../list_user?msg=2"); // Exclusão realizada com sucesso
            exit();
        } else {
            echo mysqli_errno($conect) . ": " . mysqli_error($conect);
            die();
        }
    } else {
        header("Location: ../list_user?msg=3"); // Usuário não autorizado a excluir outros usuários
        exit();
    }
}
?>