<?php
session_start();

include("cfg.php");

// Funkcja usuwania produktu z koszyka
function UsunZKoszyka($id) {
    // Sprawdzamy, czy dany produkt istnieje w koszyku
    if (isset($_SESSION['koszyk'][$id])) {
        unset($_SESSION['koszyk'][$id]); // Usuwamy produkt z koszyka
    }
}

// Funkcja edycji ilości produktu w koszyku
function EdytujKoszyk($id, $ilosc) {
    global $mysqli;

    // Pobieramy ilość produktu dostępnego w magazynie z bazy danych
    $result = $mysqli->query("SELECT ilosc FROM produkty WHERE id = $id");
    if ($result) {
        $produkt = $result->fetch_assoc();
        $aktualnaIlosc = (int)$produkt['ilosc'];

        // Sprawdzamy, czy użytkownik nie próbuje ustawić ilości większej niż dostępna w magazynie
        if ($ilosc > 0 && $ilosc <= $aktualnaIlosc) {
            $_SESSION['koszyk'][$id] = $ilosc; // Aktualizujemy ilość w koszyku
        } elseif ($ilosc > $aktualnaIlosc) {
            // Komunikat o błędzie, jeśli ilość przekracza dostępny zapas
            echo "<p style='color: red; text-align: center;'>Nie można ustawić większej ilości niż dostępna w magazynie.</p>";
        } elseif ($ilosc <= 0) {
            unset($_SESSION['koszyk'][$id]); // Jeśli ilość <= 0, usuwamy produkt z koszyka
        }
    }
}

// Obsługa formularzy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edytuj_koszyk'])) {
        // Obsługa zmiany ilości produktu w koszyku
        $id = (int)$_POST['id'];
        $ilosc = (int)$_POST['ilosc'];
        EdytujKoszyk($id, $ilosc);
    } elseif (isset($_POST['usun_z_koszyka'])) {
        // Obsługa usuwania produktu z koszyka
        $id = (int)$_POST['id'];
        UsunZKoszyka($id);
    }
}

// Jeśli koszyk jest pusty, wyświetl komunikat i zakończ działanie skryptu
if (!isset($_SESSION['koszyk']) || empty($_SESSION['koszyk'])) {
    echo "<p style='text-align: center; font-size: 1.5rem; margin-top: 20px;'>Twój koszyk jest pusty.</p>";
    echo "<div style='text-align: center;'><a href='index.php?idp=sklep' style='font-size: 1.2rem; color: #798645;'>Wróć do sklepu</a></div>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koszyk</title>
    <style>
        /* css */
        body {
            font-family: Arial, sans-serif;
            background-color: #FEFAE0;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .koszyk-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #F2EED7;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #798645;
            color: white;
        }

        table td {
            background-color: #fff;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .actions button {
            padding: 10px 15px;
            background-color: #798645;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .actions button:hover {
            background-color: #626F47;
        }

        .back-to-shop {
            text-align: center;
        }

        .back-to-shop a {
            text-decoration: none;
            color: #798645;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .back-to-shop a:hover {
            color: #626F47;
        }

        .total {
            text-align: right;
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Twój Koszyk</h1>
    <div class="koszyk-container">
        <table>
            <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Ilość</th>
                    <th>Cena brutto</th>
                    <th>Łączna cena</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $suma = 0; // Zmienna przechowująca sumę koszyka
                foreach ($_SESSION['koszyk'] as $id => $ilosc) {
                    // Pobieramy dane produktu z bazy danych
                    $result = $mysqli->query("SELECT tytul, cena_netto, vat FROM produkty WHERE id = $id");
                    if ($row = $result->fetch_assoc()) {
                        $cena_brutto = $row['cena_netto'] * (1 + $row['vat'] / 100); // Oblicz cenę brutto
                        $laczna_cena = $cena_brutto * $ilosc; // Oblicz łączną cenę
                        $suma += $laczna_cena; // Dodaj do sumy koszyka

                        echo "<tr>
                                <td>{$row['tytul']}</td>
                                <td>
                                    <form method='post' class='actions'>
                                        <input type='number' name='ilosc' value='{$ilosc}' min='1' style='width: 60px;'>
                                        <input type='hidden' name='id' value='{$id}'>
                                        <button type='submit' name='edytuj_koszyk'>Zapisz</button>
                                    </form>
                                </td>
                                <td>{$cena_brutto} zł</td>
                                <td>{$laczna_cena} zł</td>
                                <td>
                                    <form method='post' class='actions'>
                                        <input type='hidden' name='id' value='{$id}'>
                                        <button type='submit' name='usun_z_koszyka'>Usuń</button>
                                    </form>
                                </td>
                              </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
        <div class="total">Suma: <?php echo $suma; ?> zł</div> <!-- Wyświetlanie całkowitej sumy -->
        <div class="back-to-shop">
            <a href="index.php?idp=sklep">Wróć do sklepu</a> <!-- Link powrotny do sklepu -->
        </div>
    </div>
</body>
</html>
