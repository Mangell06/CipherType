<?php
session_start();
if (isset($_POST['inname'])) {
    $_SESSION['name'] = $_POST['inname'];
}
if (!isset($_POST['indifficulty'])) {
    header('Location: index.php');
    exit;
}
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

$randomPhrase = "";
$difficulty = $_POST["indifficulty"];
$sentencesLines = [];
$dentroIdioma = false;

$sentencesFile = fopen("sentences.txt", "r");
while (!feof($sentencesFile)) {
    $linea = fgets($sentencesFile);
    if ($linea === false) continue;
    $linea = trim($linea);
    if (strpos($linea, '[') === 0 && substr($linea, -1) === ']') {
        $idiomaActual = substr($linea, 1, -1);
        $dentroIdioma = ($idiomaActual === $_SESSION['selected_lang']);
        continue;
    }
    if ($dentroIdioma && $linea !== '') {
        $sentencesLines[] = $linea;
    }
}
fclose($sentencesFile);


$textoSencilloSubstringTrim = trim(substr($sentencesLines[0], 9));
$separateSentences = explode("*", $textoSencilloSubstringTrim);

if (!function_exists('getRandomPhrase')) {
    function getRandomPhrase($stringFrases){
        $textoSubstringTrim = trim($stringFrases);
        $array = explode("*", $textoSubstringTrim);
        $randomPhraseKey = array_rand($array, 1);
        return $array[$randomPhraseKey];
    }
}



$phraseWithImage;
if (isset($difficulty) && $difficulty === "sencillo") {
    $phraseWithImage = getRandomPhrase(substr($sentencesLines[0], 9));
} else if (isset($difficulty) && $difficulty === "normal") {
    $phraseWithImage = getRandomPhrase(substr($sentencesLines[1], 7));
} else if (isset($difficulty) && $difficulty === "experto") {
    $phraseWithImage = getRandomPhrase(substr($sentencesLines[2], 8));
}

