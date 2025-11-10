<?php
session_start();
if (isset($_POST['inname'])) {
    $_SESSION['name'] = $_POST['inname'];
}
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
            echo "<p>Nombre: ".$_SESSION['name']."</p>";
            echo "<button type='submit' id='closeSession' onclick='destroySession()'>Cerrar sesión</button>";
            echo "</div>";
        }
    ?>
    <div id="fraseCounter">
        Frase <span id="currentFrase">1</span> de <span id="totalFrases"><?php
        $difficulty = $_POST["indifficulty"] ?? "sencillo";
        $numFrases = 3;

        if ($difficulty === "normal") {
            $numFrases = 4;
        } else if ($difficulty === "experto") {
            $numFrases = 5;
        }
        echo $numFrases;
        ?></span>
    </div>
    <img class="mesa" src="media/mesa.jpg" alt="Imagen de una mesa">
    <div class="machine">
        <div class="textos">
            <p id="timer"></p>
            <p id="textStartInformation">Escribe la siguiente frase: </p>
            <div class="text">
            </div>
        </div>
        <img src="media/typingmachine.png" alt="Imagen de máquina de escribir">

    </div>
    <img id="lupa" src="media/lupa_easteregg.png" alt="lupa easteregg">
    <img id="vela" src="media/velaEasterEgg.png" alt="vela Easter Egg">
    <img id="libro" src="media/libroEasterEgg.png" alt="libro Easter Egg">
    <img id="sombrero" src="media/sombreroSherlock.png" alt="sombrero Easter Egg">
    <img id="sherlock" class="invisible" src="media/sherlockHolmes.png" alt="sherlock">
    <div class="invisible listaEntera">
        <p>Lupa</p>
        <p>Vela</p>
        <p>Libro</p>
        <p>Sombrero</p>

    </div>
    <form id="endForm" action="gameover.php" method="POST" style="display:none;">
        <input type="hidden" name="points" id="pointsField">
    </form>
    <script>
        const closeSession = document.getElementById("closeSession");
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }

        const correctSound = new Audio('media/correctchoice.mp3');
        const wrongSound = new Audio('media/wrongchoice1.mp3');

        correctSound.load();
        wrongSound.load();

        let points = 0;
        let win = false;
        let eventCont = 4;
        let frases = <?php
        $sentencesFile = fopen("sentences.txt", "r");
        $sentencesLines = [];
        while (!feof($sentencesFile)) {
            array_push($sentencesLines, fgets($sentencesFile));
        }
        fclose($sentencesFile);

        function getRandomPhrases($stringFrases, $count) {
            $textoSubstringTrim = trim($stringFrases);
            $array = explode("*", trim($stringFrases));
            
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

        echo json_encode($frases);
        ?>;
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
        const nombres = ["Lupa", "Vela", "Libro", "Sombrero"];

        ids.forEach((id, index) => {
            const element = document.getElementById(id);
            element.addEventListener("click", () => {
                element.classList.add("invisible");
                alert(`Has clicado el objeto ${nombres[index]}`);
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
                        alert("Gracias Watson por encontrar todos mis objetos, te obsequio con 7000 puntos más.");
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

        const showPhrase = () => { startTimer(); 
            const span = document.getElementById("letter"+indexLetter); 
            span.className = "highlight"; 
        };

        const render = () => {
            div.innerText = "";
            for (let i = 0; i < fraseActual.length; i++) {
                const span = document.createElement("span");
                span.id = "letter" + i;
                span.textContent = fraseActual[i];
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

        let pendingAccent = "";
        let indexLetter = 0;
        let funcionar = false;

        function checkInput(isMayus, inletter) {
            const letter = document.getElementById("letter"+indexLetter);
            return (isMayus && inletter.toUpperCase() === letter.textContent) || inletter.toLowerCase() === letter.textContent;
        }

        function isCorrectLetter(iscorrect, isspace) {
            const letter = document.getElementById("letter"+indexLetter);
            if (!isspace) {
                letter.className = iscorrect ? "correct" : "error";
                points += iscorrect ? 100 : -100;
                if (iscorrect) {
                    correctSound.play();
                } else {
                    wrongSound.play();
                }
            } else {
                if (letter.textContent != " ") {
                    letter.className = "error";
                    wrongSound.play();
                    points -= 100;
                } else {
                    correctSound.play();
                    points += 100;   
                }
            }
        }

        function endGame() {
            document.getElementById("pointsField").value = points;
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

                if (/^\p{L}$| |,$/u.test(e.key)) {
                    let iscorrect = e.shiftKey;
                    iscorrect = checkInput(iscorrect, inputChar);
                    isCorrectLetter(iscorrect, e.key === " " ? true : false);
                    indexLetter++;
                    
                    if (indexLetter < fraseActual.length && fraseActual[indexLetter] !== " ") {
                        showPhrase();  
                    }
                    
                    if (indexLetter >= fraseActual.length) {
                        funcionar = false;
                        setTimeout(() => {
                            prepareNextPhrase();
                        }, 500);
                    }
                }
            }
        });

        document.addEventListener("keydown", (event)=>{
            if ((event.key).toLocaleLowerCase() === "c" && event.ctrlKey){
                closeSession.classList.add("highlightButtonText");
                setTimeout(() => {
                    closeSession.click();
                }, "1000");
            }
        });

       startTimer();
    </script>
</body>
</html>