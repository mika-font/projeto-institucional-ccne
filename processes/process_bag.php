<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Acesso não autorizado.'
    ];
    header('Location: ' . BASE_URL . '/central.php');
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

        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header('Location: ../forms/form_bag.php');
        exit();
    }

    $insert = $conect->prepare("INSERT INTO bolsa 
        (nome, id_sub_origem, id_sub_alocacao, id_orientador, codigo, descricao, carga_horaria, modalidade, situacao, edital_url) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("siiississs", $name, $sub_origem, $sub_alocacao, $leader_id, $code, $description, $workload, $modality, $situation, $edital_url);

    if ($insert->execute()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Bolsa cadastrada com sucesso.'
        ];
        header("Location: ../lists/list_bag.php");
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erro ao cadastrar bolsa.'
        ];
        header("Location: ../forms/form_bag.php");
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
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Por favor, preencha todos os campos.'
        ];
        header("Location: ../forms/form_bag.php?id_bag=$id_bag");
        exit();
    }

    $update = $conect->prepare("UPDATE bolsa SET 
        nome = ?, id_sub_origem = ?, id_sub_alocacao = ?, id_orientador = ?, codigo = ?, 
        descricao = ?, carga_horaria = ?, modalidade = ?, situacao = ?, edital_url = ? 
        WHERE id_bolsa = ?");
    $update->bind_param("siiississsi", $name, $sub_origem, $sub_alocacao, $leader_id, $code, $description, $workload, $modality, $situation, $edital_url, $id_bag);

    if ($update->execute()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Bolsa alterada com sucesso.'
        ];
        header("Location: ../lists/list_bag.php");
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erro ao alterar bolsa.'
        ];
        header("Location: ../forms/form_bag.php?id_bag=$id_bag");
    }
    exit();
} else if (isset($_POST['delete'])) {
    $id_bag = intval($_POST['id_bag'] ?? 0);

    if (empty($id_bag)) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'ID da bolsa não encontrado.'
        ];
        header("Location: ../lists/list_bag.php");
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
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Não é possível excluir uma bolsa com status ativo ou em andamento.'
            ];
            header("Location: ../lists/list_bag.php");
            exit();
        }
    }

    try {
        $delete = $conect->prepare("DELETE FROM bolsa WHERE id_bolsa = ?");
        $delete->bind_param("i", $id_bag);
        $delete->execute();

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Bolsa excluída com sucesso.'
        ];
        header("Location: ../lists/list_bag.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Não é possível excluir uma bolsa com vínculos.'
            ];
            header("Location: ../lists/list_bag.php");
        } else {
            $_SESSION['alert'] = [
                'type' => 'danger',
                'message' => 'Erro ao excluir bolsa.'
            ];
            header("Location: ../lists/list_bag.php");
        }
        exit();
    }
}
