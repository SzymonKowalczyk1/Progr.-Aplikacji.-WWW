<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formularz Kontaktowy</title>
    <link rel="stylesheet" href="css/style.css">  
</head>
<body>
    <?php
    // Wczytanie konfiguracji, np. do połączenia z bazą danych lub innych ustawień
    include("cfg.php"); 

    // Importowanie niezbędnych klas z biblioteki PHPMailer
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Automatyczne ładowanie plików potrzebnych do działania PHPMailer
    require 'vendor/autoload.php';  

    /**
     * Funkcja do wyświetlenia formularza kontaktowego.
     * Formularz pozwala użytkownikowi na wpisanie tematu, e-maila i treści wiadomości.
     */
    function PokazKontakt()
    {
        $wynik = '
        <div class="contact-form">
            <h2 class="heading">Skontaktuj się z nami</h2>
            <form method="post" name="ContactForm" enctype="multipart/form-data" action="'.$_SERVER['REQUEST_URI'].'">
                <label for="temat">Temat wiadomości</label>
                <input type="text" name="temat" id="temat" class="formField" placeholder="Wpisz temat" required> 
                
                <label for="email">Twój adres e-mail</label>
                <input type="email" name="email" id="email" class="formField" placeholder="Wpisz adres e-mail" required> 
                
                <label for="message">Treść wiadomości</label>
                <textarea id="message" name="tresc" class="formField" placeholder="Wpisz treść wiadomości" required></textarea>
                
                <button type="submit" name="contact_submit" class="contact-form-btn">Wyślij</button>
            </form>
        </div>';
        return $wynik;
    }

    /**
     * Funkcja wysyłająca e-mail na podstawie danych z formularza.
     * Korzysta z biblioteki PHPMailer do obsługi wysyłki e-mail.
     */
    function WyslijMailaKontakt()
    {
        global $email_pass;
        
        // Sprawdzenie, czy wszystkie pola formularza zostały wypełnione
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo '[nie_wypelniles_pola]';
            echo PokazKontakt(); // Wyświetlanie formularza w przypadku błędów
        } else {
            // Pobieranie danych z formularza
            $temat = htmlspecialchars($_POST['temat']);
            $tresc = htmlspecialchars($_POST['tresc']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  
            
            // Walidacja adresu e-mail
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '[niepoprawny_adres_email]';
                echo PokazKontakt(); // Wyświetlanie formularza, jeśli e-mail jest niepoprawny
                return;
            }

            $mail = new PHPMailer(true);  

            try {
                 // Konfiguracja serwera SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  
                $mail->SMTPAuth = true;
                $mail->Username = 'szymon.progwww@gmail.com';  
                $mail->Password = $email_pass;  
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port = 587;  

                // Ustawienia odbiorcy i nadawcy
                $mail->setFrom('szymon.progwww@gmail.com', 'Formularz kontaktowy');  
                $mail->addAddress($email);  // Ustawienie adresu odbiorcy jako wprowadzonego w formularzu

                // Treść wiadomości
                $mail->isHTML(true); 
                $mail->Subject = $temat;  
                $mail->Body    = $tresc;  
                $mail->AltBody = strip_tags($tresc);  

                // Próba wysłania wiadomości
                $mail->send();
                echo '[Wiadomosc Wyslana]';  // Komunikat o powodzeniu
            } catch (Exception $e) {
                echo "Wiadomość nie mogła zostać wysłana. Błąd: {$mail->ErrorInfo}";  // Komunikat o błędzie
            }
        }
    }

    // Obsługa formularza po wysłaniu
    if (isset($_POST['contact_submit'])) {
        WyslijMailaKontakt();  // Wywołanie funkcji wysyłania e-maila
    } else {
        echo PokazKontakt(); // Wyświetlenie formularza w przypadku braku danych w formularzu
    }
    ?>
</body>
</html>
