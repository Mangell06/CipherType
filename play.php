<?php
session_start();
if (isset($_POST['inname'])) {
    $_SESSION['name'] = $_POST['inname'];
}
if (!isset($_SESSION['lang_data']) || !isset($_POST['indifficulty'])) {
    header('Location: index.php');
    exit;
}

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

$numFrases = 3;
if ($difficulty === "normal") {
    $numFrases = 4;
} else if ($difficulty === "experto") {
    $numFrases = 5;
}

function getRandomPhrases($stringFrases, $count) {
    $textoSubstringTrim = trim($stringFrases);
    $array = explode("*", $textoSubstringTrim);
    
    if (count($array) < $count) {
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $result[] = $array[$i % count($array)];
        }
        return $result;
    }
    
    $randomKeys = array_rand($array, $count);
    if (!is_array($randomKeys)) {
        $randomKeys = [$randomKeys];
    }
    
    $result = [];
    foreach ($randomKeys as $key) {
        $result[] = $array[$key];
    }
    return $result;
}

$frases = [];
if ($difficulty === "sencillo") {
    $frases = getRandomPhrases(substr($sentencesLines[0], 9), $numFrases);
} else if ($difficulty === "normal") {
    $frases = getRandomPhrases(substr($sentencesLines[1], 7), $numFrases);
} else if ($difficulty === "experto") {
    $frases = getRandomPhrases(substr($sentencesLines[2], 8), $numFrases);
}

$frasesProcesadas = [];
foreach ($frases as $frase) {
    $fraseSplit = explode("|", $frase);
    $frasesProcesadas[] = [
        'texto' => $fraseSplit[0],
        'imagen' => isset($fraseSplit[1]) ? $fraseSplit[1] : ""
    ];
}

$frasesJson = json_encode($frasesProcesadas);
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
    <div id="fraseCounter">
        Frase <span id="currentFrase">1</span> de <span id="totalFrases"><?php echo $numFrases; ?></span>
    </div>
    <img class="mesa" src="media/mesa.jpg" alt="Imagen de una mesa">
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
        <img id="imagePhrase" class="imagePhrase hidden" src="" alt="">
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
        const imagePhrase = document.getElementById("imagePhrase");
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
            } else {
                inverseCountPulsation = 1;
                countPulsation = 3;
            }
            if (countPulsation < 0) {
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
        let win = false;
        let eventCont = 4;
        let frases = <?php echo $frasesJson; ?>;
        let fraseActualIndex = 0;
        let fraseActual = frases[fraseActualIndex];
        let totalFrases = frases.length;

        const p = document.getElementById("timer");
        const pInformation = document.getElementById("textStartInformation");
        const div = document.querySelector("div.text");
        const listaDiv = document.querySelector("div.invisible");
        const listaP = listaDiv.querySelectorAll("p");
        const imgSherlock = document.getElementById("sherlock");
        const fraseCounter = document.getElementById("fraseCounter");
        const currentFraseSpan = document.getElementById("currentFrase");
        const totalFrasesSpan = document.getElementById("totalFrases");

        totalFrasesSpan.textContent = totalFrases;
        currentFraseSpan.textContent = fraseActualIndex + 1;

        const ids = ["lupa", "vela", "libro", "sombrero"];
        const nombres = <?php echo json_encode($_SESSION['lang_data']['ELEMENTS_EASTEREGG']); ?>;

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

        function startTimer() {
            p.style.display = "block";
            pInformation.style.display = "none";
            fraseCounter.style.display = "none";
            div.innerText = "";
            imagePhrase.classList.add("hidden");
            
            let cont = 3;
            p.innerText = cont;
            
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
            }, 750);
        }

        function prepareNextPhrase() {
            fraseActualIndex++;
            
            if (fraseActualIndex < totalFrases) {
                fraseActual = frases[fraseActualIndex];
                currentFraseSpan.textContent = fraseActualIndex + 1;
                indexLetter = 0;
                funcionar = false;
                
                setTimeout(() => {
                    startTimer();
                }, 1000);
            } else {
                endGame();
            }
        }

        const showPhrase = () => { 
            const span = document.getElementById("letter"+indexLetter); 
            span.className = "highlight"; 
        };

        const render = () => {
            div.innerText = "";
            
            // Mostrar imagen si existe
            if (fraseActual.imagen && fraseActual.imagen !== "") {
                imagePhrase.src = '/admin/image/' + fraseActual.imagen;
                imagePhrase.classList.remove("hidden");
            } else {
                imagePhrase.classList.add("hidden");
            }
            
            for (let i = 0; i < fraseActual.texto.length; i++) {
                const span = document.createElement("span");
                span.id = "letter" + i;
                span.textContent = fraseActual.texto[i];
                div.appendChild(span);
            }
            showPhrase();
            funcionar = true;
            
            pInformation.style.display = "block";
            fraseCounter.style.display = "block";
        }

        const afterInterval = () => {
            p.style.display = "none";
            render();
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
                    points -= 100
                    letrasErroneas += 1;
                } else {
                    correctSound.play();
                    points += 100 * multiplicador;
                    letrasAcertadas += 1;   
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
                if (indexLetter < fraseActual.texto.length && fraseActual.texto[indexLetter] !== " ") {
                    showPhrase();  
                }
                if (indexLetter >= fraseActual.texto.length) {
                    funcionar = false;
                    setTimeout(() => {
                        prepareNextPhrase();
                    }, 500);
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

        startTimer();
    </script>
</body>
</html>