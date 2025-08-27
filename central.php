<?php
include_once(__DIR__ . '/control.php');

$welcome_message = "Bem-vindo(a)";
if (isset($_SESSION['id_user'])) {
    $user_id = $_SESSION['id_user'];
    $query = $conect->prepare("SELECT nome FROM usuario WHERE id_usuario = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    $name = "Usuário";

    if ($result->num_rows === 1) {
        $user_data = $result->fetch_assoc();
        $name = $user_data['nome'];
    }
    $first_name_only = explode(' ', trim($name))[0];
    $welcome_message = "Bem-vindo(a), " . htmlspecialchars($first_name_only) . "!";
}

$feedback_message = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'cadastro_sucesso':
            $feedback_message = 'Usuário cadastrado com sucesso!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/icons/faviconccne.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <title>Portal de Bolsas CCNE</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Portal de Bolsas | CCNE</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($first_name_only) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                            <li><a class="dropdown-item" href="./forms/form_user.php?id_user=<?= $_SESSION['id_user']; ?>">Editar Conta</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        <h1 class="mb-4"><?= $welcome_message ?></h1>

        <?php if (!empty($feedback_message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($feedback_message) ?></div>
        <?php endif; ?>

        <div class="row">
            <?php if ($_SESSION['type'] == RULE_ESTUDANTE): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Minhas Atividades</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Acompanhe suas inscrições e bolsas ativas.</p>
                            <a href="./lists/list_my_bags.php" class="btn btn-primary">Ver Minhas Inscrições</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Oportunidades</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Veja todas as bolsas com inscrições abertas.</p>
                            <a href="./lists/list_open_bags.php" class="btn btn-primary">Ver Bolsas Abertas</a>
                        </div>
                    </div>
                </div>

            <?php elseif ($_SESSION['type'] == RULE_ORIENTADOR): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Gerenciamento</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Gerencie as bolsas pelas quais você é responsável.</p>
                            <a href="./lists/list_my_bags.php" class="btn btn-primary">Ver Minhas Bolsas</a>
                        </div>
                    </div>
                </div>

            <?php elseif ($_SESSION['type'] == RULE_FINANCEIRO): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>Pendências</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Visualize as bolsas que requerem sua ação.</p>
                            <a href="./lists/list_my_bags.php" class="btn btn-primary">Ver Bolsas Pendentes</a>
                        </div>
                    </div>
                </div>

            <?php elseif (in_array($_SESSION['type'], [RULE_DIRECAO, RULE_GERENTE])): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4>Gerenciar Bolsas</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Crie novas bolsas e administre as existentes.</p>
                            <a href="./lists/list_bag.php" class="btn btn-primary mb-2 w-100">Listar Todas as Bolsas</a>
                            <a href="./forms/form_bag.php" class="btn btn-primary w-100">Cadastrar Nova Bolsa</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4>Gerenciar Estrutura</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Administre cursos e subunidades do sistema.</p>
                            <a href="./lists/list_course.php" class="btn btn-primary mb-2 w-100">Listar Cursos</a>
                            <a href="./lists/list_subunit.php" class="btn btn-primary w-100">Listar Subunidades</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($_SESSION['type'] == RULE_GERENTE): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4>Gerenciar Usuários</h4>
                        </div>
                        <div class="card-body">
                            <p class="card-text justify">Crie novos usuários e administre os existentes.</p>
                            <a href="./lists/list_user.php" class="btn btn-primary mb-2 w-100">Listar Todos os Usuários</a>
                            <a href="./forms/form_user.php" class="btn btn-primary w-100">Cadastrar Novo Usuário</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>