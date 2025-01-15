<?php
session_start();

// Konfiguracja połączenia z bazą danych
define('DB_HOST', 'localhost'); // Adres serwera bazy danych
define('DB_USER', 'root');      // Nazwa użytkownika bazy danych
define('DB_PASS', '');          // Hasło bazy danych
define('DB_NAME', 'moja_strona'); // Nazwa bazy danych

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    // Obsługa błędów połączenia z bazą danych
    die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
}

// Funkcja dodawania produktu do koszyka
function DodajDoKoszyka($id, $ilosc) {
    if (!isset($_SESSION['koszyk'])) {
        $_SESSION['koszyk'] = [];
    }
    if (isset($_SESSION['koszyk'][$id])) {
        $_SESSION['koszyk'][$id] += $ilosc;
    } else {
        $_SESSION['koszyk'][$id] = $ilosc;
    }
}

// Obsługa dodawania do koszyka
if (isset($_GET['dodaj_do_koszyka']) && is_numeric($_GET['dodaj_do_koszyka'])) {
    $id = (int)$_GET['dodaj_do_koszyka']; // Pobieramy ID produktu z parametru URL
    DodajDoKoszyka($id, 1); // Dodajemy 1 sztukę produktu do koszyka
    header("Location: index.php?idp=sklep"); // Przekierowanie do odświeżonej strony sklepu
    exit(); // Zakończenie skryptu po przekierowaniu
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep Internetowy</title>
    <style>
        /* Ogólny styl strony */
        body {
            font-family: Arial, sans-serif;
            background-color: #FEFAE0;
        }

        /* Nagłówki */
        h1, h2 {
            color: #333;
            text-align: center;
        }

        /* Kontener dla produktów */
        .produkty-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        /* Styl pojedynczego produktu */
        .produkt {
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #F2EED7;
            padding: 15px;
            width: 300px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .produkt img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        .produkt h3 {
            margin: 10px 0;
            color: #798645;
        }

        .produkt p {
            margin: 5px 0;
        }

        /* Link "Dodaj do koszyka" */
        .produkt a.add-to-cart {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #798645;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .produkt a.add-to-cart:hover {
            background-color: #626F47;
        }

        /* Link powrotu lub szczegółów produktu */
        .produkt a.text-link {
            color: black; /* Czarny kolor */
            text-decoration: none; /* Bez podkreślenia */
            font-weight: bold;
        }

        .produkt a.text-link:hover {
            color: #626F47; /* Ciemnozielony przy najechaniu */
            text-decoration: none; /* Brak podkreślenia */
        }
    </style>
</head>
<body>
    <h1>Sklep Internetowy</h1>
    <h2>Lista Produktów</h2>
    <div class="produkty-container">
    <?php
    // Jeśli wybrano produkt, wyświetl szczegóły produktu
    if (isset($_GET['produkt'])) {
        $id = (int)$_GET['produkt']; // Pobieramy ID produktu z parametru URL
        $result = $mysqli->query("SELECT produkty.*, kategorie.nazwa AS kategoria_nazwa 
                                  FROM produkty 
                                  LEFT JOIN kategorie ON produkty.kategoria = kategorie.id 
                                  WHERE produkty.id = $id");

        if ($row = $result->fetch_assoc()) {
            $cena_brutto = $row['cena_netto'] * (1 + $row['vat'] / 100); // Obliczamy cenę brutto

            // Sprawdzanie dostępności produktu
            $ilosc = (int)$row['ilosc'];
            $dostepnosc = $ilosc > 0 ? "Dostępnych: $ilosc szt." : "Brak na stanie";

            // Wyświetlenie szczegółów produktu
            echo "<div class='produkt'>
                    <h3>{$row['tytul']}</h3>
                    <img src='{$row['zdjecie']}' alt='{$row['tytul']}'>
                    <p>{$row['opis']}</p>
                    <p><strong>Kategoria:</strong> {$row['kategoria_nazwa']}</p>
                    <p><strong>Gabaryt:</strong> {$row['gabaryt']}</p> <!-- Wyświetlanie gabarytu -->
                    <p><strong>Cena brutto:</strong> {$cena_brutto} zł</p>
                    <p><strong>Dostępność:</strong> $dostepnosc</p>";
            
            // Wyświetlenie przycisku "Dodaj do koszyka" tylko, jeśli produkt jest dostępny
            if ($ilosc > 0) {
                echo "<p><a href='index.php?idp=sklep&dodaj_do_koszyka={$row['id']}' class='add-to-cart'>Dodaj do koszyka</a></p>";
            }

            echo "<p><a href='index.php?idp=sklep' class='text-link'>Powrót do sklepu</a></p>
                  </div>";
        } else {
            echo "<p>Produkt nie został znaleziony.</p>"; // Obsługa, gdy produkt nie istnieje
        }
    } else {
        // Wyświetlanie listy produktów
        $result = $mysqli->query("SELECT produkty.*, kategorie.nazwa AS kategoria_nazwa 
                                  FROM produkty 
                                  LEFT JOIN kategorie ON produkty.kategoria = kategorie.id");
        while ($row = $result->fetch_assoc()) {
            $cena_brutto = $row['cena_netto'] * (1 + $row['vat'] / 100); // Obliczamy cenę brutto
            echo "<div class='produkt'>
                    <h3><a href='index.php?idp=sklep&produkt={$row['id']}'>{$row['tytul']}</a></h3>
                    <img src='{$row['zdjecie']}' alt='{$row['tytul']}'>
                    <p><strong>Cena brutto:</strong> {$cena_brutto} zł</p>
                    <p><a href='index.php?idp=sklep&dodaj_do_koszyka={$row['id']}' class='add-to-cart'>Dodaj do koszyka</a></p>
                  </div>";
        }
    }
    ?>
    </div>
</body>
</html>
