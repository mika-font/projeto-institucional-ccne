<?php
    include_once('../control.php'); 

    function list_all_bags($conect){
        $query = $conect->prepare("SELECT b.*, s.nome AS nome_subunidade FROM bolsa AS b LEFT JOIN subunidade AS s ON b.id_subunidade = s.id_subunidade");
        $query->execute();
        return $query->get_result();
    }

    function list_bags_by_subunit($conect, $subunit_id){
        $query = $conect->prepare("SELECT b.*, s.nome AS nome_subunidade FROM bolsa AS b LEFT JOIN subunidade AS s ON b.id_subunidade = s.id_subunidade WHERE b.id_subunidade = ?");
        $query->bind_param("i", $subunit_id);
        $query->execute();
        return $query->get_result();
    }

    function list_bags_by_status($conect, $situation){
        $query = $conect->prepare("SELECT b.*, s.nome AS nome_subunidade FROM bolsa AS b LEFT JOIN subunidade AS s ON b.id_subunidade = s.id_subunidade WHERE b.situa$situation = ?");
        $query->bind_param("s", $situation);
        $query->execute();
        return $query->get_result();
    }

    if(isset($_SESSION['type']) && in_array($_SESSION['type'], [4, 2])) {
        
        $subunit_filter = $_GET['subunit_filter'] ?? null;
        $situation_filter = $_GET['situation_filter'] ?? null;
        $result = null;

        // Decide qual função chamar com base no filtro aplicado
        if (!empty($subunit_filter)) {
            $result = list_bags_by_subunit($conect, $subunit_filter);
        } else if (!empty($situation_filter)) {
            $result = list_bags_by_status($conect, $situation_filter);
        } else {
            $result = list_all_bags($conect);
        }

        // Busca dados para preencher os menus de filtro
        $subunits_result = $conect->query("SELECT id_subunidade, nome FROM subunidade ORDER BY nome ASC");
        $situations_list = ['Aberta para Inscrições', 'Em Seleção', 'Ativo', 'Inativar', 'Inativo'];

    } else {
        header('Location: ' . BASE_URL . '/central.php?msg=10'); // não autorizado
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
    <header>
        <h1>Listagem de Bolsas</h1>
    </header>
    <main>
        <form action="<?php $_SERVER['PHP_SELF']?>" method="get">
            <label for="subunit_filter">Filtrar por Subunidade:</label>
            <select name="subunit_filter" id="subunit_filter">
                <option value="">-- Todas --</option>
                <?php
                    if ($subunits_result->num_rows > 0) {
                        while ($sub = $subunits_result->fetch_assoc()) {
                            $selected = ($_GET['subunit_filter'] ?? '') == $sub['id_subunidade'] ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($sub['id_subunidade']) . "' $selected>" . htmlspecialchars($sub['nome']) . "</option>";
                        }
                    }
                ?>
            </select>

            <label for="situation_filter">Filtrar por Situação:</label>
            <select name="situation_filter" id="situation_filter">
                <option value="">-- Todas --</option>
                <?php
                    foreach ($situations_list as $situation) {
                        $selected = ($_GET['situation_filter'] ?? '') == $situation ? 'selected' : '';
                        echo "<option value='" . htmlspecialchars($situation) . "' $selected>" . htmlspecialchars($situation) . "</option>";
                    }
                ?>
            </select>
            <button type="submit">Filtrar</button>
            <a href="list_bag.php">Limpar Filtros</a>
        </form>

        <hr>

        <table border="1">
            <thead>
                <tr>
                    <th>Bolsa</th>
                    <th>Subunidade</th>
                    <th>Carga Horária</th>
                    <th>Situação</th>
                    <th>Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($bag = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($bag['nome']) ?></td>
                            <td><?= htmlspecialchars($bag['nome_subunidade']) ?></td>
                            <td><?= htmlspecialchars($bag['limite_ch']) ?>h</td>
                            <td><?= htmlspecialchars($bag['situacao']) ?></td>
                            <td>
                                <a href="../forms/form_bag.php?id_bolsa=<?= $bag['id_bolsa']; ?>">Editar</a>
                                <form action='../processes/process_bag.php' method='post' style='display:inline;' onsubmit="return confirm('Tem certeza que deseja excluir?');">
                                    <input type='hidden' name='id_bolsa' value='<?= $bag['id_bolsa']; ?>'>
                                    <button type='submit' name='delete'>Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhuma bolsa encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>