<?php
    include_once('../control.php');
    // Funções do listar minhas bolsas para Estudante, Orientador e Financeiro:
    // Listar todas as bolsas em que o estudante está inscrito
    // Listar todas as bolsas em que o orientador é responsável
    // Listar todas as bolsas em que o estudante é beneficiário
    // Listar todas as bolsas em que o financeiro possui pendências

if (!isset($_SESSION['type']) || !in_array($_SESSION['type'], [0, 1, 3])) {
    header("Location: " . BASE_URL . "/central.php?msg=10"); // Acesso não autorizado
    exit();
}

$user_type = $_SESSION['type'];
$user_id = $_SESSION['id_user'];
$page_title = "";
$result = null;

// Preparar a consulta e o título da página para cada perfil
switch ($user_type) {
    // Caso 1: ORIENTADOR - Listar bolsas pelas quais ele é responsável
    case 1:
        $page_title = "Minhas Bolsas como Orientador";
        $query = $conect->prepare(
            "SELECT b.*, s.nome AS nome_subunidade 
             FROM bolsa AS b 
             LEFT JOIN subunidade AS s ON b.id_subunidade = s.id_subunidade
             WHERE b.id_orientador = ?"
        );
        $query->bind_param("i", $user_id);
        break;

    // Caso 0: ESTUDANTE - Listar bolsas nas quais ele se inscreveu
    case 0:
        $page_title = "Minhas Inscrições em Bolsas";
        $query = $conect->prepare(
            "SELECT b.id_bolsa, b.nome, b.situacao AS situacao_bolsa, c.data_hora, c.situacao AS situacao_inscricao
             FROM bolsa AS b
             JOIN candidato AS c ON b.id_bolsa = c.id_bolsa
             WHERE c.id_user = ?"
        );
        $query->bind_param("i", $user_id);
        break;

    // Caso 3: FINANCEIRO - Listar bolsas com pendências financeiras
    case 3:
        $page_title = "Bolsas com Pendências Financeiras";
        $status_pendencia = "Inativar";
        $query = $conect->prepare("SELECT * FROM ccne_bd_bolsa WHERE situacao = ?");
        $query->bind_param("s", $status_pendencia);
        break;
}

// Executa a consulta preparada
if (isset($query)) {
    $query->execute();
    $result = $query->get_result();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <title><?= $page_title ?> - Portal de Bolsas CCNE</title>
</head>
<body>
    <header>
        <h1><?= $page_title ?></h1>
    </header>
    <main>
        <table border="1" style="width:100%; border-collapse: collapse;">
            
            <thead>
                <?php if ($user_type == 1): // Orientador ?>
                    <tr>
                        <th>Nome da Bolsa</th>
                        <th>Situação da Bolsa</th>
                        <th>Ações</th>
                    </tr>
                <?php elseif ($user_type == 0): // Estudante ?>
                    <tr>
                        <th>Nome da Bolsa</th>
                        <th>Data da Inscrição</th>
                        <th>Situação da Inscrição</th>
                        <th>Ações</th>
                    </tr>
                <?php elseif ($user_type == 3): // Financeiro ?>
                    <tr>
                        <th>Nome da Bolsa</th>
                        <th>Código</th>
                        <th>Ações</th>
                    </tr>
                <?php endif; ?>
            </thead>

            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($bag = $result->fetch_assoc()): ?>
                        <tr>
                            <?php if ($user_type == 1): // Orientador ?>
                                <td><?= htmlspecialchars($bag['nome']) ?></td>
                                <td><?= htmlspecialchars($bag['situacao']) ?></td>
                                <td><a href="list_candidatos.php?id_bolsa=<?= $bag['id_bolsa'] ?>">Ver Candidatos</a></td>
                            <?php elseif ($user_type == 0): // Estudante ?>
                                <td><?= htmlspecialchars($bag['nome']) ?></td>
                                <td><?= (new DateTime($bag['data_hora']))->format('d/m/Y H:i') ?></td>
                                <td><?= htmlspecialchars($bag['situacao_inscricao']) ?></td>
                                <td><a href="../details/detalhes_bolsa.php?id_bolsa=<?= $bag['id_bolsa'] ?>">Ver Detalhes da Bolsa</a></td>
                            <?php elseif ($user_type == 3): // Financeiro ?>
                                <td><?= htmlspecialchars($bag['nome']) ?></td>
                                <td><?= htmlspecialchars($bag['codigo']) ?></td>
                                <td><a href="aprovar_pagamento.php?id_bolsa=<?= $bag['id_bolsa'] ?>">Analisar e Aprovar</a></td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">Nenhum item encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>