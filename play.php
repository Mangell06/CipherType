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
        
        if (!function_exists('getRandomPhrase')) {
            function getRandomPhrase($stringFrases){
                $textoSubstringTrim = trim($stringFrases);
                $array = explode("*", $textoSubstringTrim);
                $randomPhraseKey = array_rand($array, 1);
                return $array[$randomPhraseKey];
            }
        }
        
        if (isset($difficulty) && $difficulty === "sencillo") {
            echo getRandomPhrase(substr($sentencesLines[0], 9));
        } else if (isset($difficulty) && $difficulty === "normal") {
            echo getRandomPhrase(substr($sentencesLines[1], 7));
        } else if (isset($difficulty) && $difficulty === "experto") {
            echo getRandomPhrase(substr($sentencesLines[2], 8));
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
            funcionar = true;
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
                    points -= 100
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
                    if (indexLetter < frase.length && frase[indexLetter] !== " ") {
                        showPhrase();  
                    }
                    if (indexLetter >= frase.length) {
                        endGame();
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
    }})

    </script>
</body>
</html>