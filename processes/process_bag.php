<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
    header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
    exit();
}

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $sub_origem = $_POST['id_sub_origem'];
    $sub_alocacao = $_POST['id_sub_alocacao'];
    $orientador_id_form = $_POST['orientador_id'] ?? '';
    $leader_id = !empty($orientador_id_form) ? intval($orientador_id_form) : NULL;
    $code = $_POST['code'];
    $description = $_POST['description'];
    $workload = $_POST['workload'];
    $modality = $_POST['modality'];
    $situation = $_POST['situation'];
    $edital_url = $_POST['edital_url'];

    if (empty($name) || empty($sub_origem) || empty($sub_alocacao) || empty($workload) || empty($modality) || empty($situation) || empty($edital_url)) {
        header('Location: ../forms/form_bag.php?msg=campos_vazios');
        exit();
    }

    $insert = $conect->prepare("INSERT INTO bolsa 
        (nome, id_sub_origem, id_sub_alocacao, id_orientador, codigo, descricao, carga_horaria, modalidade, situacao, edital_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("siiississs", $name, $sub_origem, $sub_alocacao, $leader_id, $code, $description, $workload, $modality, $situation, $edital_url);

    if ($insert->execute()) {
        header("Location: ../lists/list_bag.php?msg=bolsa_cadastrada");
    } else {
        header("Location: ../forms/form_bag.php?msg=erro_cadastro");
    }
    exit();
} else if (isset($_POST['edit'])) {
    $id_bag = intval($_POST['id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $sub_origem = $_POST['id_sub_origem'] ?? null;
    $sub_alocacao = $_POST['id_sub_alocacao'] ?? null;
    $orientador_id_form = $_POST['orientador_id'] ?? '';
    $leader_id = !empty($orientador_id_form) ? intval($orientador_id_form) : NULL;
    $code = $_POST['code'] ?? '';
    $workload = $_POST['workload'] ?? null;
    $description = $_POST['description'] ?? '';
    $modality = $_POST['modality'] ?? '';
    $situation = $_POST['situation'] ?? '';
    $edital_url = $_POST['edital_url'] ?? '';

    if (empty($id_bag) || empty($name) || empty($sub_origem) || empty($sub_alocacao) || empty($workload) || empty($description) || empty($modality) || empty($situation) || empty($edital_url)) {
        header("Location: ../forms/form_bag.php?id_bag=$id_bag&msg=campos_vazios");
        exit();
    }

    $update = $conect->prepare("UPDATE bolsa SET 
        nome = ?, id_sub_origem = ?, id_sub_alocacao = ?, id_orientador = ?, codigo = ?, 
        descricao = ?, carga_horaria = ?, modalidade = ?, situacao = ?, edital_url = ? 
        WHERE id_bolsa = ?");
    $update->bind_param("siiississsi", $name, $sub_origem, $sub_alocacao, $leader_id, $code, $description, $workload, $modality, $situation, $edital_url, $id_bag);

    if ($update->execute()) {
        header("Location: ../lists/list_bag.php?msg=bolsa_alterada");
    } else {
        header("Location: ../forms/form_bag.php?id_bag=$id_bag&msg=erro_alteracao");
    }
    exit();
} else if (isset($_POST['delete'])) {
    $id_bag = intval($_POST['id_bag'] ?? 0);

    if (empty($id_bag)) {
        header("Location: ../lists/list_bag.php?msg=id_nao_encontrado");
        exit();
    }

    $stmt = $conect->prepare("SELECT situacao FROM bolsa WHERE id_bolsa = ?");
    $stmt->bind_param("i", $id_bag);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $bolsa = $result->fetch_assoc();
        $situacao_atual = $bolsa['situacao'];

        // Lista de status que PROIBEM a exclusão direta
        $status_proibidos = [
            'Vigente',
            'Aberta para Inscrições',
            'Em Seleção',
            'Aguardando Documentação',
            'Pendente de Ativação (Financeiro)',
            'Pendente de Desativação (Financeiro)'
        ];

        if (in_array($situacao_atual, $status_proibidos)) {
            // Se o status for proibido, redireciona com uma mensagem de erro específica
            header("Location: ../lists/list_bag.php?msg=erro_exclusao_status_ativo");
            exit();
        }
    }

    try {
        $delete = $conect->prepare("DELETE FROM bolsa WHERE id_bolsa = ?");
        $delete->bind_param("i", $id_bag);
        $delete->execute();

        header("Location: ../lists/list_bag.php?msg=bolsa_excluida");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            header("Location: ../lists/list_bag.php?msg=erro_exclusao_vinculo_bolsa");
        } else {
            // Para qualquer outro erro de banco de dados
            header("Location: ../lists/list_bag.php?msg=erro_banco");
        }
        exit();
    }
}
