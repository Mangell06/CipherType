<?php
if (!defined('ACCESS_ALLOWED')) {
    header('HTTP/1.0 403 Forbidden');
    // exit('Acceso directo no permitido.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gameover</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
<body class="gameover">
    <div class="gameoverDiv">
        <h1>¿Quieres registrar tu récord?</h1>
         <?php
            session_start();
            $name = $_SESSION['name'];
            $points = $_GET['points'];
            echo "<table>";
            echo "<tr><th>Nombre</th><th>Puntos</th></tr>";
            echo "<tr><td>".$name."</td><td>".$points."</td></tr>";
            echo "</table>";
         ?>

         <div class="buttons">
            <input type="button" value="Sí, lo quiero registrar" onclick="changePageRanking()">
            <input type="button" value="No lo quiero registrar" onclick="changePageIndex()">
        </div>
    </div>
    <script>
        function changePageIndex(){
            window.location = "/index.php";
        }
        function changePageRanking(){
            window.location = "/ranking.php";
            <?php
                session_start();
                $name = $_SESSION['name'] ?? 'Desconocido';
                $points = $_SESSION['points'] ?? 0;
                $file = 'ranking.txt';
                $line = "#{$name}:{$points}\n";
                file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
                header("Location: ranking.php");
                exit;
                ?>
        }
        
        
    </script>
         
</body>
</html>

