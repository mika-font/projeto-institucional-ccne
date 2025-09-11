<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_POST['selection']) || !in_array($_SESSION['type'], [RULE_ORIENTADOR, RULE_DIRECAO, RULE_GERENTE])) {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Acesso não autorizado.'
    ];
    header("Location: " . BASE_URL . "/central.php");
    exit();
}

$id_bag = intval($_POST['id_bolsa']);
$id_student = intval($_POST['id_estudante']);

$conect->begin_transaction();
try {
    // Atualiza o status do estudante bolsista na tabela 'inscricao'
    $stmt1 = $conect->prepare("UPDATE inscricao SET situacao = 'Selecionado' WHERE id_estudante = ? AND id_bolsa = ?");
    $stmt1->bind_param("ii", $id_student, $id_bag);
    $stmt1->execute();

    // Atualiza o status dos outros candidatos para 'Não Selecionado'
    $stmt2 = $conect->prepare("UPDATE inscricao SET situacao = 'Não Selecionado' WHERE id_estudante != ? AND id_bolsa = ? AND situacao = 'Inscrito'");
    $stmt2->bind_param("ii", $id_student, $id_bag);
    $stmt2->execute();
    
    // Atualiza a tabela 'bolsa' com o ID do bolsista e o novo status
    $stmt3 = $conect->prepare("UPDATE bolsa SET id_bolsista_atual = ?, situacao = 'Aguardando Documentação' WHERE id_bolsa = ?");
    $stmt3->bind_param("ii", $id_student, $id_bag);
    $stmt3->execute();

    // Se tudo deu certo, confirma as alterações
    $conect->commit();
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'Candidato selecionado com sucesso.'
    ];
    header("Location: ../lists/list_candidates.php?id_bag=$id_bag");
    exit();

} catch (Exception $e) {
    $conect->rollback();
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Erro ao selecionar candidato.'
    ];
    header("Location: ../lists/list_candidates.php?id_bag=$id_bag");
    exit();
}
?>