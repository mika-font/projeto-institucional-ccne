<?php
include_once(__DIR__ . '/../control.php');

if ($_SESSION['type'] != RULE_ESTUDANTE) {
    header("Location: " . BASE_URL . "/central.php?msg=nao_autorizado");
    exit();
}

$id_bag = intval($_GET['id_bag'] ?? 0);
$user_id = $_SESSION['id_user'];

// Verifica se a bolsa está disponível para inscrições
$query_bolsa = $conect->prepare("SELECT nome, situacao, carga_horaria FROM bolsa WHERE id_bolsa = ?");
$query_bolsa->bind_param("i", $id_bag);
$query_bolsa->execute();
$result_bolsa = $query_bolsa->get_result();
if ($result_bolsa->num_rows !== 1) {
    header("Location: " . BASE_URL . "/lists/list_my_bags.php?msg=bolsa_nao_encontrada");
    exit();
}
$bag = $result_bolsa->fetch_assoc();
if ($bag['situacao'] !== 'Aberta para Inscrições') {
    header("Location: " . BASE_URL . "/details/details_bag.php?id_bag=$id_bag&msg=inscricoes_fechadas");
    exit();
}

$workload = $bag['carga_horaria'];

// Verifica se o estudante já possui dados cadastrais
$student_data = null;
$has_student_data = false;
$query_dados = $conect->prepare("SELECT * FROM dados_estudante WHERE id_usuario = ?");
$query_dados->bind_param("i", $user_id);
$query_dados->execute();
$result_dados = $query_dados->get_result();
if ($result_dados->num_rows === 1) {
    $student_data = $result_dados->fetch_assoc();
    $has_student_data = true;
}

$matricula = $student_data['matricula'] ?? '';
$id_curso = $student_data['id_curso'] ?? '';
$telefone = $student_data['telefone'] ?? '';
$cod_banco = $student_data['cod_banco'] ?? '';
$agencia = $student_data['agencia'] ?? '';
$conta = $student_data['conta'] ?? '';

