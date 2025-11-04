<?php
    session_start();
    $name = $_SESSION['name'];
    $points = $_POST['points'];
    if (!isset($name) || !isset($points)) {
        header("HTTP/1.1 403 Forbidden");
        header('Location: /errors/error403.php');
        exit();
    }
    $_SESSION["points"] = $points;
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
<body class="gameover">
    <?php
        if (isset($_SESSION['name'])) {
            echo "<div class='gameoverDiv cancelSession'>";
            echo "<p>Nombre: ".$_SESSION['name']."</p>";
            echo "<button type='submit' id='closeSession' onclick='destroySession()'>Cerrar sesión</button>";
            echo "</div>";
        }
    ?>
    <div class="gameoverDiv">
        <h1>¿Quieres registrar tu récord?</h1>

         <?php
            echo "<table>";
            echo "<tr><th>Nombre</th><th>Puntos</th></tr>";
            echo "<tr><td>".$name."</td><td>".$points."</td></tr>";
            echo "</table>";
         ?>

        <div class="buttons">
            
            <form action="./ranking.php" method="post" class="buttons" style="display:inline;">
                <button type="submit" id="returnRanking" value="Sí, lo quiero registrar">Sí, lo quiero registrar</button>
            </form>
            <button type="submit" id="returnIndex" value="No lo quiero registrar">No lo quiero registrar</button>
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