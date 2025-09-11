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
    <?php if (isset($_SESSION['id_user'])) {
        include_once(__DIR__ . '/../templates/header.php');
    } ?>

    <main>
        <div class="row justify-content-center">
            <div class="col-md-8 mt-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h3><?= $edit_mode ? 'Alteração' : 'Cadastro' ?> de Usuário</h3>
                        <?php include_once(__DIR__ . '/../templates/alerts.php'); ?>
                    </div>
                    <div class="card-body">
                        <form action="../processes/process_user.php" method="post">

                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id_user" value="<?= htmlspecialchars($id_user) ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome Completo:</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail Institucional:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Senha:</label>
                                    <input type="password" class="form-control" id="password" name="password" <?= !$edit_mode ? 'required' : '' ?>>
                                    <?php if ($edit_mode): ?>
                                        <div class="form-text">Deixe em branco para não alterar a senha.</div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="repeat_password" class="form-label">Repetir Senha:</label>
                                    <input type="password" class="form-control" id="repeat_password" name="repeat_password" <?= !$edit_mode ? 'required' : '' ?>>
                                </div>
                            </div>

                            <?php // Caso 1: O usuário logado é um Gerente E ele está editando o perfil de OUTRA PESSOA.
                            if (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE && $edit_mode && $_SESSION['id_user'] != $id_user) : ?>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipo de Usuário:</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="<?= RULE_ESTUDANTE ?>" <?= $type == RULE_ESTUDANTE ? 'selected' : '' ?>>Estudante</option>
                                        <option value="<?= RULE_ORIENTADOR ?>" <?= $type == RULE_ORIENTADOR ? 'selected' : '' ?>>Orientador</option>
                                        <option value="<?= RULE_DIRECAO ?>" <?= $type == RULE_DIRECAO ? 'selected' : '' ?>>Direção</option>
                                        <option value="<?= RULE_FINANCEIRO ?>" <?= $type == RULE_FINANCEIRO ? 'selected' : '' ?>>Financeiro</option>
                                        <option value="<?= RULE_GERENTE ?>" <?= $type == RULE_GERENTE ? 'selected' : '' ?>>Gerente</option>
                                    </select>
                                </div>
                            <?php 
                            // Caso 2: O usuário logado é um Gerente E ele está editando A SI MESMO.
                            elseif (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE && $edit_mode && $_SESSION['id_user'] == $id_user) : ?>
                                <div class="mb-3">
                                    <label for="type" class="form-label">Tipo de Usuário:</label>
                                    <select class="form-select" id="type" name="type_disabled" disabled>
                                        <option>Gerente</option>
                                    </select>
                                    <input type="hidden" name="type" value="<?= RULE_GERENTE ?>">
                                    <div class="form-text">O Gerente Master não pode alterar o seu próprio nível de acesso.</div>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                            <?php endif; ?>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <a href="../central.php" class="btn btn-secondary">Voltar</a>
                                <div>
                                    <?php if ($edit_mode): ?>
                                        <button type="submit" name="edit" class="btn btn-primary">Salvar Alterações</button>
                                    <?php else: // Caso 3: Outros usuários (não-gerentes) ou um novo cadastro público. ?>
                                        <button type="submit" name="register" class="btn btn-success">Cadastrar</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
</body>

</html>