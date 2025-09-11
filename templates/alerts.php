<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['alert'])) {

    $alert_type = $_SESSION['alert']['type'] ?? 'info'; // 'Sucesso', 'Perigo', 'Aviso', 'Informação'
    $alert_message = $_SESSION['alert']['message'] ?? 'Ocorreu um evento.';

    $bootstrap_class = 'alert-' . htmlspecialchars($alert_type);

    echo "<div class='alert " . $bootstrap_class . " alert-dismissible fade show' role='alert'>";
    echo htmlspecialchars($alert_message);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo "</div>";

    unset($_SESSION['alert']);
}
?>