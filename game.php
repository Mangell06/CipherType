
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <link rel="icon" href="media/lupa.ico">
</head>
<body>
    <noscript>
            <p class="error">Querido Watson, debes activar el javascript para seguirle la pista</p>
    </noscript>
    <h1 class="js-required hidden">Tienes js activado</h1>
    <script>
        const text =document.querySelector(".js-required.hidden");
        text.classList.remove(".hidden");
    </script>
</body>
</html>