<?php
    session_start();
    $sentencesFile = fopen("../sentences.txt","r+"); //abrir archivo
    $phraseIndex = $_POST["fraseIndex"]; // coger por post la frase
    $level = $_POST["selectdifficulty"]; //coger por post el nivel
    $newContent = "";
    while(!feof($sentencesFile)) {
        $linea = fgets($sentencesFile);
        if ($linea === false || trim($linea) === '') continue;
        $linea = trim($linea);

        if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
            $idiomaActual = substr($linea, 1, -1);
            $dentroIdioma = ($idiomaActual === $_SESSION['selected_lang']);
            $newContent .= $linea . "\n";
            continue;
        }

        if (!$dentroIdioma) {
            $newContent .= $linea . "\n";
            continue;
        }
        $separetorLevelSentences = explode(":", $linea,2);
        $levelFile = $separetorLevelSentences[0];
        $levelPhrases = trim($separetorLevelSentences[1]);

        $newContent .= $levelFile.":";
        
        $phrases = explode("*", $levelPhrases);
        $phrasesArray = [];
        for ($i = 0; $i < count($phrases); $i++){
            if ($i != $phraseIndex || $level != $levelFile){
                $phrasesArray[] = $phrases[$i];
            } else{
                $parts = explode("|", $phrases[$i]);
                if (isset($parts[1]) && file_exists("image/" . $parts[1])) {
                    unlink("image/" . $parts[1]);
                }
            }
        }
        $newContent .= implode("*", $phrasesArray);
        $newContent .= "\n";
    }

    file_put_contents("../sentences.txt", trim($newContent));
    fclose($sentencesFile); //cerrar archivo
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form id="formulario" action="/admin/index.php" method="post">
        <input name="selectdifficulty" type='hidden' value='<?php echo $level ?>'>
        <input name="deleteSuccess" type='hidden' value='true'>
    </form>
    <script>
        document.getElementById("formulario").submit();
    </script>
</body>
</html>