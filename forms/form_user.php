<?php
    session_start();
    include_once("../conect.php");
    $conexao = conectServer();

    $modoEdicao = false;
    $usuarioEditando = null;

    if (isset($_SESSION['tipo']) && $_SESSION['tipo'] == 4 && isset($_GET['id_usuario'])) {
        $id_usuario = intval($_GET['id_usuario']);

        $query = $conexao->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
        $query->bind_param("i", $id_usuario);
        $query->execute();
        $resultado = $query->get_result();

        if ($resultado->num_rows === 1) {
            $usuarioEditando = $resultado->fetch_assoc();
            $modoEdicao = true;
        }
    }

    // Define os valores a serem preenchidos no formulário
    $nome = $usuarioEditando['nome'] ?? '';
    $email = $usuarioEditando['email'] ?? '';
    $tipo = $usuarioEditando['tipo'] ?? '';
    $id_usuario = $usuarioEditando['id_usuario'] ?? null;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <title>Portal de Bolsas CCNE</title>
</head>
<body>
    <main>
        <form action="../processos/processa_user.php" method="post">
            <h1><?= $modoEdicao ? 'Alteração' : 'Cadastro' ?> de Usuário</h1>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required><br>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" value="<?= $modoEdicao ? '' : 'required' ?>"><br>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" value="<?= $modoEdicao ? '' : 'required' ?>"><br>

            <?php if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 4) : ?>
                <label for="tipo">Tipo de Usuário:</label>
                <select id="tipo" name="tipo">
                    <option value="financeiro" <?= $tipoSelecionado == 3 ? 'selected' : '' ?>>Financeiro</option>
                    <option value="orientador" <?= $tipoSelecionado == 1 ? 'selected' : '' ?>>Orientador</option>
                    <option value="direcao"    <?= $tipoSelecionado == 2 ? 'selected' : '' ?>>Direção</option>
                </select>
            <?php endif; ?>
            <br>
            
            <button type="reset">Limpar</button>
            <a href="../index.php">Voltar</a>
            <br>

            <?php if ($modoEdicao): ?>
                <button type="submit" name="alterar">Salvar Alterações</button>
            <?php else: ?>
                <button type="submit" name="cadastrar">Cadastrar</button>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>