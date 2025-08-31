<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_POST['selection']) || !in_array($_SESSION['type'], [RULE_ORIENTADOR, RULE_DIRECAO, RULE_GERENTE])) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
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
    header("Location: ../lists/list_candidates.php?id_bag=$id_bag&msg=selecao_sucesso");
    exit();

} catch (Exception $e) {
    // Se algo deu errado, desfaz todas as alterações
    $conect->rollback();
    //header("Location: ../lists/list_candidates.php?id_bag=$id_bag&msg=erro_selecao");
    //exit();
    echo "<h1>Ocorreu um Erro na Transação!</h1>";
    echo "<p><strong>Mensagem detalhada do MySQL:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Código do Erro:</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Arquivo onde o erro ocorreu:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Linha do erro:</strong> " . $e->getLine() . "</p>";
    
    // Interrompe o script para que possamos ler o erro claramente
    die("Execução interrompida para depuração.");
}
?>