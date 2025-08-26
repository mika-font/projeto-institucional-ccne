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
        header('Location: ' . BASE_URL . '/forms/form_user.php?msg=campos_vazios');
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . BASE_URL . '/forms/form_user.php?msg=email_invalido');
        exit();
    }
    $allowed_domains = ['ufsm.br', 'acad.ufsm.br'];
    $email_domain = substr(strrchr($email, "@"), 1);
    if (!in_array($email_domain, $allowed_domains)) {
        header('Location: ' . BASE_URL . '/forms/form_user.php?msg=dominio_invalido');
        exit();
    }
    if ($password !== $repeat_password) {
        header('Location: ' . BASE_URL . '/forms/form_user.php?msg=senhas_nao_coincidem');
        exit();
    }

    $stmt = $conect->prepare("SELECT id_usuario FROM usuario WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        header('Location: ' . BASE_URL . '/forms/form_user.php?msg=email_existente');
        exit();
    }
    $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
    $insert_user = $conect->prepare("INSERT INTO usuario (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
    $insert_user->bind_param("sssi", $name, $email, $encrypted_password, $type);

    if ($insert_user->execute()) {
        if (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE) {
            header("Location: " . BASE_URL . "/lists/list_user.php?msg=cadastro_sucesso");
        } else {
            header("Location: " . BASE_URL . "/index.php?msg=cadastro_sucesso");
        }
        exit();
    } else {
        header('Location: ' . BASE_URL . '/forms/form_user.php?msg=erro_cadastro');
        exit();
    }
} else if (isset($_POST['edit'])) {
    include_once('../control.php');

    $id_to_edit = intval($_POST['id']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];
    $type = intval($_POST['type']);

    if (!isset($_SESSION['id_user']) || ($_SESSION['id_user'] != $id_to_edit && $_SESSION['type'] != RULE_GERENTE)) {
        header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
        exit();
    }

    if (empty($id_to_edit) || empty($name) || empty($email) || ($type === null || $type === '')) {
        header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit . '&msg=campos_vazios');
        exit();
    }

    $allowed_domains = ['ufsm.br', 'acad.ufsm.br'];
    $email_domain = substr(strrchr($email, "@"), 1);
    if (!in_array($email_domain, $allowed_domains)) {
        header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit . '&msg=dominio_invalido');
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
            header('Location: ' . BASE_URL . '/forms/form_user.php?id=' . $id_to_edit . '&msg=email_existente');
            exit();
        }
    }

    $sql_parts = ["nome = ?", "email = ?", "tipo = ?"];
    $params = [$name, $email, $type];
    $types = "ssi";

    if (!empty($password)) {
        if ($password !== $repeat_password) {
            header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit . '&msg=senhas_nao_coincidem');
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
        header("Location: " . BASE_URL . "/central.php?msg=alteracao_sucesso");
    } else {
        header('Location: ' . BASE_URL . '/forms/form_user.php?id_user=' . $id_to_edit . '&msg=erro_alteracao');
    }
    exit();
} else if (isset($_POST['delete'])) {
    include_once('../control.php');

    $id_to_delete = $_POST['id_user'] ?? null;

    // Somente Gerentes podem excluir, e não podem excluir a si mesmos
    if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
        header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
        exit();
    }
    if ($_SESSION['id_user'] == $id_to_delete) {
        header("Location: " . BASE_URL . "/lists/list_user.php?msg=erro_autoexclusao");
        exit();
    }
    if (empty($id_to_delete)) {
        header("Location: " . BASE_URL . "/lists/list_user.php?msg=id_invalido");
        exit();
    }

    try {
        $delete = $conect->prepare("DELETE FROM usuario WHERE id_usuario = ?");
        $delete->bind_param("i", $id_to_delete);
        $delete->execute();

        if ($delete->affected_rows > 0) {
            header("Location: " . BASE_URL . "/lists/list_user.php?msg=exclusao_sucesso");
        } else {
            header("Location: " . BASE_URL . "/lists/list_user.php?msg=usuario_nao_encontrado");
        }
        exit();
    } catch (mysqli_sql_exception $e) {
        // Erro 1451 é o código para violação de chave estrangeira (FK constraint)
        if ($e->getCode() == 1451) {
            header("Location: " . BASE_URL . "/lists/list_user.php?msg=erro_exclusao_vinculo");
        } else {
            // Outro erro de banco de dados
            header("Location: " . BASE_URL . "/lists/list_user.php?msg=erro_banco");
        }
        exit();
    }
}
