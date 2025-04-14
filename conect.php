<?php
function conectServer(){
    $bdServer = "localhost";
    $bdUser = "root";
    $bdPassword = "";
    $bdDataBase = "";

    $conexao = mysqli_connect($bdServer, $bdUser, $bdPassword, $bdDataBase);
    
    if ($conexao) {
        return $conexao;
    } else {
        die("Erro ao acessar o banco de dados! " . mysqli_connect_errno() . ": " . mysqli_connect_error());
    }
}

?>