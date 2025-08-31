<?php
session_start();
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../conect.php');
$conect = conectServer();

$edit_mode = false;
$user_editing = null;
$can_edit = false;

if (isset($_GET['id_user'])) {
    $id_to_edit = intval($_GET['id_user']);

    if (isset($_SESSION['id_user'])) {
        // Permite a edição se:
        // a) O ID na sessão é o mesmo que o ID na URL (usuário editando a si mesmo)
        // b) O tipo de usuário na sessão é Gerente
        if ($_SESSION['id_user'] == $id_to_edit || $_SESSION['type'] == RULE_GERENTE) {
            $can_edit = true;
        }
    }

    if ($can_edit) {
        $query = $conect->prepare("SELECT id_usuario, nome, email, tipo FROM usuario WHERE id_usuario = ?");
        $query->bind_param("i", $id_to_edit);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 1) {
            $user_editing = $result->fetch_assoc();
            $edit_mode = true;
        }
    } else {
        header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
        exit();
    }
}

$id_user = $user_editing['id_usuario'] ?? null;
$name = $user_editing['nome'] ?? '';
$email = $user_editing['email'] ?? '';
$type = $user_editing['tipo'] ?? 0;

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
    <?php if(isset($_SESSION['id_user'])) {
        include_once(__DIR__ . '/../templates/header.php');
    } ?>

    <main>
        <form action="../processes/process_user.php" method="post">
            <h1><?= $edit_mode ? 'Alteração' : 'Cadastro' ?> de Usuário</h1>

            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($id_user) ?>">
            <?php endif; ?>

            <label for="name">Nome Completo:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required><br>

            <label for="email">E-mail Institucional:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required><br>

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" <?= !$edit_mode ? 'required' : '' ?>><br>
            <?php if ($edit_mode): ?>
                <small>Deixe em branco para não alterar a senha.</small><br>
            <?php endif; ?>

            <label for="repeat_password">Repetir Senha:</label>
            <input type="password" id="repeat_password" name="repeat_password" <?= !$edit_mode ? 'required' : '' ?>><br>

            <?php
            // Caso 1: O usuário logado é um Gerente E ele está editando o perfil de OUTRA PESSOA.
            if (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE && $_SESSION['id_user'] != $id_user) :
            ?>
                <label for="type">Tipo de Usuário:</label>
                <select id="type" name="type" required>
                    <option value="<?= RULE_ESTUDANTE ?>" <?= $type == RULE_ESTUDANTE ? 'selected' : '' ?>>Estudante</option>
                    <option value="<?= RULE_ORIENTADOR ?>" <?= $type == RULE_ORIENTADOR ? 'selected' : '' ?>>Orientador</option>
                    <option value="<?= RULE_DIRECAO ?>" <?= $type == RULE_DIRECAO ? 'selected' : '' ?>>Direção</option>
                    <option value="<?= RULE_FINANCEIRO ?>" <?= $type == RULE_FINANCEIRO ? 'selected' : '' ?>>Financeiro</option>
                    <option value="<?= RULE_GERENTE ?>" <?= $type == RULE_GERENTE ? 'selected' : '' ?>>Gerente</option>
                </select>
                <br>

            <?php
            // Caso 2: O usuário logado é um Gerente E ele está editando A SI MESMO.
            elseif (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE && $_SESSION['id_user'] == $id_user) :
            ?>
                <label for="type">Tipo de Usuário:</label>
                <select id="type" name="type_disabled" disabled>
                    <option>Gerente</option>
                </select>
                <input type="hidden" name="type" value="<?= RULE_GERENTE ?>">
                <br>
                <small>O Gerente Master não pode alterar o seu próprio nível de acesso.</small>
                <br>

            <?php
            // Caso 3: Outros usuários (não-gerentes) ou um novo cadastro público.
            else:
            ?>
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
            <?php endif; ?>

            <a href="../index.php">Voltar</a>

            <?php if ($edit_mode): ?>
                <button type="submit" name="edit">Salvar Alterações</button>
            <?php else: ?>
                <button type="submit" name="register">Cadastrar</button>
            <?php endif; ?>
        </form>
    </main>
    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
</body>

</html>