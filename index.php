<?php
    session_start();
    if (isset($_SESSION['points'])) {
        unset($_SESSION['points']);
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CipherType</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="media/lupa.ico">
</head>
<body class="play">
    <?php
        if (isset($_SESSION['name'])) {
            echo "<div class='gameoverDiv cancelSession'>";
            echo "<p>Nombre: ".$_SESSION['name']."</p>";
            echo "<button type='submit' id='closeSession' onclick='destroySession()'>Cerrar sesión</button>";
            echo "</div>";
        }
    ?>
    <div class="maincontainer">
        <form action="./play.php" method="post" class="datacontainer">
            <h1>CipherType</h1>
            <div class="incontainer">
                <input type="text" id="inname" name="inname" placeholder="Introduzca su nombre">
                <select disabled name="indifficulty" id="indifficulty">
                    <option value="sencillo">sencillo</option>
                    <option value="normal">normal</option>
                    <option value="experto">experto</option>
                </select>
                <button disabled type="submit" id="buttonInitialitze">Inicializar</button>
                <p id="messageerror" class="error"></p>
                <noscript>
                <p class="error">Querido Watson, debes activar el javascript para seguirle la pista</p>
                </noscript>
            </div>
        </form>
        <div class="datacontainer">
            <h1>Description</h1>
            <p>
                Mi querido Watson, este juego es una batalla entre tu mente
                y el paso del tiempo: las palabras son las pistas, y tu rapidez 
                y precisión al escribir son la clave para ganar.
            </p>
        </div>
    </div>
    <img class="mesa" src="media/mesamesa.jpg" alt="Imagen de una mesa">
    <div class="machine">
        <img src="media/typingmachine.png" alt="Imagen de máquina de escribir">
    </div>
    <script>
    const closeSession = document.getElementById("closeSession");
        const destroySession = () => {
            window.location = "/destroy_session.php";
        }
    const buttonInitialitzeGame = document.getElementById('buttonInitialitze');
    document.querySelector("#indifficulty").disabled = false;
    document.querySelector("#buttonInitialitze").disabled = false;
    const input = document.getElementById('inname');

    buttonInitialitzeGame.addEventListener("click", (event) => {
        if (input.value.trim() === "") {
            event.preventDefault();
            const message = document.getElementById('messageerror');
            message.textContent = "Querido Watson, tu nombre no puede ser un espacio vacio";
        }
    });

    document.addEventListener("keydown", (event)=>{
        console.log(event.target);
        if (event.target.nodeName === "INPUT"){
            return;
        }
        if ((event.key).toLocaleLowerCase() === "i"){
            buttonInitialitzeGame.classList.add("highlightButtonText");
            setTimeout(() => {
                buttonInitialitzeGame.click();
            }, "1000");
        } else if ((event.key).toLocaleLowerCase() === "c"){
            closeSession.classList.add("highlightButtonText");
            setTimeout(() => {
                closeSession.click();
            }, "1000");
    }})

    const valueName = "<?php 
    if (isset($_SESSION['name'])){
        echo $_SESSION['name'];
    } else{
        echo '';
    } ?>";
    if (valueName !== ''){
        input.value = valueName;
    }
    
</script>
</body>
</html>