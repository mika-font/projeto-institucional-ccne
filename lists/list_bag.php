<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || !in_array($_SESSION['type'], [RULE_GERENTE, RULE_DIRECAO])) {
    header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
    exit();
}

$subunit_filter = $_GET['subunit_filter'] ?? null;
$situation_filter = $_GET['situation_filter'] ?? null;

// Base da consulta SQL com JOINS para buscar nomes
$sql = "SELECT 
            b.id_bolsa, 
            b.nome, 
            b.carga_horaria, 
            b.situacao,
            s_origem.nome AS nome_subunidade_origem,
            s_alocacao.nome AS nome_subunidade_alocacao,
            o.nome AS nome_orientador
        FROM bolsa AS b 
        LEFT JOIN subunidade AS s_origem ON b.id_sub_origem = s_origem.id_subunidade
        LEFT JOIN subunidade AS s_alocacao ON b.id_sub_alocacao = s_alocacao.id_subunidade
        LEFT JOIN usuario AS o ON b.id_orientador = o.id_usuario";

$where_clauses = [];
$params = [];
$types = "";

if (!empty($subunit_filter)) {
    $where_clauses[] = "b.id_sub_origem = ?";
    $params[] = $subunit_filter;
    $types .= "i";
}

if (!empty($situation_filter)) {
    $where_clauses[] = "b.situacao = ?";
    $params[] = $situation_filter;
    $types .= "s";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY b.nome ASC";

$query = $conect->prepare($sql);

if (!empty($params)) {
    $query->bind_param($types, ...$params);
}

$query->execute();
$result = $query->get_result();

$subunits_result = $conect->query("SELECT id_subunidade, nome FROM subunidade ORDER BY nome ASC");
// Lista de situações possíveis
$situations_list = [
    'Aberta para Inscrições',
    'Em Seleção',
    'Aguardando Documentação',
    'Pendente de Ativação (Financeiro)',
    'Vigente',
    'Pendente de Desativação (Financeiro)',
    'Encerrada',
    'Cancelada'
];

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
    <div class="container mt-4">
        <h1 class="mb-4">Gerenciamento de Bolsas</h1>
        <div class="d-flex justify-content-between mb-3">
            <p>Listagem de todas as bolsas do sistema.</p>
            <a href="../forms/form_bag.php" class="btn btn-success">Adicionar Nova Bolsa</a>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">Filtros de Busca</div>
            <div class="card-body">
                <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="get" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="subunit_filter" class="form-label">Filtrar por Subunidade:</label>
                        <select name="subunit_filter" id="subunit_filter" class="form-select">
                            <option value="">-- Todas --</option>
                            <?php while ($sub = $subunits_result->fetch_assoc()): ?>
                                <option value="<?= $sub['id_subunidade'] ?>" <?= ($subunit_filter == $sub['id_subunidade']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sub['nome']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="situation_filter" class="form-label">Filtrar por Situação:</label>
                        <select name="situation_filter" id="situation_filter" class="form-select">
                            <option value="">-- Todas --</option>
                            <?php foreach ($situations_list as $situation): ?>
                                <option value="<?= $situation ?>" <?= ($situation_filter == $situation) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($situation) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                        <a href="list_bag.php" class="btn btn-secondary w-100 mt-2">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-striped table-hover table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Bolsa</th>
                    <th>Subunidade de Origem</th>
                    <th>Subunidade de Alocação</th>
                    <th>Orientador</th>
                    <th>C.H.</th>
                    <th>Situação</th>
                    <th class="text-center">Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($bag = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($bag['nome']) ?></td>
                            <td><?= htmlspecialchars($bag['nome_subunidade_origem']) ?></td>
                            <td><?= htmlspecialchars($bag['nome_subunidade_alocacao']) ?></td>
                            <td><?= htmlspecialchars($bag['nome_orientador']) ?></td>
                            <td><?= htmlspecialchars($bag['carga_horaria']) ?>h</td>
                            <td><?= htmlspecialchars($bag['situacao']) ?></td>
                            <td class="text-center">
                                <a href="../forms/form_bag.php?id_bag=<?= $bag['id_bolsa']; ?>" class="btn btn-sm btn-primary">Editar</a>
                                <form action='../processes/process_bag.php' method='post' class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta bolsa?');">
                                    <input type='hidden' name='id_bag' value='<?= $bag['id_bolsa']; ?>'>
                                    <button type='submit' name='delete' class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhuma bolsa encontrada com os filtros aplicados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="../central.php" class="btn btn-secondary">Voltar</a>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>