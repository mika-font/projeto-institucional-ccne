<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || !in_array($_SESSION['type'], [RULE_GERENTE, RULE_DIRECAO])) {
    header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
    exit();
}

$edit_mode = false;
$editing = null;
$id_bag = null;

if (isset($_GET['id_bag'])) {
    $id_bag = intval($_GET['id_bag']);
    $query = $conect->prepare("SELECT * FROM bolsa WHERE id_bolsa = ?");
    $query->bind_param("i", $id_bag);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows === 1) {
        $editing = $result->fetch_assoc();
        $edit_mode = true;
    }
}

$name = $editing['nome'] ?? '';
$sub_origem = $editing['id_sub_origem'] ?? '';
$sub_alocacao = $editing['id_sub_alocacao'] ?? '';
$leader_id = $editing['id_orientador'] ?? '';
$code = $editing['codigo'] ?? '';
$description = $editing['descricao'] ?? '';
$workload = $editing['carga_horaria'] ?? '';
$modality = $editing['modalidade'] ?? '';
$situation = $editing['situacao'] ?? '';
$edital_url = $editing['edital_url'] ?? '';

$subunits_result = $conect->query("SELECT id_subunidade, nome FROM subunidade ORDER BY nome ASC");
$orientadores_result = $conect->query("SELECT id_usuario, nome FROM usuario WHERE tipo = " . RULE_ORIENTADOR . " ORDER BY nome ASC");
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
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3><?= $edit_mode ? 'Alteração ' : 'Cadastro' ?> de Bolsa</h3>
                    </div>
                    <div class="card-body">
                        <form action="../processes/process_bag.php" method="post">
                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id" value="<?= htmlspecialchars($id_bag) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome da Bolsa:</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="id_sub_origem" class="form-label">Subunidade de Origem:</label>
                                    <select class="form-select" id="id_sub_origem" name="id_sub_origem" required>
                                        <option value="">Selecione...</option>
                                        <?php mysqli_data_seek($subunits_result, 0);
                                        while ($sub = $subunits_result->fetch_assoc()) {
                                            $selected = ($sub['id_subunidade'] == $sub_origem) ? 'selected' : '';
                                            echo "<option value='{$sub['id_subunidade']}' $selected>" . htmlspecialchars($sub['nome']) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="id_sub_alocacao" class="form-label">Subunidade de Alocação:</label>
                                    <select class="form-select" id="id_sub_alocacao" name="id_sub_alocacao" required>
                                        <option value="">Selecione...</option>
                                        <?php mysqli_data_seek($subunits_result, 0);
                                        while ($sub = $subunits_result->fetch_assoc()) {
                                            $selected = ($sub['id_subunidade'] == $sub_alocacao) ? 'selected' : '';
                                            echo "<option value='{$sub['id_subunidade']}' $selected>" . htmlspecialchars($sub['nome']) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="leader_id" class="form-label">Orientador Responsável:</label>
                                <select class="form-select" id="leader_id" name="leader_id">
                                    <option value="">Selecione...</option>
                                    <?php while ($orientador = $orientadores_result->fetch_assoc()) {
                                        $selected = ($orientador['id_usuario'] == $leader_id) ? 'selected' : '';
                                        echo "<option value='{$orientador['id_usuario']}' $selected>" . htmlspecialchars($orientador['nome']) . "</option>";
                                    } ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">Código da Bolsa:</label>
                                    <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($code) ?>" placeholder="Ex: BAE.00.00.00.000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="workload" class="form-label">Carga Horária (h/semana):</label>
                                    <input type="number" class="form-control" id="workload" name="workload" value="<?= htmlspecialchars($workload) ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrição da Bolsa:</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="modality" class="form-label">Modalidade:</label>
                                    <select class="form-select" id="modality" name="modality" required>
                                        <option value="Monitoria" <?= $modality == 'Monitoria' ? 'selected' : '' ?>>Monitoria</option>
                                        <option value="BAE/PRAE" <?= $modality == 'BAE/PRAE' ? 'selected' : '' ?>>BAE/PRAE</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="situation" class="form-label">Situação:</label>
                                    <select class="form-select" id="situation" name="situation" required>
                                        <option value="Aberta para Inscrições"                  <?= $situation == 'Aberta para Inscrições' ? 'selected' : '' ?>>Aberta para Inscrições</option>
                                        <option value="Em Seleção"                              <?= $situation == 'Em Seleção' ? 'selected' : '' ?>>Em Seleção</option>
                                        <option value="Aguardando Documentação"                 <?= $situation == 'Aguardando Documentação' ? 'selected' : '' ?>>Aguardando Documentação</option>
                                        <option value="Pendente de Ativação (Financeiro)"       <?= $situation == 'Pendente de Ativação (Financeiro)' ? 'selected' : '' ?>>Pendente de Ativação (Financeiro)</option>
                                        <option value="Vigente"                                 <?= $situation == 'Vigente' ? 'selected' : '' ?>>Vigente</option>
                                        <option value="Pendente de Desativação (Financeiro)"    <?= $situation == 'Pendente de Desativação (Financeiro)' ? 'selected' : '' ?>>Pendente de Desativação (Financeiro)</option>
                                        <option value="Encerrada"                               <?= $situation == 'Encerrada' ? 'selected' : '' ?>>Encerrada</option>
                                        <option value="Cancelada"                               <?= $situation == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edital_url" class="form-label">Link do Edital:</label>
                                <input type="url" class="form-control" id="edital_url" name="edital_url" value="<?= htmlspecialchars($edital_url) ?>" placeholder="https://site.ufsm.br/edital/123">
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between">
                                <a href="../lists/list_bag.php" class="btn btn-secondary">Voltar</a>
                                <div>
                                    <?php if ($edit_mode): ?>
                                        <button type="submit" name="edit" class="btn btn-primary">Salvar Alterações</button>
                                    <?php else: ?>
                                        <button type="submit" name="register" class="btn btn-success">Cadastrar Bolsa</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
</body>
</html>