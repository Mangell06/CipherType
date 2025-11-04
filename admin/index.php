<?php
session_start();
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
<body>
<div class="admincontainermain">
<?php
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    $username = $_SESSION['username'];
    echo '<form action="logout.php" method="post" id="logoutcontainer">';
    echo "<p>Bienvenid@ $username</p>";
    echo '<button type="submit" id="buttonLogout">Cerrar sesión</button>';
    echo '</form>';

    echo '<div class="toolscontainer">';
        echo '<div class="todo">';
         if (isset($_SESSION['fraseCreada']) && $_SESSION['fraseCreada']) {
            echo "<p class='correct'>La frase se ha creado correctamente</p>";
            unset($_SESSION['fraseCreada']);
        } else if (isset($_POST["deleteSuccess"]) && $_POST["deleteSuccess"]) {
            echo "<p class='correct'>La frase se ha eliminado correctamente</p>";
        }
        echo '<div class="buttonpanel">';
            echo "<form action='/admin/create_sentence.php' method='post' style='display:inline;'>";
                echo '<button type="submit" id="addbutton">&#43;</button>';
            echo '</form>';
            echo '<button type="button" id="toggleView">En listar</button>';
        echo '</div>';
        echo '<br/>';
        echo '<div id="contentContainer" style="display:none;">';
            echo '<form method="post">';
                echo '<select name="selectdifficulty" id="selectdifficulty" onchange="this.form.submit()">';
                    echo '<option value="" selected hidden>Selecciona dificultad</option>';
                    echo '<option value="sencillo">Sencillo</option>';
                    echo '<option value="normal">Normal</option>';
                    echo '<option value="experto">Experto</option>';
                echo '</select>';
            echo '</form>';

            $selectdifficulty = $_POST['selectdifficulty'] ?? 'sencillo';
            if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
                $sentences = fopen('../sentences.txt', 'r');
                if ($sentences) {
                    while (!feof($sentences)) {
                        $linea = fgets($sentences);
                        if ($linea === false || trim($linea) === '') continue;
                        $linea = trim($linea);
                        $partes = explode(':', $linea, 2);
                        if (count($partes) < 2) continue;
                        $nivel = trim($partes[0]);
                        if ($nivel !== $selectdifficulty) continue;
                        $selectedSentences = explode('*', $partes[1]);
                        break;
                    }
                    $selectdifficultyShow = ucfirst($selectdifficulty);
                    echo "<table>";
                    echo "<tr><th>Frases <br/> dificultat: $selectdifficultyShow</th></tr>";
                    foreach ($selectedSentences as $count => $frase) {
                        if (trim($frase) == "") continue;
                        if ($count % 2 !== 0) {
                            echo "<tr><td class='secondly'>".$frase;
                        } else {
                            echo "<tr><td>".$frase;
                        }
                        echo "<form action='/admin/delete_sentences.php' method='post'>";
                        echo "<input name='selectdifficulty' type='hidden' value='".$selectdifficulty."'>";
                        echo "<input name='fraseIndex' type='hidden' value='".$count."'>";
                        echo "<button type='submit' class='deletebutton'>&#128465;</button>";
                        echo '</form></td></tr>';
                    }
                    echo "</table>";
                    fclose($sentences);
                }
            }
        echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    echo "<form action='/admin/login.php' method='post' id='logincontainer'>";
    echo '<label for="username">Nombre de usuario</label>';
    echo '<input type="text" id="username" name="username" placeholder="juan.perez" />';
    echo '<label for="password">Contraseña</label>';
    echo '<input type="password" id="password" name="password" placeholder="contraseña123" />';
    echo '<button type="submit" id="buttonLogin">Iniciar sesión</button>';
    if (isset($_SESSION['error']) && $_SESSION['error']) {
        echo '<p class="error">El usuario no existe o la contraseña es incorrecta</p>';
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
document.addEventListener("keydown", (event)=>{
    if ((event.key).toLocaleLowerCase() === "i" && event.shiftKey){
        buttonLogin.classList.add("highlightButtonTextAdmin");
        setTimeout(() => { buttonLogin.click(); }, "1000"); 
    } else if ((event.key).toLocaleLowerCase() === "c"){    
        buttonLogout.classList.add("highlightButtonText");
        setTimeout(() => { buttonLogout.click(); }, "1000");
    }
});
</script>
</body>
</html>
