<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CipherType</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="maincontainer">
        <form action="./play.php" method="post" class="datacontainer">
            <h1>CipherType</h1>
            <div class="incontainer">
                <input type="text" id="inname" name="inname" placeholder="Introduzca su nombre">
                <select name="indifficulty" id="indifficulty">
                    <option value="Sencillo">Sencillo</option>
                    <option value="Normal">Normal</option>
                    <option value="Experto">Experto</option>
                </select>
                <button type="submit" id="buttonInitialitze">Inicializar</button>
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
    <img class="mesa" src="media/mesamesa.jpg" alt="">
    <div class="machine">
        <img src="media/typingmachine.png" alt="">
    </div>
</body>
<script>
    const buttonInitialitzeGame = document.getElementById('buttonInitialitze');

    buttonInitialitzeGame.addEventListener("click", (event) => {
        const input = document.getElementById('inname');
        
        if (input.value.trim() === "") {
            event.preventDefault();
        }
    });
</script>
</html>