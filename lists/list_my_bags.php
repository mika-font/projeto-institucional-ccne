<?php
/*
    Página: Minhas Bolsas
    Descrição: Lista as bolsas associadas ao usuário logado, seja como Estudante, Orientador ou Financeiro.
*/
include_once(__DIR__ . '/../control.php');

// Apenas Estudante, Orientador e Financeiro podem acessar esta página
if (!isset($_SESSION['type']) || !in_array($_SESSION['type'], [RULE_ESTUDANTE, RULE_ORIENTADOR, RULE_FINANCEIRO])) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$user_type = $_SESSION['type'];
$user_id = $_SESSION['id_user'];
$page_title = "";
$result = null;
$query = null;

switch ($user_type) {

    // CASO ORIENTADOR: Listar bolsas pelas quais ele é responsável
    case RULE_ORIENTADOR:
        $page_title = "Minhas Bolsas (Orientador)";
        $query = $conect->prepare(
            "SELECT id_bolsa, nome, situacao FROM bolsa WHERE id_orientador = ? ORDER BY nome ASC"
        );
        $query->bind_param("i", $user_id);
        break;

    // CASO ESTUDANTE: Listar bolsas nas quais ele se inscreveu
    case RULE_ESTUDANTE:
        $page_title = "Minhas Inscrições em Bolsas";
        $query = $conect->prepare(
            "SELECT b.id_bolsa, b.nome AS nome_bolsa, i.data_inscricao, i.situacao AS situacao_inscricao
             FROM bolsa AS b
             JOIN inscricao AS i ON b.id_bolsa = i.id_bolsa
             WHERE i.id_estudante = ? ORDER BY i.data_inscricao DESC"
        );
        $query->bind_param("i", $user_id);
        break;

    // CASO FINANCEIRO: Listar bolsas com pendências financeiras
    case RULE_FINANCEIRO:
        $page_title = "Bolsas com Pendências Financeiras";
        $status_activate = "Pendente de Ativação (Financeiro)";
        $status_disable = "Pendente de Desativação (Financeiro)";
        $query = $conect->prepare(
            "SELECT id_bolsa, nome, codigo, situacao FROM bolsa WHERE situacao IN (?, ?)"
        );
        $query->bind_param("ss", $status_activate, $status_disable);
        break;
}

// Executa a consulta preparada
if (isset($query)) {
    $query->execute();
    $result = $query->get_result();
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/basic.css">
    <title>Portal de Bolsas | CCNE</title>
</head>

<body>
    <?php include_once(__DIR__ . '/../templates/header.php'); ?>
    <div class="container mt-4">
        <h1 class="mb-4"><?= $page_title ?></h1>
        
        <?php include_once(__DIR__ . '/../templates/alerts.php'); ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <?php if ($user_type == RULE_ORIENTADOR): ?>
                        <tr>
                            <th>Nome da Bolsa</th>
                            <th>Situação da Bolsa</th>
                            <th colspan="2" class="text-center">Ações</th>
                        </tr>
                    <?php elseif ($user_type == RULE_ESTUDANTE): ?>
                        <tr>
                            <th>Nome da Bolsa</th>
                            <th>Data da Inscrição</th>
                            <th>Situação da Inscrição</th>
                            <th colspan="2" class="text-center">Ações</th>
                        </tr>
                    <?php elseif ($user_type == RULE_FINANCEIRO): ?>
                        <tr>
                            <th>Nome da Bolsa</th>
                            <th>Código</th>
                            <th>Pendência</th>
                            <th colspan="2" class="text-center">Ações</th>
                        </tr>
                    <?php endif; ?>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($item = $result->fetch_assoc()): ?>
                            <tr>
                                <?php if ($user_type == RULE_ORIENTADOR): ?>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td><?= htmlspecialchars($item['situacao']) ?></td>
                                    <td class="text-center"><a href="../details/details_bag.php?id_bag=<?= $item['id_bolsa'] ?>" class="btn btn-sm btn-secondary">Ver Detalhes</a></td>
                                    <td class="text-center"><a href="list_candidates.php?id_bag=<?= $item['id_bolsa'] ?>" class="btn btn-sm btn-info">Ver Candidatos</a></td>
                                <?php elseif ($user_type == RULE_ESTUDANTE): ?>
                                    <td><?= htmlspecialchars($item['nome_bolsa']) ?></td>
                                    <td><?= (new DateTime($item['data_inscricao']))->format('d/m/Y H:i') ?></td>
                                    <td><strong><?= htmlspecialchars($item['situacao_inscricao']) ?></strong></td>
                                    <td class="text-center"><a href="../details/details_bag.php?id_bag=<?= $item['id_bolsa'] ?>" class="btn btn-sm btn-secondary">Ver Detalhes</a></td>
                                <?php elseif ($user_type == RULE_FINANCEIRO): ?>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td><?= htmlspecialchars($item['codigo_bolsa']) ?></td>
                                    <td><?= htmlspecialchars($item['situacao']) ?></td>
                                    <td class="text-center"><a href="../forms/form_manage_pendency.php?id_bag=<?= $item['id_bolsa'] ?>" class="btn btn-sm btn-warning">Resolver Pendência</a></td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhum item encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="../central.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
</body>

</html>