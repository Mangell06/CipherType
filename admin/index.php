<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminstrator</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles.css?no-cache=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="admincontainermain">
        <?php
            if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
                $username = $_SESSION['username'];
                echo '<form action="logout.php" method="post" id="logoutcontainer">';
                echo "<p>$username</p>";
                echo '<button type="submit" id="buttonLogout">Log Out</button>';
                echo '</form>';
                echo '<div class="toolscontainer">';
                    echo '<form method="post">';
                        echo '<select name="selectdifficulty" id="selectdifficulty" onchange="this.form.submit()">';
                            echo '<option value="" selected hidden>Selecciona dificultad</option>';
                            echo '<option value="sencillo">sencillo</option>';
                            echo '<option value="normal">normal</option>';
                            echo '<option value="experto">experto</option>';
                        echo '</select>';
                    echo '</form>';
                    echo '<form action="">';
                        echo '<button type="submit" id="addbutton">&#43;</button>';
                    echo '</form>';
                        $selectdifficulty = $_POST['selectdifficulty'] ?? 'sencillo';
                        $sentencesByDifficulty = [];
                        if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
                            $sentences = fopen('../sentences.txt', 'r');
                            if ($sentences) {
                                while (!feof($sentences)) {
                                    $linea = fgets($sentences);
                                    if ($linea === false || trim($linea) === '') continue;
                                    $linea = trim($linea);
                                    $partes = explode(':', $linea, 2);
                                    if (count($partes) < 2) continue;
                                    $nivel = trim($partes[0]);
                                    if ($nivel !== $selectdifficulty) continue;
                                    $selectedSentences = explode('*', $partes[1]);
                                    break;
                                }
                                $count = 0;
                                echo "<table>";
                                echo "<tr><th>Frases</th></tr>";
                                foreach ($selectedSentences as $count => $frase) {
                                    echo '<form action="">';
                                    if ($count % 2 !== 0) {
                                        echo "<tr><td class='second'>".$frase;
                                    } else {
                                        echo "<tr><td>".$frase;
                                    }
                                    echo "<button class='deletebutton'>&#128465;</button></td></tr>";
                                    echo '</form>';
                                }
                                echo "</table>";
                                fclose($sentences);
                            }
                        }
            echo '</div>';
            } else {
                $uri = $_SERVER['REQUEST_URI'];
                if (strpos($uri, '/admin') !== false) {
                    $rutaBase = 'admin/login.php';
                } else {
                    $rutaBase = 'login.php';
                }
                echo "<form action='$rutaBase' method='post' id='logincontainer'>";
                echo '<label for="username">Username</label>';
                echo '<input type="text" id="username" name="username" placeholder="juan.perez" />';
                echo '<label for="password">Password</label>';
                echo '<input type="password" id="password" name="password" placeholder="password" />';
                echo '<button type="submit" id="buttonLogin">Login</button>';
                if (isset($_SESSION['error']) && $_SESSION['error']) {
                    echo '<p class="error">El usuario no existe o la contraseña es incorrecta</p>';
                    unset($_SESSION['error']);
                }
                echo '</form>';
            }
        ?>
    </div>
</body>
</html>