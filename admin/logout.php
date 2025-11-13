<?php
    session_start();
    $mensaje = $_SESSION['username']. " a cerrado sesion";
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);
    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    file_put_contents("logs.txt", $linea, FILE_APPEND);
    unset($_SESSION['username']);
    unset($_SESSION['password']);
    header('Location: index.php');
    exit;
?>