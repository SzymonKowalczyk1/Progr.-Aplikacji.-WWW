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
    include("cfg.php"); 

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

   
    require 'vendor/autoload.php';  

    
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

   
    function WyslijMailaKontakt()
    {
        global $email_pass;
        
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo '[nie_wypelniles_pola]';
            echo PokazKontakt();
        } else {
            
            $temat = htmlspecialchars($_POST['temat']);
            $tresc = htmlspecialchars($_POST['tresc']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);  

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo '[niepoprawny_adres_email]';
                echo PokazKontakt();
                return;
            }

            $mail = new PHPMailer(true);  

            try {
                
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';  
                $mail->SMTPAuth = true;
                $mail->Username = 'szymon.progwww@gmail.com';  
                $mail->Password = $email_pass;  
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port = 587;  

                
                $mail->setFrom('szymon.progwww@gmail.com', 'Formularz kontaktowy');  
                $mail->addAddress($email);  

               
                $mail->isHTML(true); 
                $mail->Subject = $temat;  
                $mail->Body    = $tresc;  
                $mail->AltBody = strip_tags($tresc);  

            
                $mail->send();
                echo '[Wiadomosc Wyslana]';
            } catch (Exception $e) {
                echo "Wiadomość nie mogła zostać wysłana. Błąd: {$mail->ErrorInfo}";
            }
        }
    }

   
    if (isset($_POST['contact_submit'])) {
        WyslijMailaKontakt();  
    } else {
        
        echo PokazKontakt();
    }
    ?>
</body>
</html>
