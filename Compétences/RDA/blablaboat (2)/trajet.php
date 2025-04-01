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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecter les données du formulaire
    $portDepart = mysqli_real_escape_string($connexion, $_POST["departure"]);
    $portArrivee = mysqli_real_escape_string($connexion, $_POST["arrival"]);
    $dateDepart = mysqli_real_escape_string($connexion, $_POST["departure-date"]);
    $prixParPassager = mysqli_real_escape_string($connexion, $_POST["price"]);
    $id_bateau = mysqli_real_escape_string($connexion, $_POST["boat"]);
    $availableSeats = mysqli_real_escape_string($connexion, $_POST["available-seats"]);

    // Récupérer l'ID de l'utilisateur à partir de la session
    $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

    // Requête SQL pour récupérer les données de la table bateau pour l'utilisateur spécifique
    $select_query = "SELECT id_proprietaire, nom_bateau FROM bateau WHERE id_proprietaire = ?";


    $stmt = $connexion->prepare($select_query);
    $stmt->bind_param("i", $user_id);

    // Requête SQL pour insérer les données dans la table trajet avec l'ID de l'utilisateur
    $insert_query = "INSERT INTO trajet (port_depart, port_arrivee, date_depart, prix_par_passagers, id_bateau, id_proprietaire, place_disponible) 
        VALUES (?,?,?,?,?,?,?)";

    $stmt = $connexion->prepare($insert_query);

    if ($stmt === false) {
        die("Error preparing statement: " . $connexion->error);
    }

    $stmt->bind_param("ssssssi", $portDepart, $portArrivee, $dateDepart, $prixParPassager, $id_bateau, $user_id, $availableSeats);

    if ($stmt->execute()) {
        $message = "Trajet publié avec succès!";
        // Clear form values
        $portDepart = $portArrivee = $dateDepart = $prixParPassager = $id_bateau = $availableSeats = "";
    } else {
        $message = "Erreur lors de la publication du trajet : " . $stmt->error;
    }

    
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publier un Trajet</title>
    <style>
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

    nav {
        background: #2c3e50; 
        color: white;
        padding: 10px;
        text-align: center;
    }

    nav a {
        color: white;
        text-decoration: none;
        padding: 10px 15px; 
        border-right: 1px solid #fff; 
    }

    nav a:last-child {
        border-right: none; 
    }

    .container {
        width: 400px;
        padding: 30px;
        border: 2px solid #ccc;
        border-radius: 10px; 
        margin: auto;
        background-color: beige; 
        overflow: auto;
    }

    .form-group,
    input[type="text"],
    input[type="date"],
    input[type="number"],
    select {
        box-sizing: border-box;
    }
    

    input[type="text"], input[type="date"], input[type="number"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .btn {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: green;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn:hover {
        background-color: darkgreen;
    }

    /* Ajout de styles pour la section de message */
    .message-section {
        margin-top: 20px;
        color: green; /* Couleur du message de succès */
    }
    </style>
</head>
<body>
    <header>
        <div>
            <a href="homelog.php"><img class="logo" src="BlaBlaBoat.png" alt="Logo"></a>
            <h1>Publiez votre Trajet !</h1>
        </div>
        <nav>
            <a href="homelog.php">Accueil</a>
            <a href="profil.php">Profil</a>
            <a href="bateau.php">Ajouter un bateau</a>
        </nav>
    </header>
    <div class="container">
        <h2>Publiez votre trajet</h2>
        <?php if (!empty($message)): ?>
            <div class="message-section">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="post" onsubmit="clearForm()">
                <div class="form-group">
                    <label for="departure">Départ</label>
                    <select id="departure" name="departure">
                        <?php
                        $resultat = $connexion->query("SELECT nom_port FROM port");
                        while ($row = $resultat->fetch_assoc()) {
                            echo "<option value='{$row['nom_port']}'>{$row['nom_port']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="arrival">Arrivée</label>
                    <select id="arrival" name="arrival">
                        <?php
                        $resultat = $connexion->query("SELECT nom_port FROM port");
                        while ($row = $resultat->fetch_assoc()) {
                            echo "<option value='{$row['nom_port']}'>{$row['nom_port']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="departure-date">Date de départ</label>
                    <input type="date" id="departure-date" name="departure-date" required>
                </div>
                <div class="form-group">
                    <label for="available-seats">Places disponibles</label>
                    <input type="number" id="available-seats" name="available-seats" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="price">Prix par passager</label>
                    <input type="number" id="price" name="price" min="1" step="any" value="10.00" required>
                </div>

                <div class="form-group">
                    <label for="boat">Bateau</label>
                    <select id="boat" name="boat">
                        <?php
                        $resultat = $connexion->query("SELECT nom_bateau FROM bateau");
                        while ($row = $resultat->fetch_assoc()) {
                            echo "<option value='{$row['nom_bateau']}'>{$row['nom_bateau']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="boat">---------------------------------------------------------------------------</label>
                    <input type="submit" value="PUBLIER" class="btn">
                </div>
        </form>
    </div>
</body>
</html>