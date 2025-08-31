<?php
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || $_SESSION['type'] != RULE_GERENTE) {
    header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
    exit();
}

$query = $conect->prepare("SELECT * FROM subunidade ORDER BY nome ASC");
$query->execute();
$result = $query->get_result();
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
    <div class="container mt-4">
        <h1>Gerenciamento de Subunidades</h1>
        <p>Listagem de todas as subunidades do sistema.</p>
        <a href="../forms/form_subunit.php" class="btn btn-success mb-3">Adicionar Nova Subunidade</a>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Subunidade</th>
                    <th>Código</th>
                    <th class="text-center">Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sub = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sub['id_subunidade'] ?></td>
                        <td><?= htmlspecialchars($sub['nome']) ?></td>
                        <td><?= htmlspecialchars($sub['codigo']) ?></td>
                        <td class="text-center">
                            <a href='../forms/form_subunit.php?id_sub=<?= $sub['id_subunidade']; ?>' class="btn btn-sm btn-primary">Editar</a>

                            <form action='../processes/process_subunit.php' method='post' style='display:inline;' onsubmit="return confirm('Atenção! Excluir uma subunidade só é possível se não houver bolsas vinculadas a ela. Deseja continuar?');">
                                <input type='hidden' name='id_sub' value='<?= $sub['id_subunidade']; ?>'>
                                <button type='submit' name='delete' class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="../central.php" class="btn btn-secondary">Voltar</a>
    </div>
    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
</body>

</html>