<?php
    include_once('../control.php');

    $edit_mode = false;
    $editing = null;

    if (isset($_SESSION['type']) && $_SESSION['type'] == 4 && isset($_GET['id_course'])) {
        $id_course = $_GET['id_course'];

        $query = $conect->prepare("SELECT * FROM curso WHERE id_curso = ?");
        $query->bind_param("i", $id_course);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 1) {
            $editing = $result->fetch_assoc();
            $edit_mode = true;
        }
    }

    // Define os valores a serem preenchidos no formulário
    $name = $editing['nome'] ?? '';
    $code = $editing['codigo'] ?? '';
    $campus = $editing['campus'] ?? '';
    $turn = $editing['turno'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/icons/faviconccne.png" type="image/x-icon">
    <title>Portal de Bolsas CCNE</title>
</head>
<body>
    <form action="../processes/process_course.php" method="post">
        <h1><?= $edit_mode ? 'Alteração ' : 'Cadastro' ?> de Curso</h1>

        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br>

        <label for="code">Código</label>
        <input type="number" id="code" name="code" value="<?= htmlspecialchars($code) ?>" required><br>

        <label for="campus">Campus:</label>
        <select id="campus" name="campus">
            <option value = "Santa Maria"           <?= $campus == "Santa Maria"          ? 'selected' : '' ?>>Santa Maria</option>
            <option value = "Frederico Westphalen"  <?= $campus == "Frederico Westphalen" ? 'selected' : '' ?>>Frederico Westphalen</option>
            <option value = "Cachoeira do Sul"      <?= $campus == "Cachoeira do Sul"     ? 'selected' : '' ?>>Cachoeira do Sul</option>
            <option value = "Palmeira das Missões"  <?= $campus == "Palmeira das Missões" ? 'selected' : '' ?>>Palmeira das Missões</option>
        </select>
        <br>

        <label for="turn">Campus:</label>
        <select id="turn" name="turn">
            <option value = "Matutino"   <?= $turn == "Matutino"   ? 'selected' : '' ?>>Matutino</option>
            <option value = "Vespertino" <?= $turn == "Vespertino" ? 'selected' : '' ?>>Vespertino</option>
            <option value = "Noturno"    <?= $turn == "Noturno"    ? 'selected' : '' ?>>Noturno</option>
            <option value = "Diurno"     <?= $turn == "Diurno"     ? 'selected' : '' ?>>Diurno</option>
        </select>
        <br>

        <button type="reset">Limpar</button>
        <a href="../index.php">Voltar</a>
        <br>

        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($id_course)?>" required>
            <button type="submit" name="edit">Salvar Alterações</button>
        <?php else: ?>
            <button type="submit" name="register">Cadastrar</button>
        <?php endif; ?>
    </form>
</body>
</html>