<?php
session_start();
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
    if (isset($_SESSION['username'])) {
        $mensaje = $_SESSION['username']. " se a cambiado al idioma " . $idiomaSeleccionado;
    } else {
        $mensaje = "Un usuario se a cambiado al idioma " . $idiomaSeleccionado;
    }
    $fecha = date("Y-m-d H:i:s");
    $archivo = basename(__FILE__);
    $linea = "[$fecha] [$archivo] $mensaje" . PHP_EOL;
    file_put_contents("logs.txt", $linea, FILE_APPEND);
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
    <title>Administrador</title>
    <link rel="icon" href="../media/lupa.ico">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css?no-cache=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<?php
if (isset($_POST['lenguageselect'])) {
    $idiomaSeleccionado = $_POST['lenguageselect'];
    $_SESSION['selected_lang'] = $idiomaSeleccionado;
    $_SESSION['lang_data'] = [];
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
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    echo '<body class="panel">';
    echo '<div class="admincontainermain">';
    echo "<form method='post'>";
    echo "<select name='lenguageselect' class='rightpositionlenguage' id='lenguageselect' onchange='this.form.submit()'>";
    echo "<option value='' selected disabled hidden>".$_SESSION['selected_lang']."</option>";
    $archivo = fopen('../idiomas.txt', 'r');
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
    $username = $_SESSION['username'];
    echo '<div class="phrases">';
    echo "<p class='welcome'>" . $_SESSION['lang_data']['WELCOME_USER'] . " $username</p>";
    if (isset($_SESSION['fraseCreada']) && $_SESSION['fraseCreada']) {
            echo "<p class='correct'>" . $_SESSION['lang_data']['CORRECT_ADD_PHRASE'] . "</p>";
            unset($_SESSION['fraseCreada']);
    } else if (isset($_POST["deleteSuccess"]) && $_POST["deleteSuccess"]) {
        echo "<p class='correct'>" . $_SESSION['lang_data']['CORRECT_DELET_PHRASE'] . "</p>";
    } else if (isset($_SESSION['imagenCreada']) && $_SESSION['imagenCreada']){
        echo "<p class='correct'>" . $_SESSION['lang_data']['CORRECT_INSERT_IMAGE'] . "</p>";
    }
    echo '</div>';
    echo '<div class="toolscontainer">';
        echo '<div class="todo">';
        echo '<div class="buttonpanel">';
            echo "<form action='/admin/create_sentence.php' method='post' style='display:inline;'>";
                echo '<button type="submit" id="addbutton">' . $_SESSION['lang_data']['TEXT_ADD_PHRASES'] . '</button>';
            echo '</form>';
            echo "<form action='/admin/upload_image.php' method='get' style='display:inline;'>";
                echo '<button type="submit" id="addImagebutton">'.$_SESSION['lang_data']['UPLOAD_IMAGE_TAB'].'</button>';
            echo '</form>';
            echo '<button type="button" id="toggleView">' . $_SESSION['lang_data']['LIST_PHRASES'] . '</button>';
            echo '<form action="/admin/logout.php" method="post" id="logoutcontainer">';
            echo '<button type="submit" id="buttonLogout">' . $_SESSION['lang_data']['TEXT_LOGOUT'] . '</button>';
            echo '</form>';
        echo '</div>';
        echo '<br/>';
        echo '<div id="contentContainer" style="display:'. (isset($_POST['selectdifficulty']) ? 'block' : 'none') .';">';
            echo '<form method="post">';
                echo '<select name="selectdifficulty" id="selectdifficulty" onchange="this.form.submit()">';
                    // echo '<option value="" hidden'. ((isset($_POST['selectdifficulty']) && $_POST['selectdifficulty'] === "") ? ' selected' : '') .'>' . $_SESSION['lang_data']['TEXT_SELECT_DIFICULTY'] . '</option>';
                    echo '<option value="sencillo"'. ((isset($_POST['selectdifficulty']) && $_POST['selectdifficulty'] === "sencillo") ? ' selected' : '') .'>' . $_SESSION['lang_data']['DIFFICULTY_SIMPLE'] . '</option>';
                    echo '<option value="normal"'. ((isset($_POST['selectdifficulty']) && $_POST['selectdifficulty'] === "normal") ? ' selected' : '') .'>' . $_SESSION['lang_data']['DIFFICULTY_NORMAL'] . '</option>';
                    echo '<option value="experto"'. ((isset($_POST['selectdifficulty']) && $_POST['selectdifficulty'] === "experto") ? ' selected' : '') .'>' . $_SESSION['lang_data']['DIFFICULTY_EXPERT'] . '</option>';
                echo '</select>';

            $selectdifficulty = $_POST['selectdifficulty'] ?? 'sencillo';
            if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
                $sentences = fopen('../sentences.txt', 'r');
                if ($sentences) {
                    $dentroIdioma = false;
                    while (!feof($sentences)) {
                        $linea = fgets($sentences);
                        if ($linea === false || trim($linea) === '') continue;
                        $linea = trim($linea);
                        if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
                            $idiomaActual = substr($linea, 1, -1);
                            $dentroIdioma = ($idiomaActual === $_SESSION['selected_lang']);
                            continue;
                        }

                        if (!$dentroIdioma) continue;

                        $partes = explode(':', $linea, 2);
                        if (count($partes) < 2) continue;
                        $nivel = trim($partes[0]);
                        if ($nivel !== $selectdifficulty) continue;

                        $selectedSentences = explode('*', $partes[1]);
                        break;
                    }

                    fclose($sentences);
                    $selectdifficultyShow = ucfirst($selectdifficulty);
                    echo "<table>";
                    echo "<tr><th>Imagen</th><th>Frases <br/> dificultad: $selectdifficultyShow</th></tr>";

                    $winnerIndex = null;
                    foreach ($selectedSentences as $count => $frase) {
                        $frase = trim($frase);
                        if ($frase == "") continue;
                        if (isset($_SESSION['last_sentence_added']) && $_SESSION['last_sentence_added'] === $frase) {
                            $winnerIndex = $count;
                        }
                    }

                    $pageSize = 25;
                    $page = isset($_POST['page']) ? ((int)$_POST['page']-1) : 0;
                    $totalPages = ceil(count($selectedSentences) / $pageSize);

                    if (!isset($_POST['page']) && $winnerIndex != null) {
                        $page = floor($winnerIndex / $pageSize);
                    }

                    foreach ($selectedSentences as $count => $frase) {
                        if ($count < $page*$pageSize || $count > ($page+1)*$pageSize-1) {
                            continue;
                        }
                        $frase = trim($frase);
                        if ($frase == "") continue;
                        $fraseSplit = explode("|", $frase);
                        $imageName = $fraseSplit[1] ?? "";
                        if (isset($_SESSION['last_sentence_added']) && $_SESSION['last_sentence_added'] === $frase) {
                            $rowClass = "winner";
                        } else if ($count % 2 === 0) { 
                            $rowClass = "secondly";
                        } else {
                            $rowClass = "";
                        }

                        echo "<tr class='$rowClass'><td><img src='/admin/image/$imageName'></td><td>".$fraseSplit[0];
                        echo "<button type='button' class='deletebutton' onclick='deleteButtonClick(\"$selectdifficulty\", $count)'>&#128465;</button>";
                        echo "</td></tr>";
                    }

                    echo "</table>";
                    echo "<div class='allPages'>";
                    for ($i = 0; $i <$totalPages; $i++) {
                        echo "<input type='submit' name='page' class='totalPagesNumber".($page == $i ? " active" : '')."' value='".($i+1)."'>";
                    }
                    echo "</div>";
                    unset($_SESSION['last_sentence_added']);
                }

            }
        echo '</form>';
        echo '</div>';
    echo '</div>';
    echo '</div>';
    echo "<form id='deleteSentecesForm' action='/admin/delete_sentences.php' method='post'>";
    echo "<input name='selectdifficulty' type='hidden'>";
    echo "<input name='fraseIndex' type='hidden'>";
    echo "</form>";
} else {
    echo '<body class="admin">';
    echo '<div class="admincontainermain">';
    echo "<form action='/admin/login.php' method='post' id='logincontainer'>";
    echo '<label for="username">' . $_SESSION['lang_data']['TEXT_NAMEUSER'] . '</label>';
    echo '<input type="text" id="username" name="username" placeholder="juan.perez" />';
    echo '<label for="password">' . $_SESSION['lang_data']['TEXT_PASSWORD'] . '</label>';
    echo '<input type="password" id="password" name="password" placeholder="' . $_SESSION['lang_data']['TEXT_PASSWORD'] . '123" />';
    echo '<button type="submit" id="buttonLogin">' . $_SESSION['lang_data']['TEXT_BUTTON_LOGIN'] . '</button>';
    if (isset($_SESSION['error']) && $_SESSION['error']) {
        echo '<p class="error">' . $_SESSION['lang_data']['TEXT_ERROR_USER'] . '</p>';
        unset($_SESSION['error']);
    }
    echo '</form>';
}
?>
</div>
<script>
const toggleView = document.getElementById("toggleView");
const contentContainer = document.getElementById("contentContainer");
if (toggleView && contentContainer) {
    toggleView.addEventListener("click", () => {
        const visible = contentContainer.style.display === "block";
        contentContainer.style.display = visible ? "none" : "block";
    });
}

