<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function configurerSMTP(PHPMailer $mail) {
    $mail->isSMTP();
    $mail->Host = 'smtp-relay.sendinblue.com';
    $mail->SMTPAuth = true;
    $mail->Username = '8825f9001@smtp-brevo.com'; // Remplace par ton email Sendinblue
    $mail->Password = 'Vh1MBJjgLYncQNAF'; // Remplace par ta clé API SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(htmlspecialchars($_POST['name'] ?? ''));
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $message = trim(strip_tags($_POST['message'] ?? ''));

    if (!$email || empty($name) || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs correctement."]);
        exit();
    }

    try {
        $mail = new PHPMailer(true);
        configurerSMTP($mail);
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('emailjehaneruam@gmail.com', 'Votre Entreprise');
        $mail->addAddress('eruam32@gmail.com'); // Remplace par ton email de réception
        $mail->addReplyTo($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'Nouveau message de contact';
        $mail->Body = "<p><strong>Nom :</strong> $name</p>
                       <p><strong>Email :</strong> $email</p>
                       <p><strong>Message :</strong></p>
                       <p>$message</p>";
        $mail->send();

        echo json_encode(["status" => "success", "message" => "Message envoyé avec succès !"]);
    } catch (Exception $e) {
        error_log("Erreur d'envoi du mail: " . $mail->ErrorInfo);
        echo json_encode(["status" => "error", "message" => "Erreur lors de l'envoi du message."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Requête invalide."]);
}
?>
