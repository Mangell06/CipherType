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
    } else {
        $_SESSION['error'] = false;
    }
    header('Location: index.php');
    exit;
?>