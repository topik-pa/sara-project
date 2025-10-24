<?php

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
$debug = 0;


// Verifica che la richiesta sia POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Colleziona i dati della form
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $cognome = isset($_POST['cognome']) ? trim($_POST['cognome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    // $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $cf = isset($_POST['cf']) ? trim($_POST['cf']) : '';
    $elenco = isset($_POST['elenco']) ? trim($_POST['elenco']) : '';
    $privacy = isset($_POST['privacy']) ? trim($_POST['privacy']) : false;

    if (!$privacy) {
        header("location: /email-error");
        exit('Privacy non valida.');
    }

    // Validazione minima
    if (empty($nome) || empty($cognome) || empty($email) || empty($phone) /*|| empty($address)*/ || empty($cf) || empty($elenco)) {
        header("location: /email-error");
        exit('Tutti i campi sono obbligatori.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("location: /email-error");
        exit('Indirizzo email non valido.');
    }
    // Costruzione dell'email
    $to = "marcopavan.mp@gmail.com";
    $subject = 'Nuova prenotazione da farmaciaattimis.fvg.it';
    $body = "Hai ricevuto una nuova prenotazione dal form Prenota:" . "<br>" . "<br>" .
            "Nome: $nome" . "<br>" .
            "Cognome: $cognome" . "<br>" .
            "Telefono: $phone" . "<br>" .
            "CF: $cf" . "<br>" .
            "Email: $email" . "<br>" .
            "Privacy: $privacy" . "<br>" . "<br>" .
            "Elenco prodotti: $elenco";

    $headers = "From: $nome $cognome <$email>\r\n" .
               "Reply-To: $email\r\n" .
               "Content-Type: text/plain; charset=UTF-8\r\n";
    // Invio email
    try {
        // Creare un’istanza della classe PHPMailer
        $mail = new PHPMailer($debug);
        if ($debug) {
            // Emette un log dettagliato da
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
        }
        // Autenticazione tramite SMTP
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        // Login
        $mail->Host = "mail.tophost.it";
        $mail->Port = 587;
        $mail->Username = "farmaciaattimis.fvg.it";
        $mail->Password = "no1iMob1";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->setFrom('info@farmaciaattimis.fvg.it', 'Sara from farmaciaattimis.fvg.it');
        $mail->addAddress($to);
        // $mail->AddCC('moneghinisara@gmail.com');
        // $mail->addAttachment("/home/user/Desktop/immagineesempio.png", "immagineesempio.png");
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;
        $mail->send();
    } catch (Exception $e) {
        header("location: /email-error");
        echo "Message could not be sent. Mailer Error: ".$e->getMessage();
        exit('Mailer Error');
    }
    header("location: /email-confirm");
    exit('OK: email inviata');
} else {
    echo 'Metodo non consentito.';
}
?>
