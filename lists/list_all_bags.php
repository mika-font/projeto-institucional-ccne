<?php
include_once(__DIR__ . '/../control.php');

// Apenas estudantes podem acessar
if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_ESTUDANTE) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$user_id = $_SESSION['id_user'];
$page_title = "Bolsas Abertas para Inscrição";

// Busca bolsas abertas
$query = $conect->prepare("SELECT id_bolsa, nome, carga_horaria, situacao FROM bolsa WHERE situacao = 'Aberta para Inscrições' ORDER BY nome ASC");
$query->execute();
$result = $query->get_result();

// Busca bolsas em que o estudante já se inscreveu
$applied = [];
$applied_stmt = $conect->prepare("SELECT id_bolsa FROM inscricao WHERE id_estudante = ?");
$applied_stmt->bind_param("i", $user_id);
$applied_stmt->execute();
$res_applied = $applied_stmt->get_result();
while ($row = $res_applied->fetch_assoc()) {
    $applied[] = (int)$row['id_bolsa'];
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
        <h1 class="mb-4"><?= htmlspecialchars($page_title) ?></h1>
        <?php include_once(__DIR__ . '/../templates/alerts.php'); ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Nome da Bolsa</th>
                        <th>Carga Horária</th>
                        <th>Situação</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($bag = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($bag['nome']) ?></td>
                                <td><?= htmlspecialchars($bag['carga_horaria']) ?>h</td>
                                <td><?= htmlspecialchars($bag['situacao']) ?></td>
                                <td class="text-center">
                                    <a href="../details/details_bag.php?id_bag=<?= $bag['id_bolsa'] ?>" class="btn btn-sm btn-secondary">Ver Detalhes</a>

                                    <?php if (in_array((int)$bag['id_bolsa'], $applied)): ?>
                                        <button class="btn btn-sm btn-outline-success" disabled>Inscrito</button>
                                    <?php else: ?>
                                        <a href="../forms/form_application.php?id_bag=<?= $bag['id_bolsa'] ?>" class="btn btn-sm btn-success">Inscrever-se</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Nenhuma bolsa aberta para inscrições no momento.</td>
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