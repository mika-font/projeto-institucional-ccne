<?php
    include_once('./control.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <title>Portal de Bolsas CCNE</title>
</head>
<body>
    <!-- Cabeçalho com essas opções -->
    <a href="logout.php">Sair</a>
    <a href="./forms/form_user.php?id_user=<?= $_SESSION['id_user']; ?>">Editar conta</a>
    
    <?php if ($_SESSION['type'] == 0): ?> <!-- Links para Estudante -->
        <a href="./lists/list_bag.php">Bolsas Abertas</a>
        <a href="./lists/list_bag.php">Minhas Inscrições</a>
        <a href="./lists/list_bag.php">Minhas Bolsas</a>
    <?php elseif ($_SESSION['type'] == 1): ?> <!-- Links para Orientador -->
        <a href="./lists/list_bag.php">Minhas Bolsas</a>
    <?php elseif ($_SESSION['type'] == 2): ?> <!-- Links para Direção -->
        <a href="./forms/form_bag.php">Cadastrar Bolsa</a>
        <a href="./lists/list_bag.php">Listar Bolsas</a>
        <a href="./forms/form_subunit.php">Cadastrar Subunidade</a>
        <a href="./lists/list_subunit.php">Listar Subunidades</a>
        <a href="./forms/form_course.php">Cadastrar Curso</a>
        <a href="./lists/list_course.php">Listar Cursos</a>
    <?php elseif ($_SESSION['type'] == 3): ?> <!-- Links para Financeiro -->
        <a href="./lists/list_bag.php">Bolsas Pendentes</a>
    <?php elseif ($_SESSION['type'] == 4): ?> <!-- Links para Gerente -->
        <a href="./forms/form_user.php">Cadastrar Usuário</a>
        <a href="./lists/list_user.php">Listar Usuários</a>
        <a href="./forms/form_course.php">Cadastrar Curso</a>
        <a href="./lists/list_course.php">Listar Cursos</a>
        <a href="./forms/form_subunit.php">Cadastrar Subunidade</a>
        <a href="./lists/list_subunit.php">Listar Subunidades</a>
        <a href="./forms/form_bag.php">Cadastrar Bolsa</a>
        <a href="./lists/list_bag.php">Listar Bolsas</a> 
    <?php endif; ?>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>