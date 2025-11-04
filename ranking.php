<?php
    session_start();
    if (isset($_SESSION['name']) && isset($_SESSION['points'])) {
        $file = fopen('ranking.txt','a');
        $line = "#{$_SESSION['name']}:{$_SESSION['points']}\n";
        fwrite($file, $line);
        fclose($file);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
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
        <h1>Ranking</h1>
    <script>
        const gameoverBGX = new Audio("./media/ranking.mp3");
        gameoverBGX.loop = true;
        gameoverBGX.load();
        gameoverBGX.play();
    </script>
    <?php
    $contenido = file_get_contents('ranking.txt'); // leer el fichero
    if ($contenido !== false && !empty($contenido)) {
        $usuarios = explode('#', $contenido); // separar usuarios
        $ranking = [];
        $count = 0;
        foreach ($usuarios as $usuario) {
            if (!empty($usuario) && strpos($usuario, ':') !== false) {
                list($name, $points) = explode(':', $usuario);
                $ranking[$count] = [$name,(int)$points];
                $count ++;
            }
        }

        usort($ranking, fn($nombre, $puntos) => $puntos[1] <=> $nombre[1]); // ordenar array por puntos de mayor a menor

        echo "<table>";
        echo "<tr><th>Nombre</th><th>Puntos</th></tr>";
        foreach ($ranking as $count => [$name,$points]) {
            if (isset($_SESSION['name']) && isset($_SESSION['points']) && $_SESSION['name'] === $name && $_SESSION['points'] == $points) {
                echo "<tr><td class='winner'>".$name."</td><td class='winner'>".$points."</td></tr>";
            } else if ($count % 2 === 0 && $count !== 1) {
                echo "<tr><td class='second'>".$name."</td><td class='second'>".$points."</td></tr>";
            } else {
                echo "<tr><td>".$name."</td><td>".$points."</td></tr>";
            }
        }
        echo "</table>";
    } else {
        echo "El fichero está vacío o no se pudo leer.";
    }
    
    unset($_SESSION['points']);
    ?>
    <div class="maincontainer">
        <button type="submit" id="returnIndex" value="Volver al principio">Volver al principio</button>
    </div>
    <script>
        const closeSession = document.getElementById("closeSession");
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }
        const button = document.getElementById("returnIndex");
        button.addEventListener('click', (e) => {
            e.preventDefault();
            window.location = "/index.php";
        })
            
        document.addEventListener("keydown", (event)=>{
            if ((event.key).toLocaleLowerCase() === "v"){
                button.classList.add("highlightButtonText");
                setTimeout(() => {
                    button.click();
                }, "1000");
            } else if ((event.key).toLocaleLowerCase() === "c"){
                closeSession.classList.add("highlightButtonText");
                setTimeout(() => {
                    closeSession.click();
                }, "1000");
            } 
            
        })
    </script>
</body>
</html>
