<?php
if (isset($_SESSION['id_user'])) {
    $id_usuario_nav = $_SESSION['id_user'];
    $query_nav = $conect->prepare("SELECT nome FROM usuario WHERE id_usuario = ?");
    $query_nav->bind_param("i", $id_usuario_nav);
    $query_nav->execute();
    $resultado_nav = $query_nav->get_result();

    $name_usuario_nav = "Usuário";

    if ($resultado_nav->num_rows === 1) {
        $user_data = $resultado_nav->fetch_assoc();
        $name_usuario_nav = $user_data['nome'];
    }
    $first_name_only = explode(' ', trim($name_usuario_nav))[0];

    $user_roles = [
        RULE_ESTUDANTE => 'Estudante',
        RULE_ORIENTADOR => 'Orientador',
        RULE_DIRECAO => 'Direção',
        RULE_FINANCEIRO => 'Financeiro',
        RULE_GERENTE => 'Gerente'
    ];

    $user_role_name = $user_roles[$_SESSION['type']] ?? 'Desconhecido';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= BASE_URL ?>/central.php">
            Portal de Bolsas | CCNE
            <?php if (isset($_SESSION['type'])): ?>
                <span class="badge bg-secondary"><?= htmlspecialchars($user_role_name) ?></span>
            <?php endif; ?>
        </a>
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
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/forms/form_user.php?id_user=<?= $_SESSION['id_user']; ?>">Editar Conta</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout.php">Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>