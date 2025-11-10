<?php
session_start();
    if (!isset($_SESSION['lang_data'])) {
        header('Location: ../index.php');
        exit;
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
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    echo '<body class="panel">';
    echo '<div class="admincontainermain">';
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
                echo '<button type="submit" id="addImagebutton">Insertar imágenes</button>';
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
                    echo '<option value="" selected hidden>' . $_SESSION['lang_data']['TEXT_SELECT_DIFICULTY'] . '</option>';
                    echo '<option value="sencillo">' . $_SESSION['lang_data']['DIFFICULTY_SIMPLE'] . '</option>';
                    echo '<option value="normal">' . $_SESSION['lang_data']['DIFFICULTY_NORMAL'] . '</option>';
                    echo '<option value="experto">' . $_SESSION['lang_data']['DIFFICULTY_EXPERT'] . '</option>';
                echo '</select>';
            echo '</form>';

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

                    foreach ($selectedSentences as $count => $frase) {
                        $frase = trim($frase);
                        if ($frase == "") continue;
                        $fraseSplit = explode("|", $frase);
                        $imageName = $fraseSplit[1] ?? "";
                        $secondlyClass = $count % 2 !== 0 ? "secondly" : "";
                        echo "<tr><td class='$secondlyClass'><img src='/admin/image/$imageName'><td class='$secondlyClass'>".$fraseSplit[0];

                        echo "<form action='/admin/delete_sentences.php' method='post'>";
                        echo "<input name='selectdifficulty' type='hidden' value='".$selectdifficulty."'>";
                        echo "<input name='fraseIndex' type='hidden' value='".$count."'>";
                        echo "<button type='submit' class='deletebutton'>&#128465;</button>";
                        echo '</form></td></tr>';
                    }

                    echo "</table>";
                }

            }
        echo '</div>';
    echo '</div>';
    echo '</div>';
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
document.addEventListener("keydown", (event)=>{
    if (event.target.nodeName === "INPUT"){
        return;
    }
    if ((event.key).toLocaleLowerCase() === "i"){
        if (buttonLogin == null){
            return;
        }
        buttonLogin.classList.add("highlightButtonTextAdmin");
        setTimeout(() => { buttonLogin.click(); }, "1000"); 
    } else if ((event.key).toLocaleLowerCase() === "c"){    
        if (buttonLogout == null) {
            return;
        }
        buttonLogout.classList.add("highlightButtonText");
        setTimeout(() => { buttonLogout.click(); }, "1000");
    } else if (event.key === "a"){    
        if (addbutton == null) {
            return;
        }
        addbutton.classList.add("highlightButtonText");
        setTimeout(() => { addbutton.click(); }, "1000");
    } else if ((event.key).toLocaleLowerCase() === "l"){    
        if (toggleView == null) {
            return;
        }
        toggleView.classList.add("highlightButtonText");
        setTimeout(() => { toggleView.click(); }, "1000");
    } 
});
</script>
</body>
</html>
