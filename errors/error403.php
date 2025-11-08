<?php
session_start();
    if (!isset($_SESSION['lang_data'])) {
        header('Location: ../index.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 403</title>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Oswald:wght@200..700&family=Rubik:ital,wght@0,300..900;1,300..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/styles.css?no-cache=<?php echo time(); ?>">
    <link rel="icon" href="/media/lupa.ico">
</head>

<body class="error">
    <div class="postit">
    <?php
        echo '<img class="topPin" src="/media/pin.png" alt="'. $_SESSION['lang_data']['TEXT_PUSHPIN'] .'">';
        echo '<img src="/media/postit.png" alt="'. $_SESSION['lang_data']['TEXT_POSTIT'] .'">';
        echo "<p>". $_SESSION['lang_data']['TEXT_ERROR_403'] ."</p>"
    ?>
    </div>
    <div class="smallPostits">
        <div>
            <?php
            echo '<img class="pins" src="/media/pin.png" alt='. $_SESSION['lang_data']['TEXT_PUSHPIN'] .'>';
            echo '<input type="button" id="goIndex" value='. $_SESSION['lang_data']['TEXT_BUTTON_RETURN'] .' onclick="changePage()">';
            ?>
        </div>
    </div>
    <script>
        const goIndex = document.getElementById("goIndex");

        document.addEventListener("keydown", (event) => {
            if (event.key.toLowerCase() === "p") {
                goIndex.classList.add("highlightButtonTextErrors");
                setTimeout(() => {
                    goIndex.click();
                }, 1000);
            }
        });

        function changePage() {
            window.location = "/index.php";
        }
    </script>
</body>

</html>
