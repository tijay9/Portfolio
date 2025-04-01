<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.html');
    exit();
}

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

$connexion->set_charset("utf8");

// Récupérer l'ID de l'utilisateur à partir de la session
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

// Requête SQL pour récupérer les données de la table bateau pour l'utilisateur spécifique
$select_query = "SELECT b.nom_bateau, b.immatriculation, b.description, b.nbr_place, b.vitesse, u.nom AS nom_du_proprietaire 
                FROM bateau b
                JOIN utilisateurs u ON b.id_proprietaire = u.id
                WHERE b.id_proprietaire = ?";
$stmt = $connexion->prepare($select_query);

// Vérifier si la préparation de la requête a échoué
if ($stmt === false) {
    die("Erreur de préparation de la requête: " . $connexion->error);
}



$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil de l'Utilisateur</title>
    <style>
        /* Styles pour le formulaire de réservation de billet */
        body {
            font-family: Arial, sans-serif;
            background: url("baniere2.png");
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: grid;
            height: 100vh;
            place-items: center;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        nav {
            background: #2c3e50; /* Bleu marine plus foncé */
            color: white;
            padding: 10px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px; /* Ajoute un peu de padding autour du texte */
            border-right: 1px solid #fff; /* Ajoute une bordure à droite de chaque lien */
        }

        nav a:last-child {
            border-right: none; /* Assurez-vous que le dernier élément n'a pas de bordure à droite */
        }

        header {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
            margin-top: 0;
            position: fixed;
            top: 0;
        }

        header > div {
            display: flex;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header .logo {
            max-width: 50px;
            max-height: 50px;
            margin-right: 10px;
        }

        .container {
            width: 310px;
            padding: 30px;
            border: 2px solid #ccc;
            border-radius: 10px;
            /* Ajout d'une valeur pour arrondir les bords */
            margin: auto;
            background-color: beige;
            /* Ajout de la couleur beige */
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: darkred;
        }

        /* Affichage du tableau */
        .tab {
            margin-top: 10px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 2px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        td {
            background-color: white;
        }
    </style>
</head>

<body>

<header>
    <div>
        <a href="homelog.php"><img class="logo" src="BlaBlaBoat.png" alt="Logo"></a>
        <h1>Profil de l'utilisateur</h1>
    </div>
    <nav>
        <a href="homelog.php">Accueil</a>
        <a href="bateau.php">Ajouter un bateau</a>
        <a href="reservation.php">Réservez votre voyage</a>
        <a href="trajet.php">Publier un trajet</a>
        <?php
        if (isset($_SESSION['login'])) {
            echo '<a href="deconnexion.php">Déconnexion</a>';
        }
        ?>
    </nav>
</header>


    <div>
        <h2>Bateaux disponibles</h2>
        <table>
            <tr>
                <th>Nom du Bateau</th>
                <th>Immatriculation</th>
                <th>Description</th>
                <th>Nombres de places</th>
                <th>Vitesse de Croisière</th>
                <th>Nom du propriétaire</th>
            </tr>

            <?php
            // Afficher les données des bateaux pour l'utilisateur connecté
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nom_bateau']) . "</td>";
                echo "<td>" . htmlspecialchars($row['immatriculation']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['nbr_place']) . "</td>";
                echo "<td>" . htmlspecialchars($row['vitesse']) . "km</td>";
                echo "<td>" . htmlspecialchars($row['nom_du_proprietaire']) . "</td>";
                echo "</tr>";
            }
            ?>

        </table>
    </div>
    
    <div>
    <h2>Trajets proposés</h2>
    <table>
        <tr>
            <th>Port de Départ</th>
            <th>Port d'Arrivée</th>
            <th>Date de Départ</th>
            <th>Prix par Passager</th>
            <th>Places Disponibles</th>
            <th>Nom du Propriétaire</th>
        </tr>

        <?php
        // Requête SQL pour récupérer les trajets de l'utilisateur spécifique
        $select_trajet_query = "SELECT t.port_depart, t.port_arrivee, t.date_depart, t.prix_par_passagers, t.place_disponible, u.nom AS nom_du_proprietaire
                               FROM trajet t
                               JOIN utilisateurs u ON t.id_proprietaire = u.id
                               WHERE t.id_proprietaire = ?";
        $stmt_trajet = $connexion->prepare($select_trajet_query);

        // Vérifier si la préparation de la requête a échoué
        if ($stmt_trajet === false) {
            die("Erreur de préparation de la requête: " . $connexion->error);
        }

        $stmt_trajet->bind_param("i", $user_id);
        $stmt_trajet->execute();
        $result_trajet = $stmt_trajet->get_result();

        // Afficher les données des trajets pour l'utilisateur connecté
        while ($row_trajet = $result_trajet->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row_trajet['port_depart']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['port_arrivee']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['date_depart']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['prix_par_passagers']) . "$</td>";
            echo "<td>" . htmlspecialchars($row_trajet['place_disponible']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['nom_du_proprietaire']) . "</td>";
            echo "</tr>";
        }
        ?>

    </table>
</div>



<div>
    <h2>Réservation</h2>
    <table>
        <tr>
            <th>Port de départ</th>
            <th>Port d'arrivée</th>
            <th>Date de départ</th>
            <th>Prix par passagers</th>
            <th>Durée du trajet</th>
            <th>Distance</th>
            <th>Nom du client</th>
        </tr>

        <?php
        // Requête SQL pour récupérer les trajets de l'utilisateur spécifique
        $select_trajet_query = "SELECT r.port_de_depart, r.port_d_arrivee, r.date_de_depart, r.prix_par_passagers, r.duree_du_trajet, r.distance, u.nom AS nom_du_proprietaire
                               FROM reservation r
                               JOIN utilisateurs u ON r.id_client = u.id
                               WHERE r.id_client = ?";
        $stmt_trajet = $connexion->prepare($select_trajet_query);

        // Vérifier si la préparation de la requête a échoué
        if ($stmt_trajet === false) {
            die("Erreur de préparation de la requête: " . $connexion->error);
        }

        $stmt_trajet->bind_param("i", $user_id);
        $stmt_trajet->execute();
        $result_trajet = $stmt_trajet->get_result();

        // Afficher les données des trajets pour l'utilisateur connecté
        while ($row_trajet = $result_trajet->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row_trajet['port_de_depart']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['port_d_arrivee']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['date_de_depart']) . "</td>";
            echo "<td>" . htmlspecialchars($row_trajet['prix_par_passagers']) . "$</td>";
            echo "<td>" . htmlspecialchars($row_trajet['duree_du_trajet']) . "min</td>";
            echo "<td>" . htmlspecialchars($row_trajet['distance']) . "km</td>";
            echo "<td>" . htmlspecialchars($row_trajet['nom_du_proprietaire']) . "</td>";
            echo "</tr>";
        }
        ?>

    </table>
</div>

</body>

</html>