<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_POST['application']) || $_SESSION['type'] != RULE_ESTUDANTE) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$id_bag = intval($_POST['id_bag']);
$user_id = $_SESSION['id_user'];

$matricula = $_POST['matricula'];
$id_curso = intval($_POST['id_curso']);
$telefone = $_POST['telefone'];
$cod_banco = $_POST['cod_banco'];
$agencia = $_POST['agencia'];
$conta = $_POST['conta'];

$disponibilidade_horarios = json_encode($_POST['horarios'] ?? []);
$num_horarios_selecionados = count($_POST['horarios'] ?? []);

if(empty($matricula) || empty($id_curso) || empty($telefone) || empty($cod_banco) || empty($agencia) || empty($conta) || empty($disponibilidade_horarios)) {
    header("Location: " . BASE_URL . "/forms/form_application.php?id_bag=$id_bag&msg=campos_obrigatorios");
    exit();
}

$query_bolsa = $conect->prepare("SELECT carga_horaria FROM bolsa WHERE id_bolsa = ?");
$query_bolsa->bind_param("i", $id_bag);
$query_bolsa->execute();
$result_bolsa = $query_bolsa->get_result();
if ($result_bolsa->num_rows !== 1) {
    header("Location: " . BASE_URL . "/central.php?msg=bolsa_nao_encontrada");
    exit();
}
$bag = $result_bolsa->fetch_assoc();
$carga_horaria_exigida = (int)$bag['carga_horaria'];

if ($num_horarios_selecionados !== $carga_horaria_exigida) {
    header("Location: " . BASE_URL . "/forms/form_application.php?id_bag=$id_bag&msg=horas_invalidas");
    exit();
}

$conect->begin_transaction();
try {
    // Verifica se o estudante possui dados
    $query_dados = $conect->prepare("SELECT id_estudante FROM dados_estudante WHERE id_estudante = ?");
    $query_dados->bind_param("i", $user_id);
    $query_dados->execute();
    $tem_dados = $query_dados->get_result()->num_rows > 0;

    if ($tem_dados) {
        // Se já tem, atualiza os dados
        $update = $conect->prepare(
            "UPDATE dados_estudante SET matricula = ?, id_curso = ?, telefone = ?, cod_banco = ?, agencia = ?, conta = ? WHERE id_usuario = ?"
        );
        $update->bind_param("siiisssi", $matricula, $id_curso, $telefone, $cod_banco, $agencia, $conta, $user_id);
        $update->execute();
    } else {
        // Se não tem, insere os novos dados
        $insert = $conect->prepare(
            "INSERT INTO dados_estudante (id_usuario, matricula, id_curso, telefone, cod_banco, agencia, conta) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $insert->bind_param("isiisss", $user_id, $matricula, $id_curso, $telefone, $cod_banco, $agencia, $conta);
        $insert->execute();
    }

    // Agora insere a inscrição
    $situacao_inicial = "Inscrito";
    $insert_inscricao = $conect->prepare(
        "INSERT INTO inscricao (id_bolsa, id_estudante, data_inscricao, situacao, disponibilidade) 
         VALUES (?, ?, NOW(), ?, ?)"
    );
    $insert_inscricao->bind_param("iiss", $id_bag, $user_id, $situacao_inicial, $disponibilidade_horarios);
    $insert_inscricao->execute();

    $conect->commit();
    header("Location: " . BASE_URL . "/lists/list_my_bags.php?msg=inscricao_sucesso");
    exit();

} catch (Exception $e) {
    // Desfaz tudo se deu errado
    $conect->rollback();
    header("Location: " . BASE_URL . "/forms/form_application.php?id_bag=$id_bag&msg=erro_inscricao");
    exit();
}
?>