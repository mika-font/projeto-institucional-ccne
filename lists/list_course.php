<?php
    include_once('../control.php');

    if(isset($_SESSION['type']) && $_SESSION['type'] == 4){
        $query = $conect->prepare("SELECT * FROM curso");
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
        <td>Curso</td>
        <td>Código</td>
        <td>Campus</td>
        <td>Turno</td>
        <td>Opções</td>
    </tr>
    <br>
    <?php while ($course = $result->fetch_assoc()): ?>
        <tr>
            <td> <?= $course['id_curso'] ?> </td>
            <td> <?= htmlspecialchars($course['nome']) ?> </td>
            <td> <?= htmlspecialchars($course['codigo']) ?> </td>
            <td>
                <?php switch ($course['campus']) : 
                    case "Santa Maria":          echo "Santa Maria"; break;
                    case "Frederico Westphalen": echo "Frederico Westphalen"; break; 
                    case "Cachoeira do Sul":     echo "Cachoeira do Sul"; break; 
                    case "Palmeira das Missões": echo "Palmeira das Missões"; break; 
                endswitch;
                ?> 
            </td>
            <td>
                <?php switch ($course['turno']) : 
                    case "Matutino":   echo "Matutino"; break;
                    case "Vespertino": echo "Vespertino"; break; 
                    case "Noturno":    echo "Noturno"; break; 
                    case "Diurno":     echo "Diurno"; break; 
                endswitch;
                ?> 
            </td>
            <td>
                <a href='../forms/form_course.php?id_course=<?= $course['id_curso']; ?>'>Editar Curso</a>
                <form action='../processes/process_course.php' method='post' style='display:inline;'>
                    <input type='hidden' name='id_course' value='<?= $course['id_curso']; ?>'>
                    <button type='submit' name='delete'>Excluir</button>
                </form>
            </td>
        </tr>
        <br>
    <?php endwhile; ?>
</body>
</html>
