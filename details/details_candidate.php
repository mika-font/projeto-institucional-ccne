<?php
include_once(__DIR__ . '/../configs/rules.php');
include_once(__DIR__ . '/../control.php');

if (!isset($_SESSION['type']) || !in_array($_SESSION['type'], [RULE_ORIENTADOR, RULE_DIRECAO, RULE_GERENTE])) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$id_bag = intval($_GET['id_bolsa'] ?? 0);
$id_student = intval($_GET['id_estudante'] ?? 0);

if (empty($id_bag) || empty($id_student)) {
    header("Location: " . BASE_URL . "/lists/list_candidates.php?msg=dados_insuficientes");
    exit();
}

$query = $conect->prepare(
    "SELECT u.nome, u.email, 
            d.matricula, d.telefone, d.cod_banco, d.agencia, d.conta,
            c.nome AS nome_curso, 
            i.disponibilidade, 
            b.situacao AS situacao_bolsa
    FROM usuario u
    LEFT JOIN dados_estudante d ON u.id_usuario = d.id_usuario
    LEFT JOIN curso c ON d.id_curso = c.id_curso
    LEFT JOIN inscricao i ON u.id_usuario = i.id_estudante
    LEFT JOIN bolsa b ON i.id_bolsa = b.id_bolsa
    WHERE u.id_usuario = ? AND i.id_bolsa = ?"
);
$query->bind_param("ii", $id_student, $id_bag);
$query->execute();
$result = $query->get_result();
if ($result->num_rows !== 1) {
    die("Candidato ou inscrição não encontrado.");
}
$candidate = $result->fetch_assoc();

$workload_json = $candidate['disponibilidade'];
$selected_hours = json_decode($workload_json, true) ?? [];
// Para otimizar a busca, o array é convertido em um formato de "lookup table"
// A chave será o horário (ex: 'segunda_8') e o valor será 'true'
$lookup_workload = array_flip($selected_hours);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <title>Portal de Bolsas CCNE</title>
</head>

<div class="container mt-5 mb-5">
    <h2 class="mb-4">Perfil do Candidato: <?= htmlspecialchars($candidate['nome']) ?></h2>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dados Pessoais e Acadêmicos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nome:</strong> <?= htmlspecialchars($candidate['nome']) ?></p>
                            <p><strong>E-mail:</strong> <?= htmlspecialchars($candidate['email']) ?></p>
                            <p><strong>Telefone:</strong> <?= htmlspecialchars($candidate['telefone'] ?? 'Não informado') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Curso:</strong> <?= htmlspecialchars($candidate['nome_curso'] ?? 'Não informado') ?></p>
                            <p><strong>Matrícula:</strong> <?= htmlspecialchars($candidate['matricula'] ?? 'Não informado') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Dados Bancários</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Cód. Banco:</strong> <?= htmlspecialchars($candidate['cod_banco'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Agência:</strong> <?= htmlspecialchars($candidate['agencia'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Conta:</strong> <?= htmlspecialchars($candidate['conta'] ?? 'N/A') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Disponibilidade de Horários</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="table-light">
                                <tr>
                                    <th rowspan="2" class="align-middle">Dia</th>
                                    <th colspan="5">Manhã</th>
                                    <th colspan="5">Tarde</th>
                                    <th colspan="5">Noite</th>
                                </tr>
                                <tr>
                                    <?php for ($i = 8; $i <= 22; $i++): ?>
                                        <?php if ($i == 13) continue; ?>
                                        <th><?= $i ?>h</th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $dias_semana = ['Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira'];
                                $dias_semana_valor = ['segunda', 'terca', 'quarta', 'quinta', 'sexta'];
                                ?>
                                <?php foreach ($dias_semana_valor as $index => $dia_valor): ?>
                                    <tr>
                                        <td><strong><?= $dias_semana[$index] ?></strong></td>
                                        <?php for ($i = 8; $i <= 22; $i++): ?>
                                            <?php if ($i == 13) continue; ?>
                                            <?php
                                            $chave_horario = $dia_valor . '_' . $i;
                                            $is_disponivel = isset($lookup_workload[$chave_horario]);
                                            ?>
                                            <td class="<?= $is_disponivel ? 'bg-success text-white' : 'bg-light text-muted' ?>">
                                                <?= $is_disponivel ? 'Sim' : '-' ?>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>Ações</h4>
                </div>
                <div class="card-body">
                    <?php if ($candidate['situacao_bolsa'] == 'Em Seleção'): ?>
                        <form action="../processes/process_selection.php" method="post" onsubmit="return confirm('Tem certeza que deseja selecionar este candidato? Esta ação é irreversível.');">
                            <input type="hidden" name="id_bolsa" value="<?= $id_bag ?>">
                            <input type="hidden" name="id_estudante" value="<?= $id_student ?>">
                            <button type="submit" name="selection" class="btn btn-success w-100">Selecionar Candidato</button>
                        </form>
                    <?php else: ?>
                        <p class="text-muted">A seleção não está disponível para o status atual desta bolsa ("<?= htmlspecialchars($candidate['situacao_bolsa']) ?>").</p>
                    <?php endif; ?>
                    <a href="javascript:history.back()" class="btn btn-secondary w-100 mt-2">Voltar</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>

</html>