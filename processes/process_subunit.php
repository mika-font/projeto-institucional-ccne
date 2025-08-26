<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $code = $_POST['code'];

    if (!empty($name) && !empty($code)) {

        $insert = $conect->prepare("INSERT INTO subunidade (nome, codigo) VALUES (?, ?)");
        $insert->bind_param("ss", $name, $code);

        if ($insert->execute()) {
            header("Location: ../lists/list_subunit.php?msg=subunidade_cadastrada");
            exit();
        }
    } else {
        header('Location: ../forms/form_subunit.php?msg=campos_vazios');
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
            header("Location: ../lists/list_subunit.php?msg=subunidade_alterada");
            exit();
        }
    } else {
        header("Location: ../forms/form_subunit.php?id_sub=$id_sub&msg=campos_vazios");
        exit();
    }
} else if (isset($_POST['delete'])) {
    $id_sub = intval($_POST['id_sub'] ?? 0);

    if (empty($id_sub)) {
        header("Location: ../lists/list_subunit.php?msg=id_n_encontrado");
        exit();
    }

    try {
        $delete = $conect->prepare("DELETE FROM subunidade WHERE id_subunidade = ?");
        $delete->bind_param("i", $id_sub);
        $delete->execute();

        header("Location: ../lists/list_subunit.php?msg=subunidade_excluida");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) { // Erro de violação de chave estrangeira
            header("Location: ../lists/list_subunit.php?msg=erro_exclusao_vinculo_subunidade");
        } else {
            header("Location: ../lists/list_subunit.php?msg=erro_banco");
        }
        exit();
    }
}