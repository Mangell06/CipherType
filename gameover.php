<?php
    if (!defined('ACCESS_ALLOWED')) {
        // header('HTTP/1.0 403 Forbidden');
        // exit('Acceso directo no permitido.');
    }

    session_start();
    $name = $_SESSION['name'];
    $points = $_POST['points'];
    if ( isset($_POST['points'])) {
        $_SESSION["points"] = $points;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Over</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="/media/lupa.ico">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
<body class="gameover">
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
                <input type="submit" value="Sí, lo quiero registrar">
            </form>
            <input type="button" id="returnIndex" value="No lo quiero registrar">
        </div>
    </div>
    <script>
        const button = document.getElementById("returnIndex");

        button.addEventListener('click', (e) => {
            e.preventDefault();
            window.location = "/index.php";
        })
    </script>
</body>
</html>
