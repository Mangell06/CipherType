<?php
session_start();
if (!isset($_SESSION['lang_data'])) {
    header('Location: ../index.php');
    exit;
}
if (isset($_POST['newPhrase'])) {
    $sentencesFile = fopen("../sentences.txt", "r+");
    $level = $_POST["selectdifficulty"];
    $newPhrase = $_POST["newPhrase"];

    $newContent = "";
    $levelFound = false;
    $fileSaveSuccess;
    $randomFilename="";
    while (!feof($sentencesFile)) {
        $linea = fgets($sentencesFile);
        if ($linea === false || trim($linea) === '') continue;
        $linea = trim($linea);

        if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
            $idiomaActual = substr($linea, 1, -1);
            $dentroIdioma = ($idiomaActual === $_POST['selectlanguage']);
            $newContent .= $linea . "\n";
            continue;
        }

        if (!$dentroIdioma) {
            $newContent .= $linea . "\n";
            continue;
        }
        $separatorLevelSentences = explode(":", $linea, 2);
        $levelFile = $separatorLevelSentences[0];
        $levelPhrases = trim($separatorLevelSentences[1]);

        if ($levelFile == $level) {
             if (empty($levelPhrases)) {
                $levelPhrases = $newPhrase;
            } else {
                $levelPhrases .= "*" . $newPhrase;
            }
            if (isset($_FILES["image"]) && $_FILES["image"]["tmp_name"] != "" && is_uploaded_file($_FILES["image"]["tmp_name"])) {
                $filenameExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                $randomFilename = uniqid().".".$filenameExtension;
                $levelPhrases .= "|".$randomFilename;
                $uploaddir = "image/";
                $uploadfile = $uploaddir . $randomFilename;
                $tmp_name = $_FILES["image"]["tmp_name"];
                $fileSaveSuccess = move_uploaded_file($tmp_name, $uploadfile);
            }
        }

        $newContent .= $levelFile . ":" . $levelPhrases . "\n";
    }


    file_put_contents("../sentences.txt", trim($newContent));
    fclose($sentencesFile);
    $_SESSION['fraseCreada'] = true;
    $_SESSION['imagenCreada'] = $fileSaveSuccess;
    $_SESSION['last_sentence_added'] = $newPhrase . ($randomFilename ? "|" . $randomFilename : "");
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
    <?php
        echo "<title>".$_SESSION['lang_data']['TITLE_ADD_PHRASE']."</title>";
    ?>
</head>
<body class="createSentences">
   <div class="toolscontaineradmin">
    <div class="createSentence">
        <?php
            echo "<h1>".$_SESSION['lang_data']['TITLE_ADD_PHRASE']."</h1>";
        ?>
        <form action="create_sentence.php" method="post" enctype="multipart/form-data">
        <?php
            echo '<label for="selectdifficulty">' . $_SESSION['lang_data']['TEXT_SELECT_DIFICULTY'] . '</label>';
        ?>
            <select name="selectdifficulty" id="selectdifficulty">
            <?php
                echo '<option value="sencillo">' . $_SESSION['lang_data']['DIFFICULTY_SIMPLE'] . '</option>';
                echo '<option value="normal">' . $_SESSION['lang_data']['DIFFICULTY_NORMAL'] . '</option>';
                echo '<option value="experto">' . $_SESSION['lang_data']['DIFFICULTY_EXPERT'] . '</option>';
            ?>
            </select>
            <?php
                echo "<label for='selectlanguage'>".$_SESSION['lang_data']['TEXT_SELECT_LANGUAGE']."</label>";
            ?>
            
            <select name="selectlanguage" id="selectlanguage">
                <?php
                    echo "<option value='CATALÁN'>".$_SESSION['lang_data']['LANGUAGE_CATALAN']."</option>";
                    echo "<option value='CASTELLANO'>".$_SESSION['lang_data']['LANGUAGE_SPANISH']."</option>";
                    echo "<option value='ENGLISH'>".$_SESSION['lang_data']['LANGUAGE_ENGLISH']."</option>"
                ?>
                
            </select>
            <?php
                echo '<label for="newPhrase">' . $_SESSION['lang_data']['TEXT_NEW_PHRASE'] . '</label>';
            ?>
            <input type="text" name="newPhrase" id="newPhrase">

            <?php
                echo "<label for='newImage'>".$_SESSION['lang_data']['UPLOAD_IMAGE_TAB']."</label>";
            ?>
            <input type="file" name="image" accept="image/*">
            <?php
                echo '<button type="submit" id="add">' . $_SESSION['lang_data']['TEXT_ADD_PHRASES'] . '</button>';
            ?>
        </form>
    </div>
</div>
<script>
    const add = document.getElementById("add");
    const newPhrase = document.getElementById("newPhrase");
    const errorMsg = document.createElement("p");
    errorMsg.className = "error";
    errorMsg.style.display = "none"; 
    errorMsg.textContent = "<?php $_SESSION['lang_data']['TEXT_ADD_PHRASES']?>";
    newPhrase.insertAdjacentElement("afterend", errorMsg);

    document.addEventListener("keydown", (event)=>{
        if (event.target.nodeName === "INPUT"){
            return;
        }
        if ((event.key).toLocaleLowerCase() === "a"){
            if (add == null){
                return;
            }
            const phraseValue = newPhrase.value.trim();
            const contieneLetras = /[a-záéíóúüñ]/i.test(phraseValue);
            if (phraseValue === "" || !contieneLetras){
                errorMsg.style.display = "block";
                event.preventDefault();
                return;
            } else {
                errorMsg.style.display = "none";
            }

            if (newPhrase.validity.valid){
                add.classList.add("highlightButtonTextAdmin");
                setTimeout(() => { add.click(); }, 1000);
            } else{
                newPhrase.reportValidity();
                event.preventDefault();
            }
        }
    });

    add.addEventListener("click", (event) => {
        const phraseValue = newPhrase.value.trim();
        const contieneLetras = /[a-záéíóúüñ]/i.test(phraseValue);
        if (phraseValue === "" || !contieneLetras){
            errorMsg.style.display = "block";
            event.preventDefault();
            event.stopPropagation();
            return;
        } else {
            errorMsg.style.display = "none";
        }
    });
</script>
</body>
</html>