const buttonLogin = document.getElementById("buttonLogin");
const buttonLogout = document.getElementById("buttonLogout");
const addbutton = document.getElementById("addbutton");
const addImagebutton = document.getElementById("addImagebutton");
const selectedLang = "<?php echo isset($_SESSION['selected_lang']) ? $_SESSION['selected_lang'] : ''; ?>";

const keysPressed = new Set();

document.addEventListener("keydown", (event) => {
    if (event.target.nodeName === "INPUT") return;

    keysPressed.add(event.key.toLowerCase());

    if (selectedLang === "CASTELLANO") {
        if (event.key.toLowerCase() === "i" && event.shiftKey) {
            addImagebutton.classList.add("highlightButtonText");
            setTimeout(() => addImagebutton.click(), 1000);
        } else if (event.key.toLowerCase() === "i") {
            buttonLogin.classList.add("highlightButtonText");
            setTimeout(() => buttonLogin.click(), 1000);
        } else if (event.key.toLowerCase() === "c") {
            buttonLogout.classList.add("highlightButtonText");
            setTimeout(() => buttonLogout.click(), 1000);
        } else if (event.key.toLowerCase() === "l") {
            toggleView.classList.add("highlightButtonText");
            setTimeout(() => toggleView.click(), 1000);
        } else if (event.key.toLowerCase() === "a") {
            addbutton.classList.add("highlightButtonText");
            setTimeout(() => addbutton.click(), 1000);
        }

    } else if (selectedLang === "CATALÁN") {
        if (event.key.toLowerCase() === "a" && event.shiftKey) {
            addImagebutton.classList.add("highlightButtonText");
            setTimeout(() => addImagebutton.click(), 1000);
        } else if (event.key.toLowerCase() === "i") {
            buttonLogin.classList.add("highlightButtonText");
            setTimeout(() => buttonLogin.click(), 1000);
        } else if (event.key.toLowerCase() === "t") {
            buttonLogout.classList.add("highlightButtonText");
            setTimeout(() => buttonLogout.click(), 1000);
        } else if (event.key.toLowerCase() === "l") {
            toggleView.classList.add("highlightButtonText");
            setTimeout(() => toggleView.click(), 1000);
        } else if (event.key.toLowerCase() === "a") {
            addbutton.classList.add("highlightButtonText");
            setTimeout(() => addbutton.click(), 1000);
        }

    } else if (selectedLang === "ENGLISH") {
        if (keysPressed.has("l") && keysPressed.has("o")) {
            buttonLogout.classList.add("highlightButtonText");
            setTimeout(() => buttonLogout.click(), 1000);
        } else if (event.key.toLowerCase() === "l" && event.shiftKey) {
            toggleView.classList.add("highlightButtonText");
            setTimeout(() => toggleView.click(), 1000);
        } else if (event.key.toLowerCase() === "l") {
            buttonLogin.classList.add("highlightButtonText");
            setTimeout(() => buttonLogin.click(), 1000);
        } else if (event.key.toLowerCase() === "a") {
            addbutton.classList.add("highlightButtonText");
            setTimeout(() => addbutton.click(), 1000);
        } else if (event.key.toLowerCase() === "i") {
            addImagebutton.classList.add("highlightButtonText");
            setTimeout(() => addImagebutton.click(), 1000);
        }
    }
});

document.addEventListener("keyup", (event) => {
    keysPressed.delete(event.key.toLowerCase());
});

const deleteButtonClick = (dificultad, fraseIndex) => {
    document.querySelector("#deleteSentecesForm input[name='selectdifficulty']").value = dificultad;
    document.querySelector("#deleteSentecesForm input[name='fraseIndex']").value = fraseIndex;
    document.getElementById("deleteSentecesForm").submit();
}

</script>
</body>
</html>
