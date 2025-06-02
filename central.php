<?php
echo ("Hello");
include_once('controle.php');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Bolsas CCNE</title>
</head>
<body>
    <a href="logout.php">Sair</a>
    <a href="form_user?id_usuario=<?= $_SESSION['id_usuario']; ?>"></a>
</body>
</html>