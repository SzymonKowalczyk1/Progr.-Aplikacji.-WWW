<?php
$dbhost = "localhost";
$dbuser = "root"; 
$dbpass = ""; 
$baza = "moja_strona";

$login = "admin";
$pass = "12345";
$email_pass="afwm deuv ohuu kgzm";

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);


if (!$link) {
    die('<b>Przerwane połączenie: </b>' . mysqli_connect_error());
} else {
    echo '<b>Połączono z bazą danych.</b>';
}
?>
