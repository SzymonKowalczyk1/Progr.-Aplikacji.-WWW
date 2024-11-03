<?php
    $nr_indeksu = '169270';
    $nrGrupy = '3';

    echo 'Szymon Kowalczyk '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';

    echo 'Zastosowanie metody include() i require_once() <br/>';
?>

<?php
    include('test.php');  
    require_once('test2.php'); 
?>

<?php
    echo '<br/>b) Warunki if, else, elseif, switch <br />';

    $liczba = 7;

    if ($liczba > 10) {
        echo 'Liczba jest większa od 10 <br />';
    } elseif ($liczba == 5) {
        echo 'Liczba wynosi 5 <br />';
    } else {
        echo 'Liczba jest mniejsza od 10 <br />';
    }

    $kolor = "różowy";
    switch ($kolor) {
        case "czerwony":
            echo 'Kolor to czerwony <br />';
            break;
        case "zielony":
            echo 'Kolor to zielony <br />';
            break;
        default:
            echo 'Inny kolor <br />';
    }
?>

<?php
    echo 'c) Pętla while() i for() <br />';

    $i = 0;
    while ($i < 5) {
        echo 'Licznik while: '.$i.'<br />';
        $i++;
    }

    for ($j = 0; $j < 5; $j++) {
        echo 'Licznik for: '.$j.'<br />';
    }
?>

<?php
    echo 'd) Typy zmiennych $_GET, $_POST, $_SESSION <br />';

    echo '$_GET: <br />';
    if (isset($_GET['nazwa'])) {
        echo 'Wartość GET: '.$_GET['nazwa'].'<br />';
    } else {
        echo 'Brak wartości w GET <br />';
    }

    echo '<form method="get" action="">';
    echo '<label for="nazwa">Podaj nazwę (GET): </label>';
    echo '<input type="text" name="nazwa" id="nazwa">';
    echo '<input type="submit" value="Wyślij">';
    echo '</form>';

    echo '$_POST: <br />';
    if (isset($_POST['dane'])) {
        echo 'Wartość POST: '.$_POST['dane'].'<br />';
    } else {
        echo 'Brak wartości w POST <br />';
    }

    echo '<form method="post" action="">';
    echo '<label for="dane">Podaj dane (POST): </label>';
    echo '<input type="text" name="dane" id="dane">';
    echo '<input type="submit" value="Wyślij">';
    echo '</form>';

    session_start();
    if (!isset($_SESSION['licznik'])) {
        $_SESSION['licznik'] = 0;
    }
    $_SESSION['licznik']++;
    echo 'Wartość sesji: '.$_SESSION['licznik'].'<br />';
?>
