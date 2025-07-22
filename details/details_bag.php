<?php

// Exibe a bolsa com detalhes;
include_once('../control.php');

if (!isset($_GET['id_bolsa'])) {
    header("Location: " . BASE_URL . "/central.php?msg=10"); // Acesso não autorizado
    exit();
}

$id_bolsa = $_GET['id_bolsa'];
$query = $conect->prepare("SELECT * FROM bolsa WHERE id_bolsa = ?");
$query->bind_param("i", $id_bolsa);
$query->execute();
$result = $query->get_result();

if ($result && $result->num_rows > 0) {
    $bag = $result->fetch_assoc();
} else {
    header("Location: " . BASE_URL . "/central.php?msg=10"); // Acesso não autorizado
    exit();
}

?>