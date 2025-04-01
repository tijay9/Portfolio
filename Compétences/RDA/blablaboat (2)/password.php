<?php
$serveur = "localhost";
$utilisateur = "wcueymat_user";
$motDePasse = "user1234HTTP";
$nomBaseDeDonnees = "wcueymat_blablaboat_1";

$conn = new mysqli($serveur, $utilisateur, $motDePasse, $nomBaseDeDonnees);

if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST['login'];
    $mot_de_passe = $_POST['password'];

    $sql = "SELECT * FROM utilisateurs WHERE nom = '$nom_utilisateur' AND mot_de_passe = '$mot_de_passe'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "Connexion réussie. Vous êtes maintenant connecté.";
        // Ajoutez ici le code pour rediriger l'utilisateur vers la page appropriée après la connexion réussie.
    } else {
        echo "Identifiants incorrects. Veuillez réessayer.";
    }
}

$conn->close();
?>
