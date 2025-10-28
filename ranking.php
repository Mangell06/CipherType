<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="/media/lupa.ico">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
<body class="gameover">
    <div class="gameoverDiv">
        <h1>Ranking</h1>
    <?php
    $contenido = file_get_contents('ranking.txt'); // leer el fichero
    if ($contenido !== false && !empty($contenido)) {
        $usuarios = explode('#', $contenido); // separar usuarios
        $ranking = [];

        foreach ($usuarios as $usuario) {
            if (!empty($usuario) && strpos($usuario, ':') !== false) {
                list($name, $points) = explode(':', $usuario);
                $ranking[$name] = (int)$points;
            }
        }

        arsort($ranking); // ordenar de mayor a menor
        $count = 0;
        echo "<table>";
        echo "<tr><th>Nombre</th><th>Puntos</th></tr>";
        foreach ($ranking as $name => $points) {
            if ($_SESSION['name'] === $name && $_SESSION['points'] === $points) {
              echo "<tr><td class='winner'>".$name."</td><td class='winner'>".$points."</td></tr>";
            } else if ($count % 2 === 0 && $count !== 1) {
                echo "<tr><td class='second'>".$name."</td><td class='second'>".$points."</td></tr>";
            } else {
                echo "<tr><td>".$name."</td><td>".$points."</td></tr>";
            }
            $count ++;
        }
        echo "</table>";
    } else {
        echo "El fichero está vacío o no se pudo leer.";
    }
    ?>
</body>
</html>
