<?php


session_start();

// Dołącz plik konfiguracji
include("cfg.php");

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

// Logowanie
if (isset($_POST['login'])) {
    if ($_POST['username'] === $login && $_POST['password'] === $pass) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $_POST['username'];
    } else {
        echo "<p style='color: red;'>Błędne dane logowania.</p>";
        FormularzLogowania();
        exit();
    }
}

// Wylogowanie
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Sprawdzanie zalogowania
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    FormularzLogowania();
    exit();
}

// Panel administratora
echo '<div style="text-align: right; padding: 10px;">
        Zalogowany jako ' . htmlspecialchars($_SESSION['username']) . ' | 
        <a href="admin.php?action=logout" ">Wyloguj</a>
      </div>';
echo "<h1 style='text-align: center;'>Panel Administratora</h1>";

echo '<div style="text-align: center; margin: 20px;">
        <a href="admin.php?action=add_product"><button>Dodaj Produkt</button></a>
        <a href="admin.php?action=add_category"><button>Dodaj Kategorię</button></a>
        <a href="admin.php?action=list_products"><button>Lista Produktów</button></a>
        <a href="admin.php?action=list_categories"><button>Lista Kategorii</button></a>
      </div>';



// Obsługa akcji
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Dodawanie produktu
    if ($action === 'add_product') {
        echo '<h2>Dodaj Produkt</h2>';
        echo '<form method="post" action="admin.php?action=add_product" enctype="multipart/form-data">
                <label>Tytuł produktu: <input type="text" name="tytul" required></label><br>
                <label>Opis: <textarea name="opis" required></textarea></label><br>
                <label>Cena netto: <input type="number" step="0.01" name="cena_netto" required></label><br>
                <label>VAT (%): <input type="number" step="0.01" name="vat" required></label><br>
                <label>Ilość: <input type="number" name="ilosc" required></label><br>
                <label>Kategoria: 
                    <select name="kategoria" required>
                        <option value="">Wybierz kategorię</option>';
        $result = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
                $subcategories = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = {$row['id']}");
                while ($subcategory = $subcategories->fetch_assoc()) {
                    echo "<option value='{$subcategory['id']}'>-- {$subcategory['nazwa']}</option>";
                }
            }
        }
        echo '</select></label><br>
                <label>Zdjęcie produktu: <input type="file" name="zdjecie" accept="image/*" required></label><br>
            <button type="submit" name="dodaj_produkt">Dodaj produkt</button>
          </form>';
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_produkt'])) {
            $tytul = $mysqli->real_escape_string($_POST['tytul']);
            $opis = $mysqli->real_escape_string($_POST['opis']);
            $cena_netto = (float)$_POST['cena_netto'];
            $vat = (float)$_POST['vat'];
            $ilosc = (int)$_POST['ilosc'];
            $kategoria = (int)$_POST['kategoria'];
    
            // Obsługa przesyłania pliku
            $upload_dir = 'uploads/';
            $upload_file = $upload_dir . basename($_FILES['zdjecie']['name']);
            if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $upload_file)) {
                $zdjecie = $mysqli->real_escape_string($upload_file);
    
                $stmt = $mysqli->prepare("INSERT INTO produkty (tytul, opis, cena_netto, vat, ilosc, kategoria, zdjecie) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssddiis", $tytul, $opis, $cena_netto, $vat, $ilosc, $kategoria, $zdjecie);
                    if ($stmt->execute()) {
                        echo "<p style='color: green;'>Produkt '$tytul' został dodany.</p>";
                    } else {
                        echo "<p style='color: red;'>Błąd dodawania produktu: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p style='color: red;'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
                }
            } else {
                echo "<p style='color: red;'>Błąd przesyłania zdjęcia.</p>";
            }
        }
    
    
    
   
}
// Edycja produktu
elseif ($action === 'edit_product' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $mysqli->query("SELECT * FROM produkty WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        echo '<h2>Edytuj Produkt</h2>';
        echo '<form method="post" action="admin.php?action=edit_product&id=' . $id . '" enctype="multipart/form-data">
                <label>Tytuł produktu: <input type="text" name="tytul" value="' . htmlspecialchars($row['tytul']) . '" required></label><br>
                <label>Opis: <textarea name="opis" required>' . htmlspecialchars($row['opis']) . '</textarea></label><br>
                <label>Cena netto: <input type="number" step="0.01" name="cena_netto" value="' . $row['cena_netto'] . '" required></label><br>
                <label>VAT (%): <input type="number" step="0.01" name="vat" value="' . $row['vat'] . '" required></label><br>
                <label>Ilość: <input type="number" name="ilosc" value="' . $row['ilosc'] . '" required></label><br>
                <label>Kategoria: 
                    <select name="kategoria" required>';
        $categories = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0");
        while ($cat = $categories->fetch_assoc()) {
            $selected = $row['kategoria'] == $cat['id'] ? 'selected' : '';
            echo "<option value='{$cat['id']}' $selected>{$cat['nazwa']}</option>";
        }
        echo '</select></label><br>
                <label>Zdjęcie produktu: <input type="file" name="zdjecie" accept="image/*"></label><br>
                <button type="submit" name="edytuj_produkt">Zapisz zmiany</button>
              </form>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edytuj_produkt'])) {
            $tytul = $mysqli->real_escape_string($_POST['tytul']);
            $opis = $mysqli->real_escape_string($_POST['opis']);
            $cena_netto = (float)$_POST['cena_netto'];
            $vat = (float)$_POST['vat'];
            $ilosc = (int)$_POST['ilosc'];
            $kategoria = (int)$_POST['kategoria'];

            $zdjecie = $row['zdjecie']; 

            // Obsługa przesyłania nowego zdjęcia, jeśli zostało dodane
            if (!empty($_FILES['zdjecie']['name'])) {
                $upload_dir = 'uploads/';
                $upload_file = $upload_dir . basename($_FILES['zdjecie']['name']);
                if (move_uploaded_file($_FILES['zdjecie']['tmp_name'], $upload_file)) {
                    $zdjecie = $mysqli->real_escape_string($upload_file);
                } else {
                    echo "<p style='color: red;'>Błąd przesyłania zdjęcia. Zachowano poprzednie zdjęcie.</p>";
                }
            }

            // Aktualizacja produktu w bazie danych
            $stmt = $mysqli->prepare("UPDATE produkty SET tytul = ?, opis = ?, cena_netto = ?, vat = ?, ilosc = ?, kategoria = ?, zdjecie = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("ssddiisi", $tytul, $opis, $cena_netto, $vat, $ilosc, $kategoria, $zdjecie, $id);
                if ($stmt->execute()) {
                    echo "<p style='color: green;'>Produkt został zaktualizowany.</p>";
                } else {
                    echo "<p style='color: red;'>Błąd edycji produktu: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color: red;'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>Nie znaleziono produktu.</p>";
    }
}

    // Wyświetlanie listy produktów
    elseif ($action === 'list_products') {
        echo '<h2 style="text-align: center;">Lista Produktów</h2>';
        $result = $mysqli->query("SELECT produkty.id, produkty.tytul, produkty.cena_netto, produkty.vat, produkty.ilosc, 
                                         kategorie.nazwa AS kategoria_nazwa 
                                  FROM produkty 
                                  LEFT JOIN kategorie ON produkty.kategoria = kategorie.id");
        if ($result && $result->num_rows > 0) {
            echo '<table border="1" style="width: 80%; margin: 20px auto; border-collapse: collapse; text-align: center;">
                    <thead>
                        <tr style="background-color: #798645; color: #fff;">
                            <th>ID</th>
                            <th>Tytuł</th>
                            <th>Kategoria</th>
                            <th>Cena netto</th>
                            <th>VAT (%)</th>
                            <th>Ilość</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>';
            while ($row = $result->fetch_assoc()) {
                $cena_brutto = $row['cena_netto'] * (1 + $row['vat'] / 100);
                echo '<tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . htmlspecialchars($row['tytul']) . '</td>
                        <td>' . htmlspecialchars($row['kategoria_nazwa']) . '</td>
                        <td>' . number_format($row['cena_netto'], 2) . ' zł</td>
                        <td>' . $row['vat'] . '%</td>
                        <td>' . $row['ilosc'] . '</td>
                        <td>
                            <a href="admin.php?action=edit_product&id=' . $row['id'] . '">Edytuj</a> | 
                            <a href="admin.php?action=delete_product&id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć ten produkt?\');">Usuń</a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p style="text-align: center; color: red;">Brak produktów w bazie danych.</p>';
        }
    }
    // Dodawanie kategorii
    if ($action === 'add_category') {
        echo '<h2>Dodaj Kategorię</h2>';
        echo '<form method="post" action="admin.php?action=add_category">
                <label>Nazwa kategorii: <input type="text" name="nazwa" required></label><br>
                <label>Kategoria nadrzędna: 
                    <select name="matka">
                        <option value="0">Brak</option>';

        $result = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
            }
        }

        echo '</select></label><br>
                <button type="submit" name="dodaj_kategorie">Dodaj kategorię</button>
              </form>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_kategorie'])) {
            $nazwa = $mysqli->real_escape_string($_POST['nazwa']);
            $matka = (int)$_POST['matka'];

            $stmt = $mysqli->prepare("INSERT INTO kategorie (nazwa, matka) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("si", $nazwa, $matka);
                if ($stmt->execute()) {
                    echo "<p style='color: green;'>Kategoria '$nazwa' została dodana.</p>";
                } else {
                    echo "<p style='color: red;'>Błąd dodawania kategorii: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<p style='color: red;'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
            }
        }
    }
    // Wyświetlanie listy kategorii
    elseif ($action === 'list_categories') {
        echo '<h2 style="text-align: center;">Lista Kategorii</h2>';
        $result = $mysqli->query("SELECT id, nazwa, matka FROM kategorie");
        if ($result && $result->num_rows > 0) {
            echo '<table border="1" style="width: 80%; margin: 20px auto; border-collapse: collapse; text-align: center;">
                    <thead>
                        <tr style="background-color: #798645; color: #fff;">
                            <th>ID</th>
                            <th>Nazwa</th>
                            <th>Nadrzędna</th>
                            <th>Akcje</th>
                        </tr>
                    </thead>
                    <tbody>';
            while ($row = $result->fetch_assoc()) {
                $nadrzedna = $row['matka'] == 0 ? "Brak" : $mysqli->query("SELECT nazwa FROM kategorie WHERE id = {$row['matka']}")->fetch_assoc()['nazwa'];
                echo '<tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . htmlspecialchars($row['nazwa']) . '</td>
                        <td>' . htmlspecialchars($nadrzedna) . '</td>
                        <td>
                            <a href="admin.php?action=edit_category&id=' . $row['id'] . '">Edytuj</a> |
                            <a href="admin.php?action=delete_category&id=' . $row['id'] . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę kategorię?\');">Usuń</a>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p style="text-align: center; color: red;">Brak kategorii w bazie danych.</p>';
        }
    }
// Edycja kategorii
elseif ($action === 'edit_category' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $mysqli->query("SELECT * FROM kategorie WHERE id = $id");
    if ($result && $row = $result->fetch_assoc()) {
        echo '<h2>Edytuj Kategorię</h2>';
        echo '<form method="post" action="admin.php?action=edit_category&id=' . $id . '">
                <div class="form-group">
                    <label>Nazwa kategorii: 
                        <input type="text" name="nazwa" value="' . htmlspecialchars($row['nazwa']) . '" required>
                    </label>
                </div>
                <div class="form-group">
                    <label>Kategoria nadrzędna: 
                        <select name="matka">
                            <option value="0">Brak</option>';
        
        $categories = $mysqli->query("SELECT id, nazwa FROM kategorie WHERE matka = 0 AND id != $id");
        if ($categories) {
            while ($cat = $categories->fetch_assoc()) {
                $selected = ($row['matka'] == $cat['id']) ? 'selected' : '';
                echo "<option value='{$cat['id']}' {$selected}>{$cat['nazwa']}</option>";
            }
        }

        echo '</select>
                    </label>
                </div>
                <button type="submit" name="edytuj_kategorie">Zapisz zmiany</button>
                <a href="admin.php?action=list_categories" class="button">Anuluj</a>
              </form>';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edytuj_kategorie'])) {
            $nazwa = $mysqli->real_escape_string($_POST['nazwa']);
            $matka = (int)$_POST['matka'];

            // Sprawdzanie czy kategoria nie jest przypisywana sama do siebie
            if ($id === $matka) {
                echo "<p class='error-message'>Kategoria nie może być swoją własną kategorią nadrzędną.</p>";
            } 
            // Sprawdzanie czy nie próbujemy przypisać kategorii do jej własnej podkategorii
            elseif ($mysqli->query("SELECT id FROM kategorie WHERE matka = $id")->num_rows > 0 && $matka != 0) {
                echo "<p class='error-message'>Nie można przypisać kategorii do jej własnej podkategorii.</p>";
            }
            else {
                $stmt = $mysqli->prepare("UPDATE kategorie SET nazwa = ?, matka = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("sii", $nazwa, $matka, $id);
                    if ($stmt->execute()) {
                        echo "<p class='success-message'>Kategoria została zaktualizowana.</p>";
                        // Przekierowanie po 2 sekundach
                        echo "<script>setTimeout(function() { window.location = 'admin.php?action=list_categories'; }, 2000);</script>";
                    } else {
                        echo "<p class='error-message'>Błąd edycji kategorii: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p class='error-message'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
                }
            }
        }
    } else {
        echo "<p class='error-message'>Nie znaleziono kategorii.</p>";
    }
}

    // Usuwanie produktu
    elseif ($action === 'delete_product' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare("DELETE FROM produkty WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Produkt został usunięty.</p>";
        } else {
            echo "<p style='color: red;'>Błąd usuwania produktu: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
    }
}


    // Edycja kategorii
    elseif ($action === 'edit_category' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $result = $mysqli->query("SELECT * FROM kategorie WHERE id = $id");
        if ($result && $row = $result->fetch_assoc()) {
            echo '<h2>Edytuj Kategorię</h2>';
            echo '<form method="post" action="admin.php?action=edit_category&id=' . $id . '">
                    <label>Nazwa kategorii: <input type="text" name="nazwa" value="' . htmlspecialchars($row['nazwa']) . '" required></label><br>
                    <button type="submit" name="edytuj_kategorie">Zapisz zmiany</button>
                  </form>';

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edytuj_kategorie'])) {
                $nazwa = $mysqli->real_escape_string($_POST['nazwa']);
                $stmt = $mysqli->prepare("UPDATE kategorie SET nazwa = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $nazwa, $id);
                    if ($stmt->execute()) {
                        echo "<p style='color: green;'>Kategoria została zaktualizowana.</p>";
                    } else {
                        echo "<p style='color: red;'>Błąd edycji kategorii: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p style='color: red;'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>Nie znaleziono kategorii.</p>";
        }
    }
    // Usuwanie kategorii
    elseif ($action === 'delete_category' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $mysqli->prepare("DELETE FROM kategorie WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Kategoria została usunięta.</p>";
            } else {
                echo "<p style='color: red;'>Błąd usuwania kategorii: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p style='color: red;'>Błąd przygotowania zapytania: " . $mysqli->error . "</p>";
        }
    }
    
}



?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FEFAE0;
            margin: 0;
            padding: 20px;
        }

        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #F2EED7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .user-info {
            text-align: right;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 5px;
        }

        .menu-buttons {
            text-align: center;
            margin: 20px 0;
        }

        button, .button {
            padding: 10px 15px;
            margin: 5px;
            background-color: #798645;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }

        button:hover, .button:hover {
            background-color: #626F47;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #798645;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .success-message {
            color: green;
            background-color: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .error-message {
            color: red;
            background-color: #ffebee;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }

        .action-links a {
            color: #798645;
            text-decoration: none;
            margin: 0 5px;
        }

        .action-links a:hover {
            color: #626F47;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<a href="index.php" class="button">Powrót do strony głównej</a>
</body>
</html>