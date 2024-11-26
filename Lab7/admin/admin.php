<?php
session_start();

function FormularzLogowania() {
    echo '
    <form method="post" action="">
        <label for="username">Login:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required><br>
        <button type="submit" name="login">Zaloguj</button>
    </form>';
}

include("../cfg.php"); // Użycie $link jako zmiennej połączenia

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    if (isset($_POST['login'])) {
        if ($_POST['username'] === $login && $_POST['password'] === $pass) {
            $_SESSION['logged_in'] = true;
        } else {
            echo "Błędne dane logowania.<br>";
            FormularzLogowania();
            exit();
        }
    } else {
        FormularzLogowania();
        exit();
    }
}

// Funkcja: Wyświetlanie listy podstron
function ListaPodstron() {
    global $link; // Użyj globalnej zmiennej $link

    $sql = "SELECT id, page_title FROM page_list";
    $result = mysqli_query($link, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['page_title']}</td>
                <td>
                    <a href='?action=edit&id={$row['id']}'>Edytuj</a> |
                    <a href='?action=delete&id={$row['id']}'>Usuń</a>
                </td>
            </tr>";
        }
        echo "</table>";
        echo "<br><a href='?action=add'>Dodaj nową podstronę</a>";
    } else {
        echo "Brak podstron.";
        echo "<br><a href='?action=add'>Dodaj nową podstronę</a>";
    }
}

// Funkcja: Dodawanie nowej podstrony
function DodajNowaPodstrone() {
    global $link;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $status = isset($_POST['active']) ? 1 : 0;

        $sql = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', '$status')";
        mysqli_query($link, $sql);

        echo "Podstrona została dodana.";
        echo "<br><a href='admin.php'>Powrót do listy podstron</a>";
    } else {
        echo '<form method="post" action="">
            <label for="title">Tytuł:</label>
            <input type="text" id="title" name="title" required><br>
            <label for="content">Treść:</label>
            <textarea id="content" name="content"></textarea><br>
            <label for="active">Aktywna:</label>
            <input type="checkbox" id="active" name="active"><br>
            <button type="submit">Dodaj</button>
        </form>';
    }
}

// Funkcja: Edycja podstrony
function EdytujPodstrone($id) {
    global $link;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $status = isset($_POST['active']) ? 1 : 0;

        $sql = "UPDATE page_list SET page_title = '$title', page_content = '$content', status = '$status' WHERE id = '$id' LIMIT 1";
        mysqli_query($link, $sql);

        echo "Podstrona została zaktualizowana.";
        echo "<br><a href='admin.php'>Powrót do listy podstron</a>";
    } else {
        $sql = "SELECT * FROM page_list WHERE id = '$id' LIMIT 1";
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_assoc($result);

        echo '<form method="post" action="">
            <label for="title">Tytuł:</label>
            <input type="text" id="title" name="title" value="' . htmlspecialchars($row['page_title']) . '"><br>
            <label for="content">Treść:</label>
            <textarea id="content" name="content">' . htmlspecialchars($row['page_content']) . '</textarea><br>
            <label for="active">Aktywna:</label>
            <input type="checkbox" id="active" name="active" ' . ($row['status'] ? 'checked' : '') . '><br>
            <button type="submit">Zapisz</button>
        </form>';
    }
}

// Funkcja: Usuwanie podstrony
function UsunPodstrone($id) {
    global $link;

    $sql = "DELETE FROM page_list WHERE id = '$id' LIMIT 1";
    mysqli_query($link, $sql);

    echo "Podstrona została usunięta.";
    echo "<br><a href='admin.php'>Powrót do listy podstron</a>";
}

// Obsługa akcji na podstawie parametrów URL
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add') {
        DodajNowaPodstrone();
    } elseif ($action === 'edit' && isset($_GET['id'])) {
        EdytujPodstrone((int)$_GET['id']);
    } elseif ($action === 'delete' && isset($_GET['id'])) {
        UsunPodstrone((int)$_GET['id']);
    } else {
        echo "Nieznana akcja.";
    }
} else {
    ListaPodstron();
}
?>
    