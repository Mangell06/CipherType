<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 403</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="media/lupa.ico">
</head>
<body class="error">
    <div class="postit">
        <img class="topPin" src="media/pin.png" alt="Imagen de una chincheta">
        <img src="media/postit.png" alt="Imagen de una post-it">
        <p>Watson, ¿cómo esperas descubrir al culpable si ni siquiera has revisado las pistas? Acceso denegado.</p>
    </div>
    <div class="smallPostits">
        <div>
            <img class="pins" src="media/pin.png" alt="Imagen de una chincheta">
            <input type="button" value="Página inicial" onclick="changePage()">
        </div>
     </div>
     <script>
        function changePage(){
            window.location = "CipherType/play.php";
        }
     </script>
</body>
</html>