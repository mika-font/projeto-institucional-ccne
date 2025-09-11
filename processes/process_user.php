<?php
include_once('../configs/rules.php');
include_once('../conect.php');
$conect = conectServer();

if (isset($_POST['register'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $type = intval($_POST['type']);

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php');
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'E-mail inválido.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php');
        exit();
    }
    $allowed_domains = ['ufsm.br', 'acad.ufsm.br'];
    $email_domain = substr(strrchr($email, "@"), 1);
    if (!in_array($email_domain, $allowed_domains)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Domínio de e-mail inválido. Use um e-mail da UFSM.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php');
        exit();
    }
    if ($password !== $repeat_password) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'As senhas não coincidem.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php');
        exit();
    }

    $stmt = $conect->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'E-mail já está em uso.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php');
        exit();
    }
    $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_user = $conect->prepare("INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    $insert_user->bind_param("sssi", $name, $email, $encrypted_password, $type);

    if ($insert_user->execute()) {
        if (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Usuário cadastrado com sucesso.'
            ];
            header("Location: " . BASE_URL . "/lists/list_user.php");
        } else {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Cadastro realizado com sucesso. Faça login para continuar.'
            ];
            header("Location: " . BASE_URL . "/index.php");
        }
        exit();
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erro ao cadastrar usuário. Tente novamente mais tarde.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php');
        exit();
    }
} else if (isset($_POST['edit'])) {
    include_once('../control.php');

    $id_to_edit = intval($_POST['id_user']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $type = intval($_POST['type']);

    if (!isset($_SESSION['id_user']) || ($_SESSION['id_user'] != $id_to_edit && $_SESSION['type'] != RULE_GERENTE)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Acesso não autorizado.'
        ];
        header("Location: " . BASE_URL . "/central.php");
        exit();
    }

    if (empty($id_to_edit) || empty($name) || empty($email) || ($type === null || $type === '')) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit);
        exit();
    }

    $allowed_domains = ['ufsm.br', 'acad.ufsm.br'];
    $email_domain = substr(strrchr($email, "@"), 1);
    if (!in_array($email_domain, $allowed_domains)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Domínio de e-mail inválido. Use um e-mail da UFSM.'
        ];
        header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit);
        exit();
    }

    $query_current_email = $conect->prepare("SELECT email FROM usuario WHERE id_usuario = ?");
    $query_current_email->bind_param("i", $id_to_edit);
    $query_current_email->execute();
    $current_email = $query_current_email->get_result()->fetch_assoc()['email'];

    if ($email !== $current_email) {
        $check_stmt = $conect->prepare("SELECT id_usuario FROM usuario WHERE email = ? AND id_usuario != ?");
        $check_stmt->bind_param("si", $email, $id_to_edit);
        $check_stmt->execute();

        if ($check_stmt->get_result()->num_rows > 0) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'E-mail já está em uso por outro usuário.'
            ];
            header('Location: ' . BASE_URL . '/forms/form_user.php?id=' . $id_to_edit);
            exit();
        }
    }

    $sql_parts = ["nome = ?", "email = ?", "tipo = ?"];
    $params = [$name, $email, $type];
    $types = "ssi";

    if (!empty($password)) {
        if ($password !== $repeat_password) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'As senhas não coincidem.'
            ];
            header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit);
            exit();
        }
        $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_parts[] = "senha = ?";
        $params[] = $encrypted_password;
        $types .= "s";
    }

    $params[] = $id_to_edit;
    $types .= "i";

    $sql = "UPDATE usuario SET " . implode(", ", $sql_parts) . " WHERE id_usuario = ?";
    $update = $conect->prepare($sql);
    $update->bind_param($types, ...$params);

    if ($update->execute()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Usuário alterado com sucesso.'
        ];
        header("Location: " . BASE_URL . "/central.php");
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erro ao alterar usuário. Tente novamente mais tarde.'
        ];  
        header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit);
    }
    exit();
} else if (isset($_POST['delete'])) {
    include_once('../control.php');

    $id_to_delete = $_POST['id_user'] ?? null;

    // Somente Gerentes podem excluir, e não podem excluir a si mesmos
    if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Acesso não autorizado.'
        ];
        header("Location: " . BASE_URL . "/central.php");
        exit();
    }
    if ($_SESSION['id_user'] == $id_to_delete) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Você não pode excluir sua própria conta.'
        ];
        header("Location: " . BASE_URL . "/lists/list_user.php");
        exit();
    }
    if (empty($id_to_delete)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ID do usuário não encontrado.'
        ];  
        header("Location: " . BASE_URL . "/lists/list_user.php");
        exit();
    }

    try {
        $delete = $conect->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $delete->bind_param("i", $id_to_delete);
        $delete->execute();

        if ($delete->affected_rows > 0) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Usuário excluído com sucesso.'
            ];
            header("Location: " . BASE_URL . "/lists/list_user.php");
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Usuário não encontrado ou já foi excluído.'
            ];
            header("Location: " . BASE_URL . "/lists/list_user.php");
        }
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Não é possível excluir o usuário porque ele está vinculado a outros registros.'
            ];
            header("Location: " . BASE_URL . "/lists/list_user.php");
            exit();
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Erro ao excluir usuário. Tente novamente mais tarde.'
            ];
            header("Location: " . BASE_URL . "/lists/list_user.php");
            exit();
        }
        exit();
    }
}
