<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || !in_array($_SESSION['type'], [RULE_ORIENTADOR, RULE_DIRECAO, RULE_GERENTE])) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$id_bag = intval($_GET['id_bag'] ?? 0);

// Busca o nome da bolsa para o título da página
$query_bag = $conect->prepare("SELECT nome FROM bolsa WHERE id_bolsa = ?");
$query_bag->bind_param("i", $id_bag);
$query_bag->execute();
$result_bag = $query_bag->get_result();
if ($result_bag->num_rows !== 1) {
    header("Location: " . BASE_URL . "/central.php?msg=bolsa_nao_encontrada");
    exit();
}
$bag = $result_bag->fetch_assoc();
$page_title = "Candidatos para: " . htmlspecialchars($bag['nome']);

$query_candidates = $conect->prepare(
    "SELECT u.id_usuario, u.nome, u.email, i.data_inscricao, i.situacao
     FROM usuario AS u
     JOIN inscricao AS i ON u.id_usuario = i.id_estudante
     WHERE i.id_bolsa = ? ORDER BY u.nome ASC"
);
$query_candidates->bind_param("i", $id_bag);
$query_candidates->execute();
$result = $query_candidates->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Portal de Bolsas CCNE</title>
</head>

<body>
    <div class="container mt-4">
        <h1 class="mb-4"><?= $page_title ?></h1>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Nome do Candidato</th>
                        <th>E-mail</th>
                        <th>Data da Inscrição</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($candidate = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($candidate['nome']) ?></td>
                                <td><?= htmlspecialchars($candidate['email']) ?></td>
                                <td><?= (new DateTime($candidate['data_inscricao']))->format('d/m/Y H:i') ?></td>
                                <td><?= htmlspecialchars($candidate['situacao']) ?></td>
                                <td class="text-center">
                                    <a href="../details/details_candidate.php?id_bolsa=<?= $id_bag ?>&id_estudante=<?= $candidate['id_usuario'] ?>" class="btn btn-sm btn-info">Ver Detalhes</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum candidato inscrito para esta bolsa.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <a href="javascript:history.back()" class="btn btn-secondary mt-3">Voltar</a>
    </div>
</body>

</html>