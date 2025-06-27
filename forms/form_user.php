<?php
    session_start();
    include_once("../conect.php");
    $conect = conectServer();

    $edit_mode = false;
    $user_editing = null;

    if (isset($_SESSION['type']) && isset($_GET['id_user'])) {
        $id_user = intval($_GET['id_user']);

        $query = $conect->prepare("SELECT * FROM usuario WHERE id_user = ?");
        $query->bind_param("i", $id_user);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 1) {
            $user_editing = $result->fetch_assoc();
            $edit_mode = true;
        }
    }

    // Define os valores a serem preenchidos no formulário
    $id_user = $user_editing['id_user'] ?? null;
    $name = $user_editing['nome'] ?? '';
    $email = $user_editing['email'] ?? '';
    $type = $user_editing['tipo'] ?? '';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <title>Portal de Bolsas CCNE</title>
</head>

<body>
    <main>
        <form action="../processes/process_user.php" method="post">
            <h1><?= $edit_mode ? 'Alteração' : 'Cadastro' ?> de Usuário</h1>
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>

            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" id="id" value="<?= htmlspecialchars($id_user)?>" required>

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password"><br>

                <label for="repeat_password">Repetir Senha:</label>
                <input type="password" id="repeat_password" name="repeat_password"><br>

            <?php else: ?>

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required><br>

                <label for="repeat_password">Repetir Senha:</label>
                <input type="password" id="repeat_password" name="repeat_password" required><br>

            <?php endif; ?>

            <?php if (isset($_SESSION['type']) && $_SESSION['type'] == 4) : ?>

                <label for="type">Tipo de Usuário:</label>
                <select id="type" name="type">
                    <option value = "4" <?= $type == 4 ? 'selected' : '' ?>>Gerente</option>
                    <option value = "3" <?= $type == 3 ? 'selected' : '' ?>>Financeiro</option>
                    <option value = "2" <?= $type == 2 ? 'selected' : '' ?>>Direção</option>
                    <option value = "1" <?= $type == 1 ? 'selected' : '' ?>>Orientador</option>
                    <option value = "0" <?= $type == 0 ? 'selected' : '' ?>>Estudante</option>
                </select>
                
            <?php elseif(isset($_SESSION['type'])): ?>
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
            <?php else: ?>
                <input type="hidden" name="type" value = "0">
            <?php endif; ?>
            <br>

            <button type="reset">Limpar</button>
            <a href="../index.php">Voltar</a>
            <br>

            <?php if ($edit_mode): ?>
                <button type="submit" name="edit">Salvar Alterações</button>
            <?php else: ?>
                <button type="submit" name="register">Cadastrar</button>
            <?php endif; ?>
        </form>
    </main>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>