// Busca a lista de cursos
$cursos_result = $conect->query("SELECT id_curso, nome FROM curso ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/basic.css">
    <title>Portal de Bolsas | CCNE</title>
</head>

<body>

    <?php include_once(__DIR__ . '/../templates/header.php'); ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3>Inscrição para a Bolsa: "<?= htmlspecialchars($bag['nome']) ?>"</h3>
                    </div>
                    <div class="card-body">
                        <form action="../processes/process_application.php" method="post">
                            <input type="hidden" name="id_bag" value="<?= $id_bag ?>">
                            <input type="hidden" name="id_user" value="<?= $user_id ?>">

                            <h4>Passo 1: Dados Cadastrais</h4>
                            <p class="text-muted">
                                <?php if ($has_student_data): ?>
                                    Seus dados já estão salvos. Por favor, verifique se estão corretos e atualize se necessário.
                                <?php else: ?>
                                    Como esta é sua primeira inscrição, por favor, preencha seus dados cadastrais. Eles ficarão salvos para futuras inscrições.
                                <?php endif; ?>
                            </p>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="matricula" class="form-label">Matrícula:</label>
                                    <input type="text" class="form-control" id="matricula" name="matricula" value="<?= htmlspecialchars($matricula) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="id_curso" class="form-label">Curso:</label>
                                    <select class="form-select" id="id_curso" name="id_curso" required>
                                        <option value="">Selecione seu curso...</option>
                                        <?php while ($curso = $cursos_result->fetch_assoc()): ?>
                                            <option value="<?= $curso['id_curso'] ?>" <?= ($id_curso == $curso['id_curso']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($curso['nome']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="telefone" class="form-label">Telefone:</label>
                                    <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($telefone) ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <h6>Dados bancários:</h6>
                                <div class="col-md-4 mb-3">
                                    <label for="cod_banco" class="form-label">Código do Banco:</label>
                                    <input type="number" class="form-control" id="cod_banco" name="cod_banco" value="<?= htmlspecialchars($cod_banco) ?>" placeholder="Ex: 123" maxlength="4" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="agencia" class="form-label">Agência:</label>
                                    <input type="text" class="form-control" id="agencia" name="agencia" value="<?= htmlspecialchars($agencia) ?>" placeholder="Ex: 4567-5" maxlength="10" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="conta" class="form-label">Conta:</label>
                                    <input type="text" class="form-control" id="conta" name="conta" value="<?= htmlspecialchars($conta) ?>" placeholder="Ex: 8910" maxlength="20" required>
                                </div>
                            </div>
                            <hr>
                            <h4>Passo 2: Disponibilidade de Horários</h4>
                            <p class="text-muted">Marque os horários em que você tem disponibilidade para a bolsa.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center schedule-table">
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
                                        <?php foreach ($dias_semana as $index => $dia): ?>
                                            <tr>
                                                <td><?= $dia ?></td>
                                                <?php for ($i = 8; $i <= 22; $i++): ?>
                                                    <?php if ($i == 13) continue; ?>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center">
                                                            <input class="form-check-input hour-checkbox" type="checkbox" name="horarios[]" value="<?= $dias_semana_valor[$index] ?>_<?= $i ?>" id="ch_<?= $dias_semana_valor[$index] ?>_<?= $i ?>">
                                                        </div>
                                                    </td>
                                                <?php endfor; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="text-end mt-2">
                                <strong>Horas selecionadas:
                                    <span id="hourCounter" class="fw-bold">0</span> / <?= (int)$workload ?>
                                </strong>
                            </div>

                            <hr>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" id="confirmacao" required>
                                <label class="form-check-label" for="confirmacao">Declaro que todas as informações prestadas são verdadeiras.</label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="javascript:history.back()" class="btn btn-secondary">Voltar</a>
                                <button type="submit" name="application" class="btn btn-success" disabled>Confirmar Inscrição</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once(__DIR__ . '/../templates/footer.php'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleCheckboxes = document.querySelectorAll('tbody .form-check-input');
            const confirmationCheckbox = document.getElementById('confirmacao');
            const hourCounterSpan = document.getElementById('hourCounter');
            const submitButton = document.querySelector('button[name="application"]');

            // Injeta a carga horária do PHP para o JavaScript
            const requiredHours = <?= (int)($workload ?? 0); ?>;

            function updateFormState() {
                // Conta os checkboxes de horário marcados
                const selectedCount = document.querySelectorAll('tbody .form-check-input:checked').length;

                // Atualiza o contador visual
                hourCounterSpan.textContent = selectedCount;

                // Verifica as duas condições para o envio
                const hoursAreCorrect = (selectedCount === requiredHours);
                const isConfirmed = confirmationCheckbox.checked;

                // O botão só é habilitado se ambas as condições forem verdadeiras
                if (hoursAreCorrect && isConfirmed) {
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }

                // Lógica para o visual do contador e para bloquear mais seleções
                if (hoursAreCorrect) {
                    hourCounterSpan.classList.remove('text-danger');
                    hourCounterSpan.classList.add('text-success');

                    // Desabilita os checkboxes de horário não marcados
                    scheduleCheckboxes.forEach(checkbox => {
                        if (!checkbox.checked) {
                            checkbox.disabled = true;
                        }
                    });
                } else {
                    hourCounterSpan.classList.remove('text-success');

                    if (selectedCount > requiredHours) {
                        hourCounterSpan.classList.add('text-danger');
                    } else {
                        hourCounterSpan.classList.remove('text-danger');
                    }

                    // Habilita todos os checkboxes de horário
                    scheduleCheckboxes.forEach(checkbox => {
                        checkbox.disabled = false;
                    });
                }
            }

            // Um checkbox de horário for alterado
            scheduleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateFormState);
            });

            // O checkbox de confirmação for alterado
            confirmationCheckbox.addEventListener('change', updateFormState);

            // Roda a validação uma vez no carregamento da página para definir o estado inicial
            updateFormState();
        });
    </script>
</body>

</html>