<?php    
    if (!isset($_SESSION['selected_lang'])) {
    $_SESSION['selected_lang'] = 'CASTELLANO';
}
if (isset($_SESSION['name'])) {
    $mensaje = $_SESSION['name']. " a accedido a game.php";
} else {
    $mensaje = "Un usuario a accedido a game.php";
}
$fecha = date("Y-m-d H:i:s");
$archivo = basename(__FILE__);
$linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
file_put_contents("admin/logs.txt", $linea, FILE_APPEND);
if (isset($_POST['lenguageselect'])) {
    $_SESSION['selected_lang'] = $_POST['lenguageselect'];
    unset($_SESSION['lang_data']); // fuerza recarga
}
if (!isset($_SESSION['lang_data'])) {
    $_SESSION['lang_data'] = [];
    $idiomaSeleccionado = $_SESSION['selected_lang'];

    $archivo = fopen('../idiomas.txt', 'r');
    $dentroIdioma = false;
    while (($linea = fgets($archivo)) !== false) {
        $linea = trim($linea);
        if ($linea === '') continue;

        if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
            $idiomaActual = substr($linea, 1, -1);
            $dentroIdioma = ($idiomaActual === $idiomaSeleccionado);
            continue;
        }

        if ($dentroIdioma && strpos($linea, '=') !== false) {
            list($clave, $valor) = explode('=', $linea, 2);
            $clave = trim($clave);
            if (str_contains($valor, '|')) {
                $valor = trim($valor, "\"|\t ");
                $_SESSION['lang_data'][$clave] = array_map(
                    fn($v) => trim($v, '"'),
                    explode('|', $valor)
                );
            } else {
                $valor = trim($valor, "\"\t ");
                $_SESSION['lang_data'][$clave] = $valor;
            }
        }
    }
    fclose($archivo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <link rel="stylesheet" href="styles.css?no-cache=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="icon" href="media/lupa.ico">
</head>
<body class="gameErrorJS">
    <div class="datacontainer">
        <noscript>
            <?php
               echo '<h1 class="error">' . $_SESSION['lang_data']['TEXT_ERROR_JAVASCRIPT'] . '</h1>';
             ?>
        </noscript>
        <?php
            echo '<h1 class="js-required hidden">' . $_SESSION['lang_data']['TEXT_JAVASCRIPT_ACTIVATE'] . '</h1>';
        ?>
    </div>
    
    <script>
        const text =document.querySelector(".js-required.hidden");
        text.classList.remove("js-required");
        text.classList.remove("hidden");
    </script>
</body>
</html>