<?php
    session_start();
    if (isset($_SESSION['name']) && isset($_SESSION['points']) && isset($_SESSION["temp"])) {
        $mensaje = $_SESSION['name']. " a guardado su puntuacion en el ranking (" . $_SESSION['points'] . " puntos)";
        $fecha = date("Y-m-d H:i:s");
        $archivo = basename(__FILE__);
        $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
        file_put_contents("admin/logs.txt", $linea, FILE_APPEND);
        $file = fopen('ranking.txt','a');
        $line = "#{$_SESSION['name']}:{$_SESSION['points']}:{$_SESSION['temp']}\n";
        fwrite($file, $line);
        fclose($file);
    }    
if (!isset($_SESSION['lang_data'])) {
    $_SESSION['selected_lang'] = 'CASTELLANO';
    $_SESSION['lang_data'] = [];
    $idiomaSeleccionado = $_SESSION['selected_lang'];
    $archivo = fopen('idiomas.txt', 'r');
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
    <?php
        echo "<title>". $_SESSION['lang_data']['NAME_RANKING'] ."</title>";
    ?>
     <link rel="stylesheet" href="styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="media/lupa.ico">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
<body class="gameover">
    <?php
        if (isset($_SESSION['name'])) {
            echo "<div class='cancelSession'>";
            echo "<p>Nombre: ".$_SESSION['name']."</p>";
            echo "<button type='submit' id='closeSession' onclick='destroySession()'>". $_SESSION['lang_data']['TEXT_LOGOUT'] ."</button>";
            echo "</div>";
        }
    ?>
    <div class="gameoverDiv">
        <h1>Ranking</h1>
    <script>
        const gameoverBGX = new Audio("./media/ranking.mp3");
        gameoverBGX.loop = true;
        gameoverBGX.load();
        gameoverBGX.play();
    </script>
    <?php
    function formatearTiempo($segundos) {
        if ($segundos === null) return null;
        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);
        $segundosRestantes = $segundos % 60;

        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundosRestantes);
    }
    $contenido = file_get_contents('ranking.txt'); // leer el fichero
        $usuarios = explode('#', $contenido); // separar usuarios
        $ranking = [];
        $count = 0;
        foreach ($usuarios as $usuario) {
            if (!empty($usuario) && strpos($usuario, ':') !== false) {
                $usuarioExploded = explode(':', $usuario);
                $name=$usuarioExploded[0];
                $points=$usuarioExploded[1];
                $temp=isset($usuarioExploded[2]) ? $usuarioExploded[2] : null;
                $ranking[$count] = [$name, (int)$points, $temp];
                $count ++;
            }
        }

        usort($ranking, fn($nombre, $puntos) => $puntos[1] <=> $nombre[1]); // ordenar array por puntos de mayor a menor

        $winnerIndex = null;
        foreach ($ranking as $count => [$name,$points,$temp]) {
            if (isset($_SESSION['name']) && isset($_SESSION['points']) && $_SESSION['name'] === $name && $_SESSION['points'] == $points) {
                $winnerIndex = $count;
            }
        }

        $pageSize = 25;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
        $totalPages = ceil(count($ranking) / $pageSize);

        if (!isset($_GET['page']) && $winnerIndex != null) {
            $page = floor($winnerIndex / $pageSize);
        }

        echo "<table>";
        echo "<tr><th>". $_SESSION['lang_data']['TEXT_NAME'] ."</th><th>". $_SESSION['lang_data']['TEXT_POINTS'] ."</th><th>". $_SESSION['lang_data']['TEXT_TEMP'] ."</th></tr>";
        foreach ($ranking as $count => [$name,$points,$temp]) {
            if ($count < $page*$pageSize || $count > ($page+1)*$pageSize-1) {
                continue;
            }
            if (isset($_SESSION['name']) && isset($_SESSION['points']) && $_SESSION['name'] === $name && $_SESSION['points'] == $points) {
                echo "<tr class='winner'><td>".$name."</td><td>".$points."</td><td>".formatearTiempo($temp)."</td></tr>";
            } else if ($count % 2 === 0 && $count !== 1) {
                echo "<tr class='second'><td>".$name."</td><td>".$points."</td><td>".formatearTiempo($temp)."</td></tr>";
            } else {
                echo "<tr><td>".$name."</td><td>".$points."</td><td>".formatearTiempo($temp)."</td></tr>";
            }
        }
        echo "</table>";
        echo "<div class='allPages'>";
        for ($i = 0; $i <$totalPages; $i++) {
            echo "<a class='totalPagesNumber".($page == $i ? " active" : '')."' href='/ranking.php?page=".$i."'>".($i+1)."</a>";
        }
        echo "</div>";
    
    unset($_SESSION['points']);
    ?>
    <div class="maincontainer">
    <?php
       echo '<button type="submit" id="returnIndex" value="Volver al principio">'. $_SESSION['lang_data']['TEXT_RETURN'] .'</button>'
    ?>
    </div>
    <script>
        const closeSession = document.getElementById("closeSession");
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }
        const button = document.getElementById("returnIndex");
        button.addEventListener('click', (e) => {
            e.preventDefault();
            window.location = "/index.php";
        })

        const selectedLang = "<?php echo isset($_SESSION['selected_lang']) ? $_SESSION['selected_lang'] : ''; ?>";
        document.addEventListener("keydown", (event) => {
            if (event.target.nodeName === "INPUT") return;

            if (selectedLang === "CASTELLANO") {
                if (event.key.toLowerCase() === "v") {
                    button.classList.add("highlightButtonText");
                    setTimeout(() => {
                        button.click();
                    }, 1000);
                } else if (event.key.toLowerCase() === "c") {
                    closeSession.classList.add("highlightButtonText");
                    setTimeout(() => {
                        closeSession.click();
                    }, 1000);
                }

            } else if (selectedLang === "CATALÁN") {
                if (event.key.toLowerCase() === "t" && event.shiftKey) {
                    button.classList.add("highlightButtonText");
                    setTimeout(() => {
                        button.click();
                    }, 1000);
                }else if (event.key.toLowerCase() === "t") {
                    closeSession.classList.add("highlightButtonText");
                    setTimeout(() => {
                        closeSession.click();
                    }, 1000);
                }

            } else if (selectedLang === "ENGLISH") {
                if (event.key.toLowerCase() === "r") {
                    button.classList.add("highlightButtonText");
                    setTimeout(() => {
                        button.click();
                    }, 1000);
                } else if (event.key.toLowerCase() === "l") {
                    closeSession.classList.add("highlightButtonText");
                    setTimeout(() => {
                        closeSession.click();
                    }, 1000);
                }
            }
        });
    </script>
</body>
</html>
