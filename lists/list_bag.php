<?php
    include_once('../control.php');

    if(isset($_SESSION['type']) && $_SESSION['type'] == 4){
        $query = $conect->prepare("SELECT * FROM bolsa");
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
        <td>Bolsa</td>
        <td>Código</td>
        <td>Descrição</td>
        <td>Carga Horária</td>
        <td>Modalidade</td>
        <td>Situação</td>
        <td>Link Edital</td>
        <td>Opções</td>
    </tr>
    <br>
    <?php while ($bag = $result->fetch_assoc()): ?>
        <tr>
            <td> <?= $bag['id_bolsa'] ?> </td>
            <td> <?= htmlspecialchars($bag['nome']) ?> </td>
            <td> <?= htmlspecialchars($bag['codigo']) ?> </td>
            <td> <?= htmlspecialchars($bag['descricao']) ?> </td>
            <td> <?= htmlspecialchars($bag['limite_ch']) ?> </td>
            <td> <?= htmlspecialchars($bag['modalidade']) ?> </td>
            <td>
                <?php switch ($bag['situacao']) : 
                    case "Aberta para Inscrições":  echo "Aberta para Inscrições"; break;
                    case "Em Seleção":              echo "Em Seleção"; break; 
                    case "Ativo":                   echo "Ativo"; break; 
                    case "Inativar":                echo "Inativar"; break; 
                    case "Inativo":                 echo "Inativo"; break;
                endswitch;
                ?> 
            </td>
            <td> <a href='<?= htmlspecialchars($bag['arquivo']) ?>'>Edital</td>
            <td>
                <a href='../forms/form_bag.php?id_bag=<?= $bag['id_bolsa']; ?>'>Editar Bolsa</a>
                <form action='../processes/process_bag.php' method='post' style='display:inline;'>
                    <input type='hidden' name='id_bag' value='<?= $bag['id_bolsa']; ?>'>
                    <button type='submit' name='delete'>Excluir</button>
                </form>
            </td>
        </tr>
        <br>
    <?php endwhile; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
