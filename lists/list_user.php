<?php
include_once('./control.php');
if(isset($_SESSION['type']) && $_SESSION['type'] == 4){
    // listar usuários
    $query = $conect->prepare("SELECT * FROM usuario");
    if(!$query){
        echo "Erro na preparação: " . $conect->error;
        die();
    }

    $query->execute();
    $result = $query->get_result();

    if(!$result){
        echo "Erro na execução: " . $conect->error;
        die();
    }
} else {
    header('Location: central.php?msg=não autorizado');
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <title>Portal de Bolsas do CCNE</title>
</head>
<body>
    
</body>
</html>
<?php while ($usuario = $result->fetch_assoc()): ?>
    <tr>
        <td> <?php $usuario['id_usuario'] ?> </td>
        <td> <?php $usuario['nome'] ?> </td>
        <td> <?php $usuario['email'] ?> </td>
        <td>
            <a href='editar_user.php?id= <?php $usuario['id_usuario'] ?>'>Editar</a>
            <form action='excluir_user.php' method='post' style='display:inline;'>
                <input type='hidden' name='id_usuario' value='<?php $usuario['id_usuario'] ?>'>
                <button type='submit' name='excluir'>Excluir</button>
            </form>
        </td>
    </tr>
<?php endwhile; ?>
