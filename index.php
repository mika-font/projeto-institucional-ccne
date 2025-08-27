<?php
include_once(__DIR__ . '/configs/rules.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['login']) && !empty($_POST['email']) && !empty($_POST['password'])) {
    include_once(__DIR__ . '/conect.php');
    $conect = conectServer();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $command = $conect->prepare("SELECT * FROM usuario WHERE email = ?");
    $command->bind_param("s", $email);
    $command->execute();
    $consult = $command->get_result();

    if ($consult->num_rows != 1) {
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?msg=credenciais_invalidas');
        exit();
    } else {
        $user_data = $consult->fetch_assoc();
        if (password_verify($password, $user_data['senha'])) {
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['id_user'] = $user_data['id_usuario'];
            $_SESSION['type'] = $user_data['tipo'];
            $_SESSION['user_name'] = $user_data['nome'];
            $_SESSION['last_access'] = time();
            session_regenerate_id(true);

            header('Location: ' . BASE_URL . '/central.php');
            exit();
        } else {
            session_destroy();
            header('Location: ' . BASE_URL . '/index.php?msg=credenciais_invalidas');
            exit();
        }
    }
} else if (isset($_SESSION['email']) && isset($_SESSION['id_user']) && isset($_SESSION['type'])) {
    header('Location: ' . BASE_URL . 'central.php');
    exit();
}

$error_message = '';
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'credenciais_invalidas':
            $error_message = 'E-mail ou senha incorretos.';
            break;
        case 'nao_autorizado':
            $error_message = 'Você não está autorizado a acessar esta página.';
            break;
        case 'timeout':
            $error_message = 'Sua sessão expirou. Por favor, faça login novamente.';
            break;
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
    <link rel="stylesheet" href="./assets/css/custom.css">
    <title>Portal de Bolsas CCNE</title>
</head>

<body class="login-page-background">
    <div class="background-image-container">
        <img src="./assets/icons/ufsm-aerea.jpg" alt="Fundo CCNE Suave" class="background-image">
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark main-header">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="./assets/icons/brasao_ufsm.png" alt="Brasão UFSM" class="header-logo me-3">
                <span>Portal de Bolsas | CCNE</span>
            </a>
        </div>
    </nav>

    <main class="container flex-grow">
        <div class="row align-items-center h-100 mt-5 mb-2">

            <div class="col-md-7 d-none d-md-block text-center">
            </div>

            <div class="col-md-5 align-items-center">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-center align-items-center mb-4">
                            <img src="./assets/icons/CCNE_horizontal.png" alt="Brasão CCNE" style="width: 250px;" class="me-3">
                        </div>
                        <div class="text-center">
                            <h4 class="mb-2 mt-1">Autenticação</h4>
                        </div>

                        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                            <?php if (!empty($error_message)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= htmlspecialchars($error_message) ?>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail Institucional</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Senha</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <span class="input-group-text" id="togglePassword">
                                        <i class="bi bi-eye-slash-fill"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100 py-2 mt-3">Entrar</button>

                            <div class="text-center mt-4">
                                <a href="./forms/form_user.php" class="text-decoration-none">Criar uma conta</a>
                                <span class="mx-2">|</span>
                                <a href="./forms/form_recover_password.php" class="text-decoration-none">Esqueci minha senha</a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="login-footer">
        Copyright © 2025 CCNE/UFSM. Todos os direitos reservados.
    </footer>

    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = togglePassword.querySelector('i');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'password') {
                // Se o tipo é senha, mostra o ícone de olho fechado
                eyeIcon.classList.remove('bi-eye-fill');
                eyeIcon.classList.add('bi-eye-slash-fill');
            } else {
                // Se o tipo é texto, mostra o ícone de olho aberto
                eyeIcon.classList.remove('bi-eye-slash-fill');
                eyeIcon.classList.add('bi-eye-fill');
            }
        });
    </script>
</body>
</html>