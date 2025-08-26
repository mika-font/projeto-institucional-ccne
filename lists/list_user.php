<?php
include_once('../configs/rules.php');
include_once('../control.php');

if(isset($_SESSION['type']) && $_SESSION['type'] == RULE_GERENTE){
    $query = $conect->prepare("SELECT id_usuario, nome, email, tipo FROM usuario ORDER BY nome ASC");
    $query->execute();
    $result = $query->get_result();

    if(!$result){
        // Tratar erro de forma mais robusta no futuro (ex: log)
        die("Erro ao executar a consulta: " . $conect->error);
    }
} else {
    header('Location: ' . BASE_URL . '/central.php?msg=nao_autorizado');
    exit();
}

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
<body>
    <div class="container mt-4">
        <h1>Gerenciamento de Usuários</h1>
        <p>Listagem de todos os usuários do sistema.</p>
        <a href="../forms/form_user.php" class="btn btn-success mb-3">Adicionar Novo Usuário</a>

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th class="text-center">Opções</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id_usuario'] ?></td>
                        <td><?= htmlspecialchars($user['nome']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?php 
                                switch ($user['tipo']) : 
                                    case RULE_ESTUDANTE: echo "Estudante"; break;
                                    case RULE_ORIENTADOR: echo "Orientador"; break; 
                                    case RULE_DIRECAO: echo "Direção"; break; 
                                    case RULE_FINANCEIRO: echo "Financeiro"; break; 
                                    case RULE_GERENTE: echo "Gerente"; break;
                                    default: echo "Desconhecido"; break;
                                endswitch;
                            ?> 
                        </td>
                        <td class="text-center">
                            <a href='../forms/form_user.php?id_user=<?= $user['id_usuario']; ?>' class="btn btn-sm btn-primary">Editar</a>
                            
                            <form action='../processes/process_user.php' method='post' style='display:inline;' onsubmit="return confirm('Tem certeza que deseja excluir o usuário <?= htmlspecialchars($user['nome']) ?>? Esta ação é irreversível.');">
                                <input type='hidden' name='id_user' value='<?= $user['id_usuario']; ?>'>
                                <button type='submit' name='delete' class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="../central.php" class="btn btn-secondary">Voltar</a>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
