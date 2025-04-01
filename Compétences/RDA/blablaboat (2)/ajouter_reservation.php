<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.html');
    exit();
}

// Connexion à la base de données
$host = "localhost";
$utilisateur = "wcueymat_user";
$motDePasse = "user1234HTTP";
$baseDeDonnees = "wcueymat_blablaboat_1";

$connexion = new mysqli($host, $utilisateur, $motDePasse, $baseDeDonnees);

// Vérifier la connexion
if ($connexion->connect_error) {
    die("Échec de la connexion à la base de données : " . $connexion->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $id_client = $_POST['id_client'];
    $port_de_depart = $_POST['port_depart'];
    $port_d_arrivee = $_POST['port_arrivee'];
    $date_de_depart = $_POST['date_depart'];
    $prix_par_passagers = $_POST['prix_par_passagers'];
    $duree_du_trajet = $_POST['duree_du_trajet'];
    $distance = $_POST['distance'];

    // Insertion des données dans la table de réservation
    $sql_insert_reservation = "INSERT INTO reservation (id_client, port_de_depart, port_d_arrivee, date_de_depart, prix_par_passagers, duree_du_trajet, distance) 
                                VALUES ('$id_client', '$port_de_depart', '$port_d_arrivee', '$date_de_depart', '$prix_par_passagers', '$duree_du_trajet', '$distance')";

    if ($connexion->query($sql_insert_reservation) === TRUE) {
        echo "Réservation réussie!";
    } else {
        echo "Erreur lors de la réservation : " . $connexion->error;
    }
}

// Fermer la connexion à la base de données
$connexion->close();
?>
