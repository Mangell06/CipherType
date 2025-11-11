<?php
    session_start();
    $mensaje = $_SESSION['name']. " a cerrado sesion en el juego";
    $fecha = date("Y-m-d H:i:s");
    $linea = "[$fecha] $mensaje" . PHP_EOL;
    file_put_contents("logs.txt", $linea, FILE_APPEND);
    unset($_SESSION['name']);
    unset($_SESSION['points']);
    header('Location: /');
    exit();
    
?>
