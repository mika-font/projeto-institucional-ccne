<?php
// Irá servir para depurar possíveis erros nas transações de banco
try{
    $conect->begin_transaction();
    // Código da transação
    $conect->commit();
} catch (Exception $e) {
    $conect->rollback();
    //header("Location: " . BASE_URL . "/forms/form_application.php?id_bag=$id_bag&msg=erro_inscricao");
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