<?php
    include_once(__DIR__ . '/../control.php');

    $edit_mode = false;
    $editing = null;
    $id_sub = null;

    if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
        header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
        exit();
    }

    if (isset($_GET['id_sub'])) {
        $id_sub = intval($_GET['id_sub']);

        $query = $conect->prepare("SELECT * FROM subunidade WHERE id_subunidade = ?");
        $query->bind_param("i", $id_sub);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows === 1) {
            $editing = $result->fetch_assoc();
            $edit_mode = true;
        }
    }

    $name = $editing['nome'] ?? '';
    $code = $editing['codigo'] ?? '';
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3><?= $edit_mode ? 'Alteração' : 'Cadastro' ?> de Subunidade</h3>
                        <?php include_once(__DIR__ . '/../templates/alerts.php'); ?>
                    </div>
                    <div class="card-body">
                        <form action="../processes/process_subunit.php" method="post">
                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id_sub" value="<?= htmlspecialchars($id_sub)?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome da Subunidade:</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="code" class="form-label">Código:</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?= htmlspecialchars($code) ?>" required>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between">
                                <a href="../lists/list_subunit.php" class="btn btn-secondary">Voltar</a>
                                <div>
                                    <?php if ($edit_mode): ?>
                                        <button type="submit" name="edit" class="btn btn-primary">Salvar Alterações</button>
                                    <?php else: ?>
                                        <button type="reset" class="btn btn-outline-secondary">Limpar</button>
                                        <button type="submit" name="register" class="btn btn-success">Cadastrar</button>
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