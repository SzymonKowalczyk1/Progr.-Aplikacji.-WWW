<?php
include('cfg.php');  // Połączenie z bazą danych

// Pobranie ID z parametru GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;  // Domyślnie 1, jeśli nie podano ID

// Zapytanie do bazy danych o szczegóły strony
$sql = "SELECT page_title, page_content FROM page_list WHERE id = '$id' AND status = 1 LIMIT 1";
$result = mysqli_query($link, $sql);

// Sprawdzenie, czy strona istnieje
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "<h1>" . htmlspecialchars($row['page_title']) . "</h1>";
    echo "<p>" . nl2br(htmlspecialchars($row['page_content'])) . "</p>";
} else {
    echo "Podstrona nie istnieje lub jest nieaktywna.";
}
?>
