<?php
include_once('../control.php');

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
    header('Location: ../central.php?msg=não autorizado');
    exit();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Portal de Bolsas do CCNE</title>
</head>
<body>
    <tr>
        <td>Id</td>
        <td>Nome</td>
        <td>Email</td>
        <td>Tipo</td>
        <td>Opções</td>
    </tr>
    <br>
    <?php while ($user = $result->fetch_assoc()): ?>
        <tr>
            <td> <?= $user['id_user'] ?> </td>
            <td> <?= htmlspecialchars($user['nome']) ?> </td>
            <td> <?= htmlspecialchars($user['email']) ?> </td>
            <td>
                <?php switch ($user['tipo']) : 
                    case 0: echo "Estudante"; break;
                    case 1: echo "Orientador"; break; 
                    case 2: echo "Direção"; break; 
                    case 3: echo "Financeiro"; break; 
                    case 4: echo "Gerente Master"; break; 
                endswitch;
                ?> 
            </td>
            <td>
                <a href='../forms/form_user.php?id_user=<?= $user['id_user']; ?>'>Editar Usuário</a>
                <form action='../processes/process_user.php' method='post' style='display:inline;'>
                    <input type='hidden' name='id_user' value='<?= $user['id_user']; ?>'>
                    <button type='submit' name='delete'>Excluir</button>
                </form>
            </td>
        </tr>
        <br>
    <?php endwhile; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
