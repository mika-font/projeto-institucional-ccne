<?php
include_once(__DIR__ . '/../control.php');

if (isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE) {
    $query = $conect->prepare("SELECT * FROM curso ORDER BY nome ASC");
    $query->execute();
    $result = $query->get_result();
} else {
    header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
    exit();
}
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
        <h1>Gerenciamento de Cursos</h1>
        <p>Listagem de todos os cursos do sistema.</p>
        <a href="../forms/form_course.php" class="btn btn-success mb-3">Adicionar Novo Curso</a>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Curso</th>
                    <th>Código</th>
                    <th>Campus</th>
                    <th>Turno</th>
                    <th class="text-center">Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($course = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $course['id_curso'] ?></td>
                        <td><?= htmlspecialchars($course['nome']) ?></td>
                        <td><?= htmlspecialchars($course['codigo']) ?></td>
                        <td><?= htmlspecialchars($course['campus']) ?></td>
                        <td><?= htmlspecialchars($course['turno']) ?></td>
                        <td class="text-center">
                            <a href='../forms/form_course.php?id_course=<?= $course['id_curso']; ?>' class="btn btn-sm btn-primary">Editar</a>

                            <form action='../processes/process_course.php' method='post' style='display:inline;' onsubmit="return confirm('Tem certeza que deseja excluir este curso? Alunos vinculados a ele impedirão a exclusão.');">
                                <input type='hidden' name='id_course' value='<?= $course['id_curso']; ?>'>
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