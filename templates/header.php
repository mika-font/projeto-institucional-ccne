<?php
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
}
?>

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