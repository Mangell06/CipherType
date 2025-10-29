
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game</title>
    <link rel="stylesheet" href="styles.css?no-cache=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="icon" href="media/lupa.ico">
</head>
<body>
    <noscript>
            <p class="error">Querido Watson, debes activar el javascript para seguirle la pista</p>
    </noscript>
    <h1 class="js-required hidden">Tienes js activado</h1>
    <script>
        const text =document.querySelector(".js-required.hidden");
        text.classList.remove("js-required");
        text.classList.remove("hidden");
    </script>
</body>
</html>