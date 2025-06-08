<?php
    include_once('../control.php');

    $edit_mode = false;
    $editing = null;

    if (isset($_SESSION['type']) && $_SESSION['type'] == 4 && isset($_GET['id_sub'])) {
        $id_sub = $_GET['id_sub'];

        $query = $conect->prepare("SELECT * FROM subunidade WHERE id_subunidade = ?");
        $query->bind_param("i", $id_sub);
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
    <form action="../processes/process_subunit.php" method="post">
        <h1><?= $edit_mode ? 'Alteração ' : 'Cadastro' ?> de Subunidade</h1>

        <label for="name">Nome:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br>

        <label for="code">Código</label>
        <input type="number" id="code" name="code" value="<?= htmlspecialchars($code) ?>" required><br>

        <button type="reset">Limpar</button>
        <a href="../index.php">Voltar</a>
        <br>

        <?php if ($edit_mode): ?>
            <input type="hidden" name="id_sub" id="id_sub" value="<?= htmlspecialchars($id_sub)?>" required>
            <button type="submit" name="edit">Salvar Alterações</button>
        <?php else: ?>
            <button type="submit" name="register">Cadastrar</button>
        <?php endif; ?>
    </form>
</body>
</html>