<?php
    session_start();
    $credenciales = fopen('credentials.txt','r'); // leer el fichero
    $username = $_POST['username'];
    $password = $_POST['password'];
    $verificate = false;
    while (!feof($credenciales) && !$verificate) {
        $linea = fgets($credenciales);
        if ($linea === "") continue;
        $linea = trim($linea);
        $linea = explode(':', $linea,2);
        if (count($linea) < 2) continue;
        if ($username === $linea[0] && $password === $linea[1]) {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            $verificate = true;
        }
    }
    fclose($credenciales);
    if (!$verificate) {
        $_SESSION['error'] = true;
        $mensaje = $_SESSION['username']. " a intentando iniciado sesion";
        $fecha = date("Y-m-d H:i:s");
        $linea = "[$fecha] $mensaje" . PHP_EOL;
        file_put_contents("logs.txt", $linea, FILE_APPEND);
    } else {
        $_SESSION['error'] = false;
        $mensaje = $_SESSION['username']. " a iniciado sesion";
        $fecha = date("Y-m-d H:i:s");
        $archivo = basename(__FILE__);
        $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
        file_put_contents("logs.txt", $linea, FILE_APPEND);
    }
    header('Location: index.php');
    exit;
?>