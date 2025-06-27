<?php
include_once('../control.php');

$edit_mode = false;
$editing = null;

if (isset($_SESSION['type']) && $_SESSION['type'] == 4 || $_SESSION['type'] == 2 && isset($_GET['id_bag'])) {
    $id_bag = $_GET['id_bag'];

    $query = $conect->prepare("SELECT * FROM bolsa WHERE id_bolsa = ?");
    $query->bind_param("i", $id_bag);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $editing = $result->fetch_assoc();
        $edit_mode = true;
    }
} else {
    header("Location: ../central.php?msg=10"); // msg=10: Acesso não autorizado
    exit();
}

$id_bag = $editing['id_bolsa'] ?? null;
$id_subunit = $editing['id_subunidade'] ?? '';
$id_subunit_allocation = $bolsa_editing['id_subunidade_alocacao'] ?? ''; // Campo novo
$code = $editing['codigo'] ?? '';
$name = $editing['nome'] ?? '';
$description = $editing['descricao'] ?? '';
$workload_limit = $editing['limite_ch'] ?? '';
$modality = $editing['modality'] ?? '';
$situation = $editing['situacao'] ?? '';
$location = $editing['localizacao'] ?? '';
$link_file = $editing['arquivo'] ?? '';

$subunits_result = $conect->query("SELECT id_subunidade, nome FROM subunidade ORDER BY nome ASC");
$subunits_for_alocacao = $conect->query("SELECT id_subunidade, nome FROM subunidade ORDER BY nome ASC");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <title>Portal de Bolsas CCNE</title>
</head>

<body>
    <form action="../processes/process_bag.php" method="post">
        <h1><?= $edit_mode ? 'Alteração' : 'Cadastro' ?> de Bolsa</h1>

        <?php if ($edit_mode): ?>
            <input type="hidden" name="id_bag" value="<?= htmlspecialchars($id_bag) ?>">
        <?php endif; ?>

        <label for="name">Nome da Bolsa:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br>

        <label for="subunit">Subunidade:</label>
        <select id="subunit" name="id_subunit" required>
            <option value="">Selecione uma subunidade</option>
            <?php
            if ($subunits_result->num_rows > 0) {
                while ($sub = $subunits_result->fetch_assoc()) {
                    // Se estiver em modo de edição, marca a subunidade correta como selecionada
                    $selected = ($sub['id_subunidade'] == $id_subunit) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($sub['id_subunidade']) . "\" $selected>" . htmlspecialchars($sub['nome']) . "</option>";
                }
            }
            ?>
        </select><br>

        <label for="id_subunit_allocation">Subunidade de Alocação (Uso Atual):</label>
        <select id="id_subunit_allocation" name="id_subunit_allocation" required>
            <option value="">Selecione uma subunidade</option>
            <?php
            if ($subunits_para_alocacao->num_rows > 0) {
                while ($sub = $subunits_for_allocation->fetch_assoc()) {
                    // Se estiver editando, seleciona a opção correta. Se estiver cadastrando e a de origem já foi escolhida, pode-se usar JS para pré-selecionar a mesma aqui.
                    $selected = ($sub['id_subunidade'] == $id_subunit_allocation) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($sub['id_subunidade']) . "\" $selected>" . htmlspecialchars($sub['nome']) . "</option>";
                }
            }
            ?>
        </select><br>

        <label for="code">Código:</label>
        <input type="text" id="code" name="code" value="<?= htmlspecialchars($code) ?>"><br>

        <label for="description">Descrição:</label>
        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($description) ?></textarea><br>

        <label for="workload_limit">Carga Horária (h/semana):</label>
        <input type="number" id="workload_limit" name="workload_limit" value="<?= htmlspecialchars($workload_limit) ?>" required><br>

        <label for="modality">Modalidade:</label>
        <input type="text" id="modality" name="modality" value="<?= htmlspecialchars($modality) ?>" placeholder="Ex: Presencial, Remoto"><br>

        <label for="situation">Situação:</label>
        <input type="text" id="situation" name="situation" value="<?= htmlspecialchars($situation) ?>" placeholder="Ex: Aberta, Encerrada"><br>

        <label for="file">Link do Edital:</label>
        <input type="url" id="file" name="file" value="<?= htmlspecialchars($link_file) ?>" placeholder="https://site.ufsm.br/edital/123" style="width: 300px;"><br>

        <br>
        <button type="reset">Limpar</button>
        <a href="../central.php">Voltar</a>
        <br>

        <?php if ($edit_mode): ?>
            <button type="submit" name="edit">Salvar Alterações</button>
        <?php else: ?>
            <button type="submit" name="register">Cadastrar Bolsa</button>
        <?php endif; ?>
    </form>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>

</body>

</html>