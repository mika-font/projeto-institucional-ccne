<?php
function conectServer(){
    $bdServer = "localhost";
    $bdUser = "root";
    $bdPassword = "";
    $bdDataBase = "ccne_bd";

    $conect = mysqli_connect($bdServer, $bdUser, $bdPassword, $bdDataBase);
    
    if ($conect) {
        return $conect;
    } else {
        die("Erro ao acessar o banco de dados! " . mysqli_connect_errno() . ": " . mysqli_connect_error());
    }
}

?>