$phraseWithImageSplit = explode("|", $phraseWithImage);
$imageName = isset($phraseWithImageSplit[1]) ? $phraseWithImageSplit[1] : "";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
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
    ?>
        <div id='bonusSpecialWrapper' class="bonusWrapper hideBonus">
        <div class="mainBonus">
        <div class="containerBonus">
        <p id="multiplicatorBonus">X3</p>
        <progress id="file" max="100" value="100">3S</progress>
        </div>
        </div>
        </div>
    <?php
        echo '<img class="mesa" src="media/mesa.jpg" alt="'. $_SESSION['lang_data']['ALT_MESA'].'">';
    ?>
    <div class="machine">
        <?php
        echo $imageName == "" ? "" : "<img id='image' class='imagePhrase hidden' src='/admin/image/$imageName'>";
        ?>
        <div class="textos">
            <p id="timer"></p>
            <?php
            echo '<p id="textStartInformation" class="hidden">'. $_SESSION['lang_data']['SUBTITLE_INGAME'].'</p>';
            ?>
            
            <div class="text">
            </div>
        </div>
        <?php
            echo '<img class="typingMachine" src="media/typingmachine.png" alt="'. $_SESSION['lang_data']['ALT_MACHINE'].'">';
        ?>
    </div>
    <?php
    echo '<img id="lupa" src="media/lupa_easteregg.png" alt="'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][0].' Easter Egg">';
    echo '<img id="vela" src="media/velaEasterEgg.png" alt="'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][1].' Easter Egg">';
    echo '<img id="libro" src="media/libroEasterEgg.png" alt="'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][2].' Easter Egg">';
    echo '<img id="sombrero" src="media/sombreroSherlock.png" alt="'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][3].' Easter Egg">';
    echo '<img id="sherlock" class="invisible" src="media/sherlockHolmes.png" alt="sherlock">';
    ?>
    <div class="invisible listaEntera">
    <?php
         echo '<p>'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][0].'</p>';
         echo '<p>'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][1].'</p>';
         echo '<p>'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][2].'</p>';
         echo '<p>'. $_SESSION['lang_data']['ELEMENTS_EASTEREGG'][3].'</p>';
    ?>
    </div>
    <div id="temp"></div>
    <form id="endForm" action="gameover.php" method="POST" style="display:none;">
        <input type="hidden" name="points" id="pointsField">
        <input type="hidden" name="temp" id="tempField">
    </form>
    <script>
        let pendingAccent = "";
        let indexLetter = 0;
        let funcionar = false;
        let multiplicador = 1;
        let letrasAcertadas = 0;
        let letrasErroneas = 0;
        const closeSession = document.getElementById("closeSession");
        const image = document.getElementById("image");
        const tempDiv = document.getElementById("temp")
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }

        let temp = -4
        setInterval(()=>{
            temp += 1;
            if (temp < 0){tempDiv.innerText = "00:00:00";}
            else{tempDiv.innerText = formatearTiempo(temp);}
        },1000)
        
        let countPulsation = 3
        const progress = document.getElementById("file");
        let inverseCountPulsation = 1;
        setInterval(()=>{
            if (letrasAcertadas > 5) {
                countPulsation -= 1;
                progress.value = 100 - 33 * inverseCountPulsation;
                inverseCountPulsation += 1;
            }
            if (countPulsation <= 0) {
                inverseCountPulsation = 1;
                countPulsation = 3;
                const containerBonus = document.getElementById('bonusSpecialWrapper');
                const multiplicatorP = document.getElementById('multiplicatorBonus');
                multiplicador = 0;
                countPulsation = 3;
                letrasAcertadas = 0;
                letrasErroneas = 0;
                if (letrasAcertadas >= 5) {
                    multiplicatorP.textContent = "X"+multiplicador;
                    containerBonus.classList.remove("hideBonus");
                } else {
                    multiplicatorP.textContent = "X"+multiplicador;
                    containerBonus.classList.add("hideBonus");
                }
            }
        },1000);

        function formatearTiempo(segundos) {
            const horas = Math.floor(segundos / 3600);
            const minutos = Math.floor((segundos % 3600) / 60);
            const segundosRestantes = segundos % 60;

            const formatoHoras = String(horas).padStart(2, '0');
            const formatoMinutos = String(minutos).padStart(2, '0');
            const formatoSegundos = String(segundosRestantes).padStart(2, '0');

            return `${formatoHoras}:${formatoMinutos}:${formatoSegundos}`;
        }

        const correctSound = new Audio('media/correctchoice.mp3');
        const wrongSound = new Audio('media/wrongchoice1.mp3');

        correctSound.load();
        wrongSound.load();

        let points = 0;
        const p = document.getElementById("timer");
        const pInformation = document.getElementById("textStartInformation");
        const div = document.querySelector("div.text");
        const listaDiv = document.querySelector("div.invisible");
        const listaP = listaDiv.querySelectorAll("p");
        const imgSherlock = document.getElementById("sherlock");
        const ids = ["lupa", "vela", "libro", "sombrero"];
        const nombres = <?php echo json_encode($_SESSION['lang_data']['ELEMENTS_EASTEREGG']); ?>;
        win = false;
        let eventCont = 4;
        ids.forEach((id, index) => {
            const element = document.getElementById(id);
            element.addEventListener("click", () => {
                element.classList.add("invisible");
                alert(`<?php echo $_SESSION['lang_data']['TEXT_GET_ELEMENT']; ?> ${nombres[index]}`);
                eventCont--;
                if (eventCont <= 3) {
                    listaDiv.classList.remove("invisible");

                }
                if (eventCont <= 0) {
                    imgSherlock.classList.remove("invisible");
                    win = true;
                }

                listaP.forEach(pItem => {
                    if (pItem.textContent.toLowerCase() === nombres[index].toLowerCase()) {
                        pItem.classList.remove("invisible");
                        pItem.classList.add("found");
                    }
                });
                if (win) {
                    setTimeout(() => {
                        alert(<?php echo json_encode($_SESSION['lang_data']['COMPLETE_EASTEREGG']); ?>);
                    }, 1000);
                    points += 7000;
                }
            });
        });

        let cont = 4;
        const interval = setInterval(() => {
            if (cont <= 0) {
                clearInterval(interval);
                afterInterval();
                return;
            }
            cont--;
            if (cont === 0) {
                p.innerText = <?php echo json_encode($_SESSION['lang_data']['TEXT_START'] . '!'); ?>;
                return;
            }
            p.innerText = cont;
        }, 750)

        const afterInterval = () => {
            p.style.display = "none";
            pInformation.classList.remove("hidden");
            image?.classList.remove("hidden");
            render();
        }

        <?php
        echo 'const frase = "';
        echo $phraseWithImageSplit[0].'";';
        // echo 'const imageName = "'.(isset($phraseWithImageSplit[1]) ? $phraseWithImageSplit[1] : "").'";';
        ?>

         const showPhrase = () => { const span = document.getElementById("letter"+indexLetter); span.className = "highlight"; };

         const render = () => {
            div.innerText = "";
            for (let i = 0; i < frase.length; i++) {
                const span = document.createElement("span");
                span.id = "letter" + i;
                span.textContent = frase[i];
                div.appendChild(span);
            }
            showPhrase();
            funcionar = true;
        }

        function checkInput(isMayus, inletter) {
            const letter = document.getElementById("letter"+indexLetter);
            if (/’|‘/.test(letter.textContent)) {
                comparate = "'";
            } else if (/“|”/.test(letter.textContent)) {
                comparate = '"';
            } else {
                comparate = letter.textContent;
            }
            return (isMayus && inletter.toUpperCase() === comparate) || inletter.toLowerCase() === comparate;
        }

        function isCorrectLetter(iscorrect, isspace) {
            const anteriorMultiplicator = multiplicador;
            const letter = document.getElementById("letter"+indexLetter);
            if (!isspace) {
                letter.className = iscorrect ? "correct" : "error";
                points += iscorrect ? 100 * multiplicador : -100;
                if (iscorrect) {
                    correctSound.play();
                    letrasAcertadas += 1;
                } else {
                    wrongSound.play();
                    letrasErroneas += 1;
                }
            } else {
                if (letter.textContent != " ") {
                    letter.className = "error";
                    wrongSound.play();
                } else {
                    correctSound.play();
                }
            }
            const containerBonus = document.getElementById('bonusSpecialWrapper');
            if (letrasErroneas === 3) {
                letrasAcertadas = letrasAcertadas < 5 ? 0 : letrasAcertadas - 5;
                letrasErroneas = 0;
            }
            const multiplicatorP = document.getElementById('multiplicatorBonus');
            multiplicador = Math.floor(letrasAcertadas / 5);
            if (letrasAcertadas >= 5) {
                multiplicatorP.textContent = "X"+multiplicador;
                containerBonus.classList.remove("hideBonus");
            } else {
                multiplicatorP.textContent = "X"+multiplicador;
                containerBonus.classList.add("hideBonus");
            }
            if (anteriorMultiplicator !== multiplicador) {
                countPulsation = 3;
                inverseCountPulsation = 1;
                progress.value = 100;
            }
        }

        function endGame() {
            document.getElementById("pointsField").value = points;
            document.getElementById("tempField").value = temp;
            document.getElementById("endForm").submit();
        }

        document.addEventListener('keyup',(e) => {
            if (funcionar) {
                if (e.key === "Dead") {
                    pendingAccent = e.code;
                    return;
                }

                let inputChar = e.key;

                if (pendingAccent) {
                    inputChar = (inputChar + "\u0301").normalize("NFC");
                    pendingAccent = "";
                }
                if (
                (e.key === "Shift" && !e.ctrlKey) ||
                (e.key === "Control" && !e.shiftKey)
                ) return;
                let iscorrect = e.shiftKey;
                iscorrect = checkInput(iscorrect, inputChar);
                isCorrectLetter(iscorrect, e.key === " " ? true : false);
                indexLetter++;
                if (indexLetter < frase.length && frase[indexLetter] !== " ") {
                    showPhrase();  
                }
                if (indexLetter >= frase.length) {
                    endGame();
                }
            }
        });

        document.addEventListener("keydown", (event)=>{
         if ((event.key).toLocaleLowerCase() === "c" && event.ctrlKey){
            closeSession.classList.add("highlightButtonText");
            setTimeout(() => {
                closeSession.click();
            }, "1000");
        }});

    </script>
</body>
</html>