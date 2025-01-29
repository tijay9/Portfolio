<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclure PHPMailer (modifie le chemin si nécessaire)
require '../vendor/autoload.php'; 

// Adresse email de réception
$receiving_email_address = 'eruam32@gmail.com';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    if (!$email) {
        die('Adresse email invalide.');
    }

    // Initialisation de PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configuration du serveur SMTP (modifie avec tes infos)
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Serveur SMTP (ex: Gmail)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'blablaboat@canevy-mendhy.zd.lu'; // Remplace avec ton email SMTP
        $mail->Password   = 'Blablaboat.972'; // ⚠️ Utilise un mot de passe d'application Gmail !
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // Port SMTP
.
        // Paramètres de l'email
        $mail->setFrom($email, $name);
        $mail->addAddress($receiving_email_address);
        $mail->Subject = $subject;
        $mail->Body    = "Nom: $name\nEmail: $email\n\nMessage:\n$message";

        // Envoyer l'email
        if ($mail->send()) {
            echo "Message envoyé avec succès !";
        } else {
            echo "Erreur lors de l'envoi du message.";
        }
    } catch (Exception $e) {
        echo "Erreur : {$mail->ErrorInfo}";
    }
} else {
    die('Requête invalide.');
}
?>
