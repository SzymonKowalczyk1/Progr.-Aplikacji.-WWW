<?php
session_start();

// Konfiguracja połączenia z bazą danych
define('DB_HOST', 'localhost'); // Adres serwera bazy danych
define('DB_USER', 'root');      // Nazwa użytkownika bazy danych
define('DB_PASS', '');          // Hasło do bazy danych
define('DB_NAME', 'moja_strona'); // Nazwa bazy danych

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_error) {
    // Obsługa błędu połączenia z bazą danych
    die("Błąd połączenia z bazą danych: " . $mysqli->connect_error);
}

// Dołączenie Configu
include("cfg.php");

// Funkcja wyświetlająca formularz logowania
function FormularzLogowania() {
    echo '
    <form method="post" action="admin.php">
        <label for="username">Login:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit" name="login">Zaloguj</button>
    </form>';
}

// Obsługa logowania
if (isset($_POST['login'])) {
    // Porównanie danych logowania z zapisanymi w `cfg.php`
    if ($_POST['username'] === $login && $_POST['password'] === $pass) {
        $_SESSION['logged_in'] = true; // Ustawienie flagi zalogowania
        $_SESSION['username'] = $_POST['username']; // Zapisanie nazwy użytkownika w sesji
    } else {
        echo "<p style='color: red;'>Błędne dane logowania.</p>";
        FormularzLogowania(); // Wyświetlenie formularza logowania w przypadku błędu
        exit();
    }
}

// wylogowanie
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset(); // Wyczyszczenie danych sesji
    session_destroy(); // Zniszczenie sesji
    header("Location: index.php?idp=admin_panel"); // Przekierowanie na stronę logowania
    exit();
}

// Jeśli użytkownik nie jest zalogowany, wyświetl formularz logowania
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    FormularzLogowania();
    exit();
}

// Statyczna ścieżka do pliku admina
$adminUrl = "admin.php";

// Wyświetlanie panelu administratora po zalogowaniu
echo '<div style="text-align: right; padding: 10px;">
        Zalogowany jako ' . htmlspecialchars($_SESSION['username']) . ' | 
        <a href="' . $adminUrl . '?action=logout" style="text-decoration: none; color: black;">Wyloguj</a>
      </div>';
echo "<h1 style='text-align: center;'>Panel Administratora</h1>";
echo '<div style="margin: 20px auto; text-align: center;">
        <a href="' . $adminUrl . '?action=add_product"><button style="padding: 10px 20px; margin: 5px;">Dodaj Produkt</button></a>
        <a href="' . $adminUrl . '?action=add_category"><button style="padding: 10px 20px; margin: 5px;">Dodaj Kategorię</button></a>
      </div>';

// Funkcja do dodawania produktów
function DodajProdukt() {
    global $mysqli;

    echo '<h2>Dodaj nowy produkt</h2>';
    echo '<form method="post">
            <label>Tytuł produktu: <input type="text" name="tytul" required></label><br>
            <label>Opis: <textarea name="opis" required></textarea></label><br>
            <label>Cena netto: <input type="number" step="0.01" name="cena_netto" required></label><br>
            <label>VAT (%): <input type="number" step="0.01" name="vat" required></label><br>
            <label>Ilość: <input type="number" name="ilosc" required></label><br>
            <label>Gabaryt: 
                <select name="gabaryt" required>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                </select>
            </label><br>
            <label>Status: 
                <select name="status" required>
                    <option value="dostepny">Dostępny</option>
                    <option value="niedostepny">Niedostępny</option>
                </select>
            </label><br>
            <label>Kategoria: 
                <select name="kategoria" required>
                    <option value="">Wybierz kategorię</option>';
    // Pobieranie kategorii głównych
    $result = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0");
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
        // Pobieranie podkategorii
        $subcategories = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = {$row['id']}");
        while ($subcategory = $subcategories->fetch_assoc()) {
            echo "<option value='{$subcategory['id']}'>-- {$subcategory['nazwa']}</option>";
        }
    }
    echo '</select>
            </label><br>
            <label>URL zdjęcia: <input type="url" name="zdjecie" required></label><br>
            <button type="submit" name="dodaj_produkt">Dodaj produkt</button>
          </form>';

    // Obsługa dodawania produktu po przesłaniu formularza
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_produkt'])) {
        // Pobranie i zabezpieczenie danych wejściowych
        $tytul = $mysqli->real_escape_string($_POST['tytul']);
        $opis = $mysqli->real_escape_string($_POST['opis']);
        $cena_netto = (float)$_POST['cena_netto'];
        $vat = (float)$_POST['vat'];
        $ilosc = (int)$_POST['ilosc'];
        $gabaryt = $mysqli->real_escape_string($_POST['gabaryt']);
        $status = $mysqli->real_escape_string($_POST['status']);
        $kategoria = (int)$_POST['kategoria'];
        $zdjecie = $mysqli->real_escape_string($_POST['zdjecie']);

        // Dodanie produktu do bazy danych
        $stmt = $mysqli->prepare("INSERT INTO produkty (tytul, opis, cena_netto, vat, ilosc, gabaryt, status, kategoria, zdjecie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddiisss", $tytul, $opis, $cena_netto, $vat, $ilosc, $gabaryt, $status, $kategoria, $zdjecie);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Produkt '$tytul' został dodany.</p>";
        } else {
            echo "<p style='color: red;'>Błąd: Nie udało się dodać produktu.</p>";
        }
        $stmt->close();
    }
}

// Obsługa akcji w panelu admina
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add_product') {
        DodajProdukt(); // Wywołanie funkcji dodawania produktu
    } elseif ($action === 'add_category') {
        echo '<h2>Dodaj kategorię</h2>';
        echo '<form method="post" action="admin.php">
                <label>Nazwa kategorii: <input type="text" name="nazwa" required></label><br>
                <label>Kategoria nadrzędna: 
                    <select name="matka">
                        <option value="0">Brak</option>';
        // Pobieranie wszystkich kategorii
        $result = $mysqli->query("SELECT id, nazwa FROM kategorie");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
        }
        echo '</select></label><br>
                <button type="submit" name="dodaj_kategorie">Dodaj</button>
              </form>';

        // Obsługa dodawania kategorii
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_kategorie'])) {
            $nazwa = $mysqli->real_escape_string($_POST['nazwa']);
            $matka = (int)$_POST['matka'];
            $stmt = $mysqli->prepare("INSERT INTO kategorie (nazwa, matka) VALUES (?, ?)");
            $stmt->bind_param("si", $nazwa, $matka);
            $stmt->execute();
            echo "<p style='color: green;'>Kategoria '$nazwa' została dodana.</p>";
            $stmt->close();
        }
    }
}
?>
