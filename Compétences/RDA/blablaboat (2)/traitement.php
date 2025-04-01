<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Chargement de la classe PHPMailer
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Démarrer la session
session_start();

$serveur = "localhost";
$utilisateur = "wcueymat_user";
$motDePasse = "user1234HTTP";
$nomBaseDeDonnees = "wcueymat_blablaboat_1";

// Créer une connexion à la base de données
$connexion = new mysqli($serveur, $utilisateur, $motDePasse, $nomBaseDeDonnees);

// Vérifier la connexion
if ($connexion->connect_error) {
    die("Connection failed: " . $connexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecter les données du formulaire
    $username = mysqli_real_escape_string($connexion, $_POST["username"]);
    $email = mysqli_real_escape_string($connexion, $_POST["email"]);
    $password = password_hash(mysqli_real_escape_string($connexion, $_POST["password"]), PASSWORD_DEFAULT);

    // Vérifier si l'e-mail est déjà utilisé
    $sql_check_email = "SELECT email FROM utilisateurs WHERE email = ?";
    $stmt_check_email = $connexion->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    if ($stmt_check_email->num_rows > 0) {
        // L'e-mail est déjà utilisé
        echo "L'adresse e-mail est déjà utilisée. Veuillez choisir une autre adresse e-mail.";
        $stmt_check_email->close();
        $connexion->close();
        exit(); // Arrêter l'exécution du script
    }

    // Continuer le processus d'inscription si l'e-mail n'est pas déjà utilisé
    $stmt_check_email->close();

    // Générer un mot de passe unique pour la validation par e-mail
    $validationCode = bin2hex(random_bytes(16));

    // Insérer les données dans la base de données
    $sql_insert = "INSERT INTO utilisateurs (nom, mot_de_passe, email, validation_code, valide) VALUES (?, ?, ?, ?, 0)";
    $stmt_insert = $connexion->prepare($sql_insert);
    $stmt_insert->bind_param("ssss", $username, $password, $email, $validationCode);

    if ($stmt_insert->execute()) {
        // Envoyer un e-mail de validation avec le lien contenant le code de validation

        // Créer une instance de PHPMailer
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'node148-eu.n0c.com';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 587; // Utilisez le port SMTP 587
        $phpmailer->Username = 'blablaboat@canevy-mendhy.zd.lu';
        $phpmailer->Password = 'Blablaboat.972'; // Utilisez le mot de passe de votre compte de messagerie.

        // Paramètres du message
        $phpmailer->setFrom('blablaboat@canevy-mendhy.zd.lu');
        $phpmailer->addAddress($email);
        $phpmailer->Subject = "Validation de l'inscription";
        $phpmailer->Body = "Cliquez sur le lien suivant pour valider votre inscription : https://canevy-mendhy.zd.lu/blablaboat/traitement.php?email=$email&code=$validationCode";

        // Envoyer le message
        if ($phpmailer->send()) {
            echo "Un e-mail de validation a été envoyé à votre adresse e-mail. Veuillez cliquer sur le lien pour activer votre compte.";

            // Ajouter le code de validation à la session (pour vérification ultérieure)
            $_SESSION['validation_code'] = $validationCode;
            $_SESSION['validation_time'] = time(); // Enregistrez le timestamp actuel

        } else {
            echo "Erreur lors de l'envoi de l'e-mail de validation.";
            echo "Erreur : " . $phpmailer->ErrorInfo;
        }

        // Fermer la connexion après avoir utilisé les opérations nécessitant la connexion active
        $stmt_insert->close();
        $connexion->close();
    } else {
        echo "Erreur lors de l'inscription.";
        // Enregistrez l'erreur détaillée pour le débogage (ne la montrez pas aux utilisateurs en production)
        error_log("Erreur : " . $stmt_insert->error);

        // Fermer la connexion en cas d'erreur
        $stmt_insert->close();
        $connexion->close();
    }
}

// Vérifier le code de validation depuis le lien
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['email']) && isset($_GET['code'])) {
    // Collecter les données de l'URL
    $emailFromURL = mysqli_real_escape_string($connexion, $_GET['email']);
    $codeFromURL = mysqli_real_escape_string($connexion, $_GET['code']);

    // Vérifier si le code correspond à celui dans la session et si le délai n'a pas expiré
    if (isset($_SESSION['validation_code']) && isset($_SESSION['validation_time'])) {
        if ($_SESSION['validation_code'] === $codeFromURL && (time() - $_SESSION['validation_time']) <= 60) {
            // Mettre à jour la base de données pour marquer l'utilisateur comme validé
            $sql_update = "UPDATE utilisateurs SET valide = 1 WHERE email = ? AND validation_code = ?";
            $stmt_update = $connexion->prepare($sql_update);
            $stmt_update->bind_param("ss", $emailFromURL, $codeFromURL);

            if ($stmt_update->execute()) {
                echo "Inscription réussie!";
            } else {
                echo "Erreur lors de la validation de l'inscription.";
                // Enregistrez l'erreur détaillée pour le débogage (ne la montrez pas aux utilisateurs en production)
                error_log("Erreur : " . $stmt_update->error);
            }

            // Fermer la connexion après avoir utilisé les opérations nécessitant la connexion active
            $stmt_update->close();
            $connexion->close();
        } else {
            echo "Le lien de validation a expiré ou le code est incorrect.";
        }

        // Nettoyer la session après la vérification
        unset($_SESSION['validation_code']);
        unset($_SESSION['validation_time']);
    } else {
        echo "Session expirée. Veuillez réessayer.";
    }
}
?>
