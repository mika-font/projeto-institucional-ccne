<?php 
//tela de login
if (isset($_POST['login']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include_once('conect.php');
    $conexao = conectServer();
    session_start();

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $comando = $conexao->prepare("SELECT * FROM usuario WHERE email = ?");
    $comando->bind_param("s", $email);
    $comando->execute();
    $consulta = $comando->get_result();

    if (!$consulta) {
        echo mysqli_errno($conexao) . ": " . mysqli_error($conexao);
        die();
    }

    if (mysqli_num_rows($consulta) !== 1) {
        session_destroy();
        exit();
    } else {
        $senha_bd = mysqli_fetch_assoc($consulta);

        if (password_verify($senha, $senha_bd['senha'])) { // Verifica se a senha informada confere com a senha do banco de dados
            $_SESSION['email'] = $email;
            $_SESSION['id_usuario'] = $senha_bd['id_usuario'];
            $_SESSION['tipo'] = $senha_bd['tipo'];
            $_SESSION['ultimo_acesso'] = time();
            session_regenerate_id(true); //Regenera o ID da sessão para evitar ataques de fixação de sessão

            header('Location: central.php'); // Redireciona para a central do sistema
        } else {
            session_destroy(); // Senha incorreta
        }
    }
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nao_autorizado') {
    echo "<script>alert('Você não está autorizado a acessar esta página.');</script>";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'timeout') {
    echo "<script>alert('Sua sessão expirou. Por favor, faça login novamente.');</script>";
} else if (!empty($_POST['email']) && !empty($_POST['senha'])){
    echo "<script>alert('Por favor, preencha todos os campos.');</script>";
} else {
    session_start();
    if (isset($_SESSION['email']) && isset($_SESSION['id_usuario']) && isset($_SESSION['tipo'])) {
        header('Location: central.php'); // Se já estiver logado, redireciona para a central
        exit();
    }
} 
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./assets/icons/faviconccne.png" type="image/x-icon">
    <title>Portal de Bolsas CCNE</title>
</head>
<body>
    <main>
        <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
            <h1>Login</h1>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required><br> 
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required><br>
            <a href="forms/form_recuperar_senha.php">Esqueci minha senha</a><br>
            <button type="submit" name="login">Entrar</button>
            <a href="forms/form_user.php">Cadastrar</a>
        </form>
    </main>
</body>
</html>