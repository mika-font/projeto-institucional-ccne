<?php
    include_once('../control.php');

    if(isset($_SESSION['type']) && $_SESSION['type'] == 4){
        $query = $conect->prepare("SELECT * FROM subunidade");
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
    <title>Portal de Bolsas do CCNE</title>
</head>
<body>
    <tr>
        <td>Id</td>
        <td>Subunidade</td>
        <td>Código</td>
        <td>Opções</td>
    </tr>
    <br>
    <?php while ($sub = $result->fetch_assoc()): ?>
        <tr>
            <td> <?= $sub['id_subunidade'] ?> </td>
            <td> <?= htmlspecialchars($sub['nome']) ?> </td>
            <td> <?= htmlspecialchars($sub['codigo']) ?> </td>
            <td>
                <a href='../forms/form_subunit.php?id_sub=<?= $sub['id_subunidade']; ?>'>Editar Curso</a>
                <form action='../processes/process_subunit.php' method='post' style='display:inline;'>
                    <input type='hidden' name='id_sub' value='<?= $sub['id_subunidade']; ?>'>
                    <button type='submit' name='delete'>Excluir</button>
                </form>
            </td>
        </tr>
        <br>
    <?php endwhile; ?>
</body>
</html>
