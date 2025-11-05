<?php
session_start();
if (isset($_POST['newPhrase'])) {
    $sentencesFile = fopen("../sentences.txt", "r+");
    $level = $_POST["selectdifficulty"];
    $newPhrase = $_POST["newPhrase"];

    $newContent = "";
    $levelFound = false;


    while (!feof($sentencesFile)) {
        $separatorLevelSentences = explode(":", fgets($sentencesFile), 2);
        $levelFile = $separatorLevelSentences[0];
        $levelPhrases = trim($separatorLevelSentences[1]);

        if ($levelFile == $level) {
             if (empty($levelPhrases)) {
                $levelPhrases = $newPhrase;
            } else {
                $levelPhrases .= "*" . $newPhrase;
            }
        }

        $newContent .= $levelFile . ":" . $levelPhrases . "\n";
    }


    file_put_contents("../sentences.txt", trim($newContent));
    fclose($sentencesFile);
    $_SESSION['fraseCreada'] = true;
    header("Location: /admin/index.php");
   
    exit;

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="../media/lupa.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
   <div class="toolscontainer">
    <div class="createSentence">
        <h1>AGREGAR FRASE</h1>
        <form action="create_sentence.php" method="post">
            <label for="selectdifficulty">Selecciona el nivel de dificultad</label>
            <select name="selectdifficulty" id="selectdifficulty" required>
                <option value="sencillo">Sencillo</option>
                <option value="normal">Normal</option>
                <option value="experto">Experto</option>
            </select>

            <label for="newPhrase">Nueva frase</label>
            <input type="text" name="newPhrase" id="newPhrase" required>

            <button type="submit">Añadir Frase</button>
        </form>
    </div>
</div>
</body>
</html>
