<?php 
//tela de login
if (isset($_POST['login']) && !empty($_POST['email']) && !empty($_POST['password'])) {
    include_once('conect.php');
    $conect = conectServer();
    session_start();

    $email = $_POST['email'];
    $password = $_POST['password'];

    $command = $conect->prepare("SELECT * FROM usuario WHERE email = ?");
    $command->bind_param("s", $email);
    $command->execute();
    $consult = $command->get_result();

    if (!$consult) { // Consulta não realizada
        echo mysqli_errno($conect) . ": " . mysqli_error($conect);
        die();
    }

    if ($consult->num_rows != 1) { // Se ouver mais de um ou nenhum email cadastrado
        session_destroy();
        echo "<script>alert('Credenciais incorretas.');</script>";

    } else { // Se houver somente um email
        $password_bd = mysqli_fetch_assoc($consult);
        if (password_verify($password, $password_bd['senha'])) { // Verifica se a senha informada confere com a password do banco de dados
            $_SESSION['email'] = $email;
            $_SESSION['id_user'] = $password_bd['id_user'];
            $_SESSION['type'] = $password_bd['tipo'];
            $_SESSION['last_access'] = time();
            session_regenerate_id(true); //Regenera o ID da sessão para evitar ataques de fixação de sessão

            header('Location: ./central.php'); // Redireciona para a central do sistema
            exit();
        } else {
            session_destroy(); // password incorreta
        }
    }
} else if (isset($_GET['msg']) && $_GET['msg'] == 'nao_autorizado') {
    echo "<script>alert('Você não está autorizado a acessar esta página.');</script>";
} else if (isset($_GET['msg']) && $_GET['msg'] == 'timeout') {
    echo "<script>alert('Sua sessão expirou. Por favor, faça login novamente.');</script>";
} else if (!empty($_POST['email']) && !empty($_POST['password'])){
    echo "<script>alert('Por favor, preencha todos os campos.');</script>";
} else {
    session_start();
    if (isset($_SESSION['email']) && isset($_SESSION['id_user']) && isset($_SESSION['type'])) {
        header('Location: ./central.php'); // Se já estiver logado, redireciona para a central
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

            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br>
            
            <a href="./forms/form_recover_password.php">Esqueci minha senha</a><br>
            <button type="submit" name="login">Entrar</button><br>
            
            <a href="./forms/form_user.php">Cadastrar</a>
        </form>
    </main>
</body>
</html>