<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="/media/lupa.ico">
</head>

<body class="play">
    <img class="mesa" src="media/mesa.jpg" alt="Imagen de una mesa">
    <div class="machine">
        <div class="textos">
            <p id="timer"></p>
            <p id="textStartInformation" class="hidden">Escribe la siguiente frase: </p>
            <div class="text">
            </div>
        </div>
        <img src="media/typingmachine.png" alt="Imagen de máquina de escribir">
    </div>

    <script>
        const p = document.getElementById("timer");
        const pInformation = document.getElementById("textStartInformation");
        const div = document.querySelector("div.text");
        let cont = 4;
        const interval = setInterval(() => {
            if (cont <= 0) {
                clearInterval(interval);
                afterInterval();
                return;
            }
            cont--;
            if (cont === 0) {
                p.innerText = "YA!";
                return;
            }
            p.innerText = cont;
        }, 2000)

        const afterInterval = () => {
            p.style.display = "none";
            pInformation.classList.remove("hidden");
            render();
        }

        const frase = "<?php
        $randomPhrase = "";
        $difficulty = $_POST['indifficulty'];
        $sentencesFile = fopen("sentences.txt", "r");
        $sentencesLines = [];
        while (!feof($sentencesFile)) {
            array_push($sentencesLines, fgets($sentencesFile));
        }
        fclose($sentencesFile);

        $textoSencilloSubstringTrim = trim(substr($sentencesLines[0], 9));
        $separateSentences = explode("*", $textoSencilloSubstringTrim);

        function getRandomPhrase($stringFrases){
            $textoSubstringTrim = trim($stringFrases);
            $array = explode("*", $textoSubstringTrim);
            $randomPhraseKey = array_rand($array, 1);
            return $array[$randomPhraseKey];
        }

        if (isset($difficulty) && $difficulty === "sencillo") {
            echo getRandomPhrase(substr($sentencesLines[0], 9));
        } else if (isset($difficulty) && $difficulty === "normal") {
            echo getRandomPhrase(substr($sentencesLines[1], 7));
        } else if (isset($difficulty) && $difficulty === "experto") {
            echo getRandomPhrase(substr($sentencesLines[2], 8));
        }
        ?>";

        const render = () => {
            div.innerText = "";
            for (let i = 0; i < frase.length; i++) {
                const span = document.createElement("span");
                span.textContent = frase[i];
                div.appendChild(span);
            }
        }
    </script>
    <?php
    ?>
</body>

</html>