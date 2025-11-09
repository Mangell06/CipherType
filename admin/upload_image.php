<?php
session_start();
if (isset($_FILES['uploadimage']) && !empty($_FILES['uploadimage']['name'])) {
    $sentencesFile = fopen("../sentences.txt", "r+");
    $level = $_POST["selectdifficulty"];
    $phraseSeleccionada = $_POST["frase".$level];
    $newContent = "";
    $fileSaveSuccess;
    while(!feof($sentencesFile)) {
        $separetorLevelSentences = explode(":", fgets($sentencesFile),2);
        $levelFile = $separetorLevelSentences[0];
        $levelPhrases = trim($separetorLevelSentences[1]);

        $newContent .= $levelFile.":";
        
        $phrases = explode("*", $levelPhrases);
        $phrasesArray = [];
        for ($i = 0; $i < count($phrases); $i++){
            if ($level == $levelFile && $phraseSeleccionada == $phrases[$i]){
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
    <title>Insertar imagen</title>
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
    echo "<form action='upload_image.php' method='post' enctype='multipart/form-data'>";
    $sentencesFile = fopen("../sentences.txt", "r+");
    $difficulties = [];
    
    while (!feof($sentencesFile)) {
        $difficultyLevel = explode(":", fgets($sentencesFile));
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
    for ($j = 0; $j < count($difficulties); $j++) {
        echo "<option value='" . $difficulties[$j] . "'>" . ucfirst($difficulties[$j]) . "</option>";
    }
    echo "</select>";
    echo "<input type='file' name='uploadimage'>";
    if (isset($_POST["selectdifficulty"]) && (!isset($_FILES['uploadimage']) || empty($_FILES['uploadimage']['name']))){
        echo 'Tienes que insertar una imagen';
    }
    echo "<button type='submit'>Insertar imagen</button>";
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