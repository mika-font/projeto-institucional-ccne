<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Acesso não autorizado.'
    ];
    header("Location: " . BASE_URL . "/central.php");
    exit();
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $code = $_POST['code'];

    if (!empty($name) && !empty($code)) {

        $insert = $conect->prepare("INSERT INTO subunidade (nome, codigo) VALUES (?, ?)");
        $insert->bind_param("ss", $name, $code);

        if ($insert->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Subunidade cadastrada com sucesso.'
            ];
            header("Location: ../lists/list_subunit.php");
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header('Location: ../forms/form_subunit.php');
        exit();
    }
} else if (isset($_POST['edit'])) {
    $id_sub = intval($_POST['id_sub']);
    $name = $_POST['name'];
    $code = $_POST['code'];

    if (!empty($name) && !empty($code) && !empty($id_sub)) {

        $update = $conect->prepare("UPDATE subunidade SET nome = ?, codigo = ? WHERE id_subunidade = ?");
        $update->bind_param("ssi", $name, $code, $id_sub);

        if ($update->execute()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Subunidade alterada com sucesso.'
            ];
            header("Location: ../lists/list_subunit.php");
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header("Location: ../forms/form_subunit.php?id_sub=$id_sub");
        exit();
    }
} else if (isset($_POST['delete'])) {
    $id_sub = intval($_POST['id_sub'] ?? 0);

    if (empty($id_sub)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ID da subunidade não encontrado.'
        ];
        header("Location: ../lists/list_subunit.php");
        exit();
    }

    try {
        $delete = $conect->prepare("DELETE FROM subunidade WHERE id_subunidade = ?");
        $delete->bind_param("i", $id_sub);
        $delete->execute();

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Subunidade excluída com sucesso.'
        ];
        header("Location: ../lists/list_subunit.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Erro ao excluir subunidade: existem vínculos ativos.'
            ];
            header("Location: ../lists/list_subunit.php");
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Erro ao excluir subunidade.'
            ];
            header("Location: ../lists/list_subunit.php");
        }
        exit();
    }
}