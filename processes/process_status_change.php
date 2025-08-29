<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_POST['change_status']) || !in_array($_SESSION['type'], [RULE_FINANCEIRO, RULE_DIRECAO, RULE_GERENTE])) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$id_bag = intval($_POST['id_bag']);
$novo_status = $_POST['novo_status'];
$id_usuario_logado = $_SESSION['id_user'];

if (empty($id_bag) || empty($novo_status)) {
    header("Location: " . BASE_URL . "/details/details_bag.php?id_bag=$id_bag&msg=erro_dados_insuficientes");
    exit();
}

// Inicia uma transação para garantir a consistência dos dados
$conect->begin_transaction();
try {
    $update = $conect->prepare("UPDATE bolsa SET situacao = ? WHERE id_bolsa = ?");
    $update->bind_param("si", $novo_status, $id_bag);
    $update->execute();

    //Se o status mudou para "Vigente", cria-se um registro no histórico.
    if ($novo_status === 'Vigente') {
        $query_bolsista = $conect->prepare("SELECT id_bolsista_atual FROM bolsa WHERE id_bolsa = ?");
        $query_bolsista->bind_param("i", $id_bag);
        $query_bolsista->execute();
        $result = $query_bolsista->get_result();
        
        if ($result->num_rows === 1) {
            $bolsa = $result->fetch_assoc();
            $id_bolsista = $bolsa['id_bolsista_atual'];

            if (!empty($id_bolsista)) {
                // Insere no histórico com a data de início de hoje
                $insert_history = $conect->prepare(
                    "INSERT INTO historico (id_bolsa, id_estudante, data_inicio) VALUES (?, ?, CURDATE())"
                );
                $insert_history->bind_param("ii", $id_bag, $id_bolsista);
                $insert_history->execute();
            }
        }
    }

    // Se todas as operações foram bem-sucedidas, confirma a transação
    $conect->commit();
    header("Location: " . BASE_URL . "/details/details_bag.php?id_bag=$id_bag&msg=status_alterado_sucesso");
    exit();

} catch (Exception $e) {
    // Se qualquer operação falhou, desfaz tudo
    $conect->rollback();
    header("Location: " . BASE_URL . "/details/details_bag.php?id_bag=$id_bag&msg=erro_banco");
    exit();
}
?>