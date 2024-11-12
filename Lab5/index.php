<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);


if ($_GET['idp'] == '') {
    $strona = 'html/glowna.html';
}
if ($_GET['idp'] == 'burdz_chalifa') {
    $strona = 'html/Burdz_Chalifa.html';
}
if ($_GET['idp'] == 'merdeka118') {
    $strona = 'html/Merdeka118.html';
}
if ($_GET['idp'] == 'shanghai_tower') {
    $strona = 'html/Shanghai_Tower.html';
}
if ($_GET['idp'] == 'kontakt') {
    $strona = 'html/kontakt.html';
}
if ($_GET['idp'] == 'zegar') {
    $strona = 'html/zegar.html';
}
if ($_GET['idp'] == 'filmy') {
    $strona = 'html/filmy.html';
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="Author" content="Szymon Kowalczyk">
    <meta name="Content-Language" content="pl">
    <link rel="stylesheet" href="css/style.css">
    <title>Największe budynki świata</title>
</head>
<body>
    <header>
        <h1>Największe budynki świata</h1>
        <nav>
            <ul>
                <li><a href="index.php?idp=">Strona Główna</a></li>
                <li><a href="index.php?idp=burdz_chalifa">Burdż Chalifa</a></li>
                <li><a href="index.php?idp=merdeka118">Merdeka 118</a></li>
                <li><a href="index.php?idp=shanghai_tower">Shanghai Tower</a></li>
                <li><a href="index.php?idp=kontakt">Kontakt</a></li>
                <li><a href="index.php?idp=zegar">Zegar</a></li>
                <li><a href="index.php?idp=filmy">Filmy</a></li>

            </ul>
        </nav>
    </header>
    <main>
        <?php
    
        if (file_exists($strona)) {
            include($strona);
        } else {
            echo "Strona nie istnieje.";
        }
        ?>
    </main>
    <footer>
        <p>&copy; Szymon Kowalczyk ISI3 169270</p>
        <?php
        $nr_indeksu = '169270';
        $nrGrupy = 'isi3';
        echo 'Autor: Szymon Kowalczyk ' . $nr_indeksu . ' grupa ' . $nrGrupy . ' <br /><br />';
        ?>
    </footer>
</body>
</html>
