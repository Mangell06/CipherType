<?php
    session_start();
    if (isset($_SESSION['points'])) {
        unset($_SESSION['points']);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CipherType</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="media/lupa.ico">
</head>
<body class="play">
    <?php
        if (isset($_SESSION['name'])) {
            echo "<div class='cancelSession'>";
            echo "<p>". $_SESSION['lang_data']['TEXT_NAME'].": ".$_SESSION['name']."</p>";
            echo "<button type='submit' id='closeSession' onclick='destroySession()'>". $_SESSION['lang_data']['TEXT_LOGOUT']."</button>";
            echo "</div>";
        }
    if (!isset($_POST['lenguageselect']) && !isset($_SESSION['selected_lang'])) {
        echo "<div class='languageMain'>";
        echo "<form method='post'>";
        echo "<select name='lenguageselect' id='lenguageselect' onchange='this.form.submit()'>";
        echo "<option value='' selected disabled hidden>CASTELLANO</option>";
        $archivo = fopen('idiomas.txt', 'r');
        if ($archivo) {
            while (($linea = fgets($archivo)) !== false) {
                $linea = trim($linea);
                if ($linea === '') continue;
                if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
                    $idioma = substr($linea, 1, -1); // quita los corchetes
                    echo "<option value='".$idioma."'>".$idioma."</option>";
                }
            }
            fclose($archivo);
        }
        echo "</select>";
        echo "</form>";
        echo "</div>";
    } else {
        if (isset($_POST['lenguageselect'])) {
            $idiomaSeleccionado = $_POST['lenguageselect'];
            $_SESSION['selected_lang'] = $idiomaSeleccionado;
            $_SESSION['lang_data'] = [];
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
        echo "<div class='maincontainer'>";
        echo "<form action='./play.php' method='post' class='datacontainer'>";
        echo "<h1>CipherType</h1>";
        echo "<div class='incontainer'>";
        echo '<input type="text" id="inname" name="inname" placeholder="' . $_SESSION['lang_data']['TEXT_NAME_GAME'] . '">';
        echo "<select name='indifficulty' id='indifficulty'>";
        echo "<option value='sencillo'>" . $_SESSION['lang_data']['DIFFICULTY_SIMPLE'] . "</option>";
        echo "<option value='normal'>" . $_SESSION['lang_data']['DIFFICULTY_NORMAL'] . "</option>";
        echo "<option value='experto'>" . $_SESSION['lang_data']['DIFFICULTY_EXPERT'] . "</option>";
        echo "</select>";
        echo "<button disabled type='submit' id='buttonInitialitze'>" . $_SESSION['lang_data']['TEXT_INITIALITZE'] . "</button>";
        echo "<p id='messageerror' class='error'></p>";
        echo "<noscript>";
        echo "<p class='error'>" . $_SESSION['lang_data']['TEXT_ERROR_JAVASCRIPT'] . "</p>";
        echo "</noscript>";
        echo "</div>";
        echo "</form>";
        echo "<div class='datacontainer'>";
        echo "<h1>Descripción</h1>";
        echo "<p>" . $_SESSION['lang_data']['DESCRIPTION_GAME'] . "</p>";
        echo "</div>";
        echo "</div>";
        echo "<img class='mesa' src='media/mesamesa.jpg' alt='" . $_SESSION['lang_data']['ALT_MESA'] . "'>";
        echo "<div class='machine'>";
        echo "<img class='typingMachine' src='media/typingmachine.png' alt='" . $_SESSION['lang_data']['ALT_MACHINE'] . "'>";
        echo "</div>";
    }
    ?>
    <script>
    const closeSession = document.getElementById("closeSession");
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }
    const buttonInitialitzeGame = document.getElementById('buttonInitialitze');
    document.querySelector("#indifficulty").disabled = false;
    document.querySelector("#buttonInitialitze").disabled = false;
    const input = document.getElementById('inname');

    buttonInitialitzeGame.addEventListener("click", (event) => {
        if (input.value.trim() === "") {
            event.preventDefault();
            const message = document.getElementById('messageerror');
            message.textContent = "<?php echo $_SESSION['lang_data']['TEXT_ERROR_NAME'];?>";
        }
    });

    document.addEventListener("keydown", (event)=>{
        if (event.target.nodeName === "INPUT"){
            return;
        }
        if ((event.key).toLocaleLowerCase() === "i"){
            buttonInitialitzeGame.classList.add("highlightButtonText");
            setTimeout(() => {
                buttonInitialitzeGame.click();
            }, "1000");
        } else if ((event.key).toLocaleLowerCase() === "c"){
            closeSession.classList.add("highlightButtonText");
            setTimeout(() => {
                closeSession.click();
            }, "1000");
    }})

    const valueName = "<?php 
    if (isset($_SESSION['name'])){
        echo $_SESSION['name'];
    } else{
        echo '';
    } ?>";
    if (valueName !== ''){
        input.value = valueName;
    }
    
</script>
</body>
</html>