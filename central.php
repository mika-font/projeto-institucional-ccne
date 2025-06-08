<?php
    include_once('./control.php');
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
    <a href="logout.php">Sair</a>
    <a href="./forms/form_user.php?id_user=<?= $_SESSION['id_user']; ?>">Editar conta</a>
    <?php if ($_SESSION['type'] == 4): ?>
        <a href="./lists/list_user.php">Listar Usuários</a>
    <?php endif; ?>
</body>
</html>