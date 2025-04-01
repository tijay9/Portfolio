<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // Connexion à la base de données
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

    // Utiliser une requête préparée pour éviter les injections SQL
    $query = "SELECT * FROM utilisateurs WHERE nom=?";
    $stmt = $connexion->prepare($query);
    
    // Vérifier si la préparation de la requête a échoué
    if (!$stmt) {
        die("Erreur de préparation de la requête: " . $connexion->error);
    }

    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si des résultats ont été trouvés
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Vérifier si le mot de passe correspond
        if (password_verify($password, $row['mot_de_passe'])) {
            // Mot de passe correct, créer une session
            session_start();
            $_SESSION['login'] = $login;
            $_SESSION['id'] = $row['id'];

            // Rediriger l'utilisateur vers la page souhaitée
            header('Location: homelog.php');
            exit();
        } else {
            // Mot de passe incorrect, afficher un message d'erreur
            echo '<p style="color: red;">Identifiants incorrects. Veuillez réessayer.</p>';
        }
    } else {
        // Utilisateur non trouvé, afficher un message d'erreur
        echo '<p style="color: red;">Identifiants incorrects. Veuillez réessayer.</p>';
    }

    // Fermer la connexion à la base de données
    $stmt->close();
    $connexion->close();
}
?>
