<?php
    session_start();
    if (isset($_SESSION['name'])) {
        $mensaje = $_SESSION['name']. " a sido redirigido a el ERROR 403";
    } else {
        $mensaje = "Un usuario a sido redirigido a el ERROR 403";
    }
    $fecha = date("Y-m-d H:i:s");
    $linea = "[$fecha] $mensaje" . PHP_EOL;
    file_put_contents("../logs.txt", $linea, FILE_APPEND);
    if (!isset($_SESSION['selected_lang'])) {
    $_SESSION['selected_lang'] = 'CASTELLANO';
}
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="/media/lupa.ico">
</head>
<body class="error">
    <div class="postit">
        <?php
        echo '<img class="topPin" src="/media/pin.png" alt="'. $_SESSION['lang_data']['TEXT_PUSHPIN'] .'">';
        echo '<img src="/media/postit.png" alt="'. $_SESSION['lang_data']['TEXT_POSTIT'] .'">';
        echo "<p>". $_SESSION['lang_data']['TEXT_ERROR_404'] ."</p>";
        ?>
    </div>

    <div class="smallPostits">
        <div>
            <?php
            echo '<img class="pins" src="/media/pin.png" alt="'. $_SESSION['lang_data']['TEXT_PUSHPIN'] .'">';
            echo '<input type="button" id="goIndex" value="'. $_SESSION['lang_data']['TEXT_BUTTON_RETURN'] .'" onclick="changePageInitialPage()">';
            ?>
        </div>
        <div>
            <?php
            echo '<img class="pins" src="/media/pin.png" alt="'. $_SESSION['lang_data']['TEXT_PUSHPIN'] .'">';
            echo '<input type="button" id="goRanking" value="'. $_SESSION['lang_data']['TEXT_BUTTON_RANKING'] .'" onclick="changePageStatsPage()">';
            ?>
        </div>
     </div>

     <script>
        const goIndex = document.getElementById("goIndex");
        const goRanking = document.getElementById("goRanking");

        document.addEventListener("keydown", (event) => {
            const key = event.key.toLowerCase();

            if (key === "p") {
                goIndex.classList.add("highlightButtonTextErrors");
                setTimeout(() => {
                    goIndex.click();
                }, 1000);
            }

            if (key === "r") {
                goRanking.classList.add("highlightButtonTextErrors");
                setTimeout(() => {
                    goRanking.click();
                }, 1000);
            }
        });

        function changePageInitialPage(){
            window.location = "/index.php";
        }

        function changePageStatsPage(){
            window.location = "/ranking.php";
        }
     </script>
</body>
</html>
