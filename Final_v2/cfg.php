<?php
//dane logowanie do bazydanych
$dbhost = "localhost";
$dbuser = "root"; 
$dbpass = ""; 
$baza = "moja_strona";
$login = "admin";
$pass = "12345";
//klucz wysylki maili
$email_pass="afwm deuv ohuu kgzm";

//polaczenie z baza danych
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $baza);

//sprawdzenie czy polaczenie dziala
// if (!$mysqli) {
//     die('<b>Przerwane połączenie: </b>' . mysqli_connect_error());
// } else {
//     echo '<b>Połączono z bazą danych.</b>';
// }

?>
