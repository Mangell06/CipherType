<?php
session_start();
$_SESSION['allow_gameover'] = true;
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
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="/media/lupa.ico">
   
    <style>
        .highlight {
            color: #ffeb3b;
            text-decoration: underline;
            font-weight: bold;
            text-shadow: 0 0 10px #ffeb3b;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {
            0%, 100% {
                text-shadow: 0 0 5px #fff, 0 0 10px #ffeb3b;
            }

            50% {
                text-shadow: 0 0 50px #ffeb3b, 0 0 35px #ffeb3b;
            }
        }
    </style>

</head>

<body class="play">
    <img class="mesa" src="./media/mesa.jpg" alt="Imagen de una mesa">
    <div class="machine">
        <div class="textos">
            <p id="timer"></p>
            <p id="textStartInformation" class="hidden">Escribe la siguiente frase: </p>
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
        const correctSound = new Audio('media/correctchoice.mp3')
        const wrongSound = new Audio('media/wrongchoice1.mp3')

        correctSound.load();
        wrongSound.load();

        const p = document.getElementById("timer");
        const pInformation = document.getElementById("textStartInformation");
        const div = document.querySelector("div.text");
        const listaDiv = document.querySelector("div.invisible");
        const listaP = listaDiv.querySelectorAll("p");
        const imgSherlock = document.getElementById("sherlock");
        const ids = ["lupa", "vela", "libro", "sombrero"];
        const nombres = ["Lupa", "Vela", "Libro", "Sombrero"];
        win = false;
        let eventCont = 4;
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
                    const puntos = "<?php
                        session_start();
                        $_SESSION['poinst'] += 7000;
                    ?>";
                }
            });
        });

        let cont = 4;
        let points = 0;
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
        }, 750)

        const afterInterval = () => {
            p.style.display = "none";
            pInformation.classList.remove("hidden");
            render();
        }

        const frase = "<?php
        $randomPhrase = "";
        $difficulty = $_POST["indifficulty"];
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
        if (isset($_POST['inname'])) {
            $_SESSION['name'] = $_POST['inname'];
        }
        ?>";

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
        }

        let indexLetter = 0;

        

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
                    points -= 100
                } else {
                    points += 100;   
                }
            }

        }

        function endGame() {
            document.getElementById("pointsField").value = points;
            document.getElementById("endForm").submit();
        }

        document.addEventListener('keyup',(e) => {
            if (/^[A-Za-z ,]$/.test(e.key)) {
                let iscorrect = e.shiftKey;
                iscorrect = checkInput(iscorrect, e.key);
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
    </script>
</body>
</html>