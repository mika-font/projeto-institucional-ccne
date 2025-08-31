<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_GET['id_bag']) || !filter_var($_GET['id_bag'], FILTER_VALIDATE_INT)) {
    header("Location: " . BASE_URL . "/central.php?msg=id_invalido");
    exit();
}
$id_bag = intval($_GET['id_bag']);
$user_id = $_SESSION['id_user'];
$user_type = $_SESSION['type'];

$query = $conect->prepare(
    "SELECT 
        b.*,
        s_origem.nome AS nome_sub_origem,
        s_alocacao.nome AS nome_sub_alocacao,
        o.nome AS nome_orientador
    FROM bolsa AS b
    LEFT JOIN subunidade AS s_origem ON b.id_sub_origem = s_origem.id_subunidade
    LEFT JOIN subunidade AS s_alocacao ON b.id_sub_alocacao = s_alocacao.id_subunidade
    LEFT JOIN usuario AS o ON b.id_orientador = o.id_usuario
    WHERE b.id_bolsa = ?"
);
$query->bind_param("i", $id_bag);
$query->execute();
$result = $query->get_result();

if ($result->num_rows !== 1) {
    header("Location: " . BASE_URL . "/central.php?msg=bolsa_nao_encontrada");
    exit();
}
$bag = $result->fetch_assoc();

$student_has_applied = false;

if ($user_type == RULE_ESTUDANTE) {
    $check_inscricao = $conect->prepare("SELECT id_inscricao FROM inscricao WHERE id_bolsa = ? AND id_estudante = ?");
    $check_inscricao->bind_param("ii", $id_bag, $user_id);
    $check_inscricao->execute();
    if ($check_inscricao->get_result()->num_rows > 0) {
        $student_has_applied = true;
    }
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
    <div class="container mt-5 mb-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2><?= htmlspecialchars($bag['nome']) ?></h2>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Descrição</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($bag['descricao'])) ?></p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Situação:</strong> <span class="badge bg-info"><?= htmlspecialchars($bag['situacao']) ?></span></p>
                                <p><strong>Orientador:</strong> <?= htmlspecialchars($bag['nome_orientador'] ?? 'Não definido') ?></p>
                                <p><strong>Subunidade de Origem:</strong> <?= htmlspecialchars($bag['nome_sub_origem']) ?></p>
                                <p><strong>Subunidade de Alocação:</strong> <?= htmlspecialchars($bag['nome_sub_alocacao']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Código:</strong> <?= htmlspecialchars($bag['codigo'] ?? 'N/A') ?></p>
                                <p><strong>Carga Horária:</strong> <?= htmlspecialchars($bag['carga_horaria']) ?>h / semana</p>
                                <p><strong>Modalidade:</strong> <?= htmlspecialchars($bag['modalidade']) ?></p>
                                <?php if (!empty($bag['edital_url'])): ?>
                                    <p><strong>Edital:</strong> <a href="<?= htmlspecialchars($bag['edital_url']) ?>" target="_blank">Acessar Edital</a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($bag['descricao'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Ações Disponíveis</h4>
                    </div>
                    <div class="card-body d-grid gap-2">

                        <?php if ($user_type == RULE_ESTUDANTE): ?>
                            <?php if ($bag['situacao'] == 'Aberta para Inscrições'): ?>
                                <?php if ($student_has_applied): ?>
                                    <button class="btn btn-secondary" disabled>Inscrição Realizada</button>
                                <?php else: ?>
                                    <a href="../forms/form_application.php?id_bag=<?= $id_bag ?>" class="btn btn-primary">Inscrever-se na Bolsa</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary" disabled>Inscrições Fechadas</button>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if ($user_type == RULE_ORIENTADOR || in_array($user_type, [RULE_DIRECAO, RULE_GERENTE])): ?>
                            <a href="../lists/list_candidates.php?id_bag=<?= $id_bag ?>" class="btn btn-info">Visualizar Candidatos</a>
                            <?php if ($bag['situacao'] == 'Em Seleção'): ?>
                                <a href="../lists/list_candidates.php?id_bag=<?= $id_bag ?>" class="btn btn-primary">Selecionar Bolsista</a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (in_array($user_type, [RULE_FINANCEIRO, RULE_DIRECAO, RULE_GERENTE])): ?>
                            <hr>
                            <h5>Alterar Status da Bolsa</h5>
                            <form action="../processes/process_status_change.php" method="POST">
                                <input type="hidden" name="id_bag" value="<?= $id_bag ?>">

                                <select class="form-select mb-2" name="novo_status" required>
                                    <?php if ($user_type == RULE_FINANCEIRO): ?>
                                        <option value="Vigente">Mover para "Vigente"</option>
                                        <option value="Encerrada">Mover para "Encerrada"</option>
                                    <?php else: 
                                    ?>
                                        <option value="Aberta para Inscrições" <?= $bag['situacao'] == 'Aberta para Inscrições' ? 'selected' : '' ?>>Aberta para Inscrições</option>
                                        <option value="Em Seleção" <?= $bag['situacao'] == 'Em Seleção' ? 'selected' : '' ?>>Em Seleção</option>
                                        <option value="Aguardando Documentação" <?= $bag['situacao'] == 'Aguardando Documentação' ? 'selected' : '' ?>>Aguardando Documentação</option>
                                        <option value="Pendente de Ativação (Financeiro)" <?= $bag['situacao'] == 'Pendente de Ativação (Financeiro)' ? 'selected' : '' ?>>Pendente de Ativação (Financeiro)</option>
                                        <option value="Vigente" <?= $bag['situacao'] == 'Vigente' ? 'selected' : '' ?>>Vigente</option>
                                        <option value="Pendente de Desativação (Financeiro)" <?= $bag['situacao'] == 'Pendente de Desativação (Financeiro)' ? 'selected' : '' ?>>Pendente de Desativação (Financeiro)</option>
                                        <option value="Encerrada" <?= $bag['situacao'] == 'Encerrada' ? 'selected' : '' ?>>Encerrada</option>
                                        <option value="Cancelada" <?= $bag['situacao'] == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                    <?php endif; ?>
                                </select>

                                <button type="submit" name="change_status" class="btn btn-warning w-100">Atualizar Status</button>
                            </form>
                        <?php endif; ?>

                        <a href="javascript:history.back()" class="btn btn-outline-secondary mt-3">Voltar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
</body>

</html>