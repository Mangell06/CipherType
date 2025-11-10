<?php
session_start();
if (!isset($_GET['lang']) && isset($_SESSION['selected_lang'])) {
    header("Location: /admin/upload_image.php?lang=".$_SESSION['selected_lang']);
    exit;
}

if (isset($_FILES['uploadimage']) && !empty($_FILES['uploadimage']['name']) && isset($_GET['lang'])) {
    $level = $_POST["selectdifficulty"];
    $phraseSeleccionada = $_POST["frase".$level];
    $fileSaveSuccess = false;
    $idiomaDelUsuario = $_GET['lang'];
    $sentencesFile = fopen("../sentences.txt", "r+");
    $newContent = "";
    $dentroIdioma = false;
    while (!feof($sentencesFile)) {
        $linea = fgets($sentencesFile);
        if ($linea === false) continue;
        $linea = trim($linea);
        if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
            $idiomaActual = substr($linea, 1, -1);
            $dentroIdioma = ($idiomaActual === $idiomaDelUsuario);
        }
        if ($dentroIdioma) {
            $separetorLevelSentences = explode(":",  $linea);
            $levelFile = $separetorLevelSentences[0];
            if ($level === $levelFile) {
                $newContent .= $levelFile.":";
                $levelPhrases = trim($separetorLevelSentences[1]);
                $phrases = explode("*", $levelPhrases);
                $phrasesArray = [];
                for ($i = 0; $i < count($phrases); $i++){
                    if ($phraseSeleccionada == $phrases[$i]){
                        $filenameExtension = pathinfo($_FILES["uploadimage"]["name"], PATHINFO_EXTENSION);
                        $randomFilename = uniqid().".".$filenameExtension;
                        $uploaddir = "image/";
                        $uploadfile = $uploaddir . $randomFilename;
                        $tmp_name = $_FILES["uploadimage"]["tmp_name"];
                        $fileSaveSuccess = move_uploaded_file($tmp_name, $uploadfile);

                        $phrasesArray[] = $phrases[$i]."|".$randomFilename;
                    } else {
                        $phrasesArray[] = $phrases[$i];
                    }
                }
                $newContent .= implode("*", $phrasesArray);
                $newContent .= "\n";
                continue;
            }
        }
        $newContent .= $linea."\n";
    }
    file_put_contents("../sentences.txt", trim($newContent));
    fclose($sentencesFile);
    $_SESSION['imagenCreada'] = $fileSaveSuccess;
    header("Location: /admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        echo "<title>".$_SESSION['selected_lang']['UPLOAD_IMAGE_TAB']."</title>";
    ?>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="../media/lupa.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="uploadImage">
    <?php

    echo "<div id=''>";
    echo '<form method="get">';
    echo '<select name="lang" onchange="this.form.submit()">';
    $archivo = fopen('../idiomas.txt', 'r');
    if ($archivo) {
        while (($linea = fgets($archivo)) !== false) {
            $linea = trim($linea);
            if ($linea === '') continue;
            if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
                $idioma = substr($linea, 1, -1); // quita los corchetes
                if (isset($_GET['lang']) && $idioma === $_GET['lang']) {
                    echo "<option selected value='".$idioma."'>".$idioma."</option>";
                }
                else {
                    echo "<option value='".$idioma."'>".$idioma."</option>";
                }
            }
        }
        fclose($archivo);
    }
    echo '</select>';
    echo '</form>';
    echo "<form method='post' enctype='multipart/form-data'>";

    $sentencesLines = [];
    $dentroIdioma = false;

    $sentencesFile = fopen("../sentences.txt", "r+");
    while (!feof($sentencesFile)) {
        $linea = fgets($sentencesFile);
        if ($linea === false) continue;
        $linea = trim($linea);
        if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
            $idiomaActual = substr($linea, 1, -1);
            $dentroIdioma = (isset($_GET['lang']) && $idiomaActual === $_GET['lang']);
            continue;
        }
        if ($dentroIdioma && $linea !== '') {
            $sentencesLines[] = $linea;
        }
    }
    $difficulties = [];

    foreach ($sentencesLines as $sentenceValue) {
        $difficultyLevel = explode(":", $sentenceValue);
        $difficulty = $difficultyLevel[0];
        $phrases = $difficultyLevel[1];
        $separatePhrases = explode("*", $difficultyLevel[1]);
        $selectName = "frase" . $difficulty;
        echo "<select name='" . $selectName . "' id='" . $selectName . "'>";
        for ($i = 0; $i < count($separatePhrases); $i++) {
            if(str_contains($separatePhrases[$i], "|")){
                continue;
            }
            echo "<option value='" . $separatePhrases[$i] . "'>" . $separatePhrases[$i] . "</option>";
        }
        echo "</select>";

        $difficulties[] = $difficulty;
    }

    echo "<label for='selectdifficulty'>Selecciona el nivel de dificultad</label>";
    echo "<select name='selectdifficulty' id='selectdifficulty'>";
        echo "<option value='sencillo'>" . $_SESSION['lang_data']['DIFFICULTY_SIMPLE'] . "</option>";
        echo "<option value='normal'>" . $_SESSION['lang_data']['DIFFICULTY_NORMAL'] . "</option>";
        echo "<option value='experto'>" . $_SESSION['lang_data']['DIFFICULTY_EXPERT'] . "</option>";
    echo "</select>";
    //Este for recoge las dificultades directamente, pero en lugar de ser DIFFICULTY_SENCILLO es DIFFICULTY_SIMPLE. Entonces se tiene que poner tal cual.
    // for ($j = 0; $j < count($difficulties); $j++) {
    //     echo "<option value='" . $difficulties[$j] . "'>" . ucfirst($difficulties[$j]) . "</option>";
    // }
    
    echo "<input type='file' name='uploadimage'>";
    if (isset($_POST["selectdifficulty"]) && (!isset($_FILES['uploadimage']) || empty($_FILES['uploadimage']['name']))){
        echo 'Tienes que insertar una imagen';
    }
    echo "<button type='submit'>".$_SESSION['selected_lang']['UPLOAD_IMAGE_TAB']."</button>";
    echo "</form>";
    ?>
    <script>
        const showSelect = (difficultyName) => {
            Array.from(document.querySelectorAll("select"))
            .filter((selectElement) => selectElement.id.includes("frase"))
            .forEach(selectElement => {
               selectElement.classList.add("hidden");
               selectElement.required = false;
            });
            document.getElementById("frase"+difficultyName).classList.remove("hidden");
            document.getElementById("frase"+difficultyName).required = true;
        }
        const selectDifficultyValue = document.getElementById("selectdifficulty").value;
        showSelect(selectDifficultyValue);

        document.getElementById("selectdifficulty").addEventListener("change",(event) => {
            showSelect(event.target.value);
        })
    </script>
</body>

</html>