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

// ...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecter les données du formulaire
    $nom_bateau = mysqli_real_escape_string($connexion, $_POST["nbBateaux"]);
    $immatriculation = isset($_POST["Immatriculation"]) ? mysqli_real_escape_string($connexion, $_POST["Immatriculation"]) : '';
    $description = mysqli_real_escape_string($connexion, $_POST["description"]);
    $nbrplace = mysqli_real_escape_string($connexion, $_POST["nbrplace"]);
    $vitesse = mysqli_real_escape_string($connexion, $_POST["vitesse"]);

    // Récupérer l'ID de l'utilisateur à partir de la session
    $user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

    // Requête SQL pour insérer les données dans la table bateau avec l'ID de l'utilisateur
    $insert_query = "INSERT INTO bateau (nom_bateau, immatriculation, description, nbr_place, vitesse, id_proprietaire) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $connexion->prepare($insert_query);

    if ($stmt === false) {
        die("Error preparing statement: " . $connexion->error);
    }

    $stmt->bind_param("sssssi", $nom_bateau, $immatriculation, $description, $nbrplace, $vitesse, $user_id);

    if ($stmt->execute()) {
        $message = "Le bateau '$nom_bateau' a été ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout du bateau : " . $stmt->error;
    }

    $stmt->close(); // Fermer la déclaration

}

// ...

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Informations de l'Utilisateur</title>
    <style>
        /* Styles pour le Header */
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

        /* Styles pour le Body */
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

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #2980b9;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1c87c9;
        }
    </style>
</head>
<body>
<header>
    <div>
        <a href="homelog.php"><img class="logo" src="BlaBlaBoat.png" alt="Logo"></a>
        <h1>Ajouter un nouveau bateau</h1>
    </div>
    <nav>
        <a href="homelog.php">Accueil</a>
        <a href="profil.php">Profil</a>
        <a href="reservation.php">Réservez votre voyage</a>
        <?php
        if (isset($_SESSION['login'])) {
            echo '<a href="deconnexion.php">Déconnexion</a>';
        }
        ?>
    </nav>
</header>


    <form id="user-info-form" method="POST" action="">
        <!-- Use the correct input names -->
        <label for="nbBateaux">Nom de bateau :</label>
        <input type="text" id="nbBateaux" name="nbBateaux" required>

        <label for="Immatriculation">Immatriculation du bateau :</label>
        <input type="text" id="Immatriculation" name="Immatriculation" required>

        <label for="description">Description du bateau :</label>
        <input type="text" id="description" name="description" required>

        <label for="nbrplace">Nombre de places :</label>
        <input type="text" id="nbrplace" name="nbrplace" required>

        <label for="vitesse">Vitesse de croisière :</label>
        <input type="text" id="vitesse" name="vitesse" required>


        <button type="submit">Mettre à jour</button>
        <p id="update-message"><?php echo isset($message) ? $message : ''; ?></p>
    </form>
</body>
</html>
