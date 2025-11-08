<?php
    session_start();
    $name = $_SESSION['name'];
    $points = $_POST['points'];
    $temp = $_POST['temp'];
    if (!isset($name) || !isset($points)) {
        header("HTTP/1.1 403 Forbidden");
        header('Location: /errors/error403.php');
        exit();
    }
    $_SESSION["points"] = $points;
    $_SESSION["temp"] = $temp;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Over</title>
    <link rel="stylesheet" href="styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="media/lupa.ico">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
<body class="gameover sh-reveal">
    <?php
        if (isset($_SESSION['name'])) {
            echo "<div class='cancelSession sh-reveal'>";
            echo "<p class='sh-highlight'>" . $_SESSION['lang_data']['TEXT_NAME'] . ": ".$_SESSION['name']."</p>";
            echo "<button type='submit' id='closeSession' onclick='destroySession()' class='sh-lens sh-focus'>Cerrar sesión</button>";
            echo "</div>";
        }

        function formatearTiempo($segundos) {
        if ($segundos === null) return null;
        $horas = floor($segundos / 3600);
        $minutos = floor(($segundos % 3600) / 60);
        $segundosRestantes = $segundos % 60;

        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundosRestantes);
    }
    ?>
    <div class="gameoverDiv sh-reveal">
        
    <?php
       echo '<h1 class="sh-highlight">'. $_SESSION['lang_data']['TITLE_GAME_OVER'] .'</h1>';
    ?>

         <?php
            echo "<table class='sh-reveal'>";
            echo "<tr><th>". $_SESSION['lang_data']['TEXT_NAME'] ."</th><th>". $_SESSION['lang_data']['TEXT_POINTS'] ."</th><th>". $_SESSION['lang_data']['TEXT_TEMP'] ."</th></tr>";
            echo "<tr><td>".$name."</td><td>".$points."</td><td>".formatearTiempo($temp)."</td></tr>";
            echo "</table>";
         ?>

        <div class="buttons">
            
            <form action="./ranking.php" method="post" class="buttons" style="display:inline;">
            <?php
                echo '<button type="submit" id="returnRanking" value="Sí, lo quiero registrar" class="sh-lens sh-focus">'. $_SESSION['lang_data']['TEXT_REGISTER'] .'</button>'
            ?>
            </form>
            <?php
                echo '<button type="submit" id="returnIndex" value="No lo quiero registrar" class="sh-lens sh-focus">'. $_SESSION['lang_data']['TEXT_NOT_REGISTER'] .'</button>'
            ?>
        </div>
    </div>
    <script>
        const gameoverBGX = new Audio("./media/gameover.mp3");
        gameoverBGX.loop = true;
        gameoverBGX.load();
        gameoverBGX.play();
        const buttonIndex = document.getElementById("returnIndex");
        const buttonRanking = document.getElementById("returnRanking");
        const closeSession = document.getElementById("closeSession");
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }
        buttonIndex.addEventListener('click', (e) => {
            e.preventDefault();
            window.location = "/index.php";
        })

        document.addEventListener("keydown", (event)=>{
        if ((event.key).toLocaleLowerCase() === "s"){
            buttonRanking.classList.add("highlightButtonText");
            setTimeout(() => {
                buttonRanking.click();
            }, "1000");
        } else if ((event.key).toLocaleLowerCase() === "n"){
            buttonIndex.classList.add("highlightButtonText");
            setTimeout(() => {
                buttonIndex.click();
            }, "1000");
        } else if ((event.key).toLocaleLowerCase() === "c"){
            closeSession.classList.add("highlightButtonText");
            setTimeout(() => {
                closeSession.click();
            }, "1000");
    }})

    </script>
</body>
</html>