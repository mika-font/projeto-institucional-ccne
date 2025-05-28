<?php
//formulário de cadastro e alteração de usuário
// 0 - aluno
// 1 - orientador
// 2 - direção 
// 3 - financeiro
// 4 - gerente 
$tipo = 4;
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
            <h1>Cadastro de Usuário</h1>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required><br>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required><br>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required><br>

            <label for="confirmar_senha">Confirmar Senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required><br>
            <?php if($tipo == 4) : ?>
                <label for="tipo">Tipo de Usuário:</label>
                <select id="tipo" name="tipo">
                    <option value="financeiro">Financeiro</option>
                    <option value="orientador">Orientador</option>
                    <option value="direcao">Direção</option>
                </select>
            <?php endif; ?>
            <br>
            <button type="reset">Limpar</button>
            <a href="../index.php">Voltar</a>
            <button type="submit" name="cadastrar">Cadastrar</button>
        </form>
    </main>
</body>
</html>