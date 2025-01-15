<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

include('cfg.php');


$idp = isset($_GET['idp']) ? htmlspecialchars($_GET['idp']) : '';

switch ($idp) {
    case 'burdz_chalifa':
        $strona = 'html/Burdz_Chalifa.html';
        break;
    case 'merdeka118':
        $strona = 'html/Merdeka118.html';
        break;
    case 'shanghai_tower':
        $strona = 'html/Shanghai_Tower.html';
        break;
    case 'kontakt':
        $strona = 'html/contact.php';
        break;
    case 'zegar':
        $strona = 'html/zegar.html';
        break;
    case 'filmy':
        $strona = 'html/filmy.html';
        break;
    case 'sklep':
        $strona = 'produkty.php'; 
        break;
    case 'koszyk':
        $strona = 'koszyk.php'; 
        break;
    case 'admin_panel':
        $strona = 'admin.php'; 
        break;
    default:
        $strona = 'html/glowna.html'; 
        break;
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
                <li><a href="index.php?idp=sklep">Sklep</a></li>
                <li><a href="index.php?idp=koszyk">Koszyk</a></li> <!-- Link do koszyka -->
                <li><a href="index.php?idp=admin_panel">Panel Administratora</a></li> <!-- Link do panelu administratora -->
            </ul>
        </nav>
    </header>
    <main>
        <?php
        
        if (file_exists($strona)) {
            include($strona);
        } else {
            echo "<p>Strona nie istnieje.</p>";
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
