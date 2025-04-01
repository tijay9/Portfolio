<?php

// Démarrez la session
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['login'])) {
    header('Location: login.html');
    exit();
}


// Connexion à la base de données avec l'UTF-8

$host = "localhost";

$utilisateur = "wcueymat_user";

$motDePasse = "user1234HTTP";

$baseDeDonnées = "wcueymat_blablaboat_1";



$connexion = new mysqli($host, $utilisateur, $motDePasse, $baseDeDonnées);



// Vérifier la connexion

if ($connexion->connect_error) {

    die("Échec de la connexion à la base de données : " . $connexion->connect_error);

}



// Définir le jeu de caractères de la connexion à UTF-8

$connexion->set_charset("utf8");



// Récupérer la liste des ports depuis la base de données

$sql = "SELECT nom_port, co_gps FROM port";

$resultat = $connexion->query($sql);





// Initialiser la variable pour vérifier si le formulaire a été soumis

$formulaireSoumis = false;



// Récupérer les trajets depuis la base de données (si le formulaire a été soumis)

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $formulaireSoumis = true;



    // Récupérer les valeurs du formulaire

    $departure = $_POST['departure'];

    $arrival = $_POST['arrival'];

    $departure_date = $_POST['departure_date'];

    $return_date = $_POST['return_date'];

    $price_passengers = $_POST['price_passengers'];



    // Utiliser ces valeurs pour filtrer la requête SQL des trajets

    $sql_trajet = "SELECT id_trajet, port_depart, port_arrivee, date_depart, prix_par_passagers FROM trajet WHERE port_depart = '$departure' AND port_arrivee = '$arrival' AND prix_par_passagers <= '$price_passengers' AND date_depart BETWEEN '$departure_date' AND '$return_date'";

    $resultat_trajet = $connexion->query($sql_trajet);





    if (!$resultat_trajet) {

        // Afficher un message d'erreur en cas d'échec de la requête

        echo "Erreur dans la requête : " . $connexion->error;

    }

}



?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Achetez votre billet</title>

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
        
        
        /* Styles pour le menu déroulant de tri */
        #sort {
            margin-top: 10px; /* Ajouter un espace en haut */
            padding: 8px; /* Ajouter du padding */
            max-width: 250px;
            font-size: 18px; /* Ajuster la taille de la police */
            border: 1px solid #3498db; /* Ajouter une bordure */
            border-radius: 3px; /* Arrondir les coins */
            background-color: #fff; /* Couleur de fond */
            color: #3498db; /* Couleur du texte */
        }
        
        #sort:hover,
        #sort:focus {
            outline: none; /* Supprimer la bordure de focus par défaut */
            border-color: #2980b9; /* Changer la couleur de la bordure au survol et au focus */
        }

    </style>
    
    
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var table = document.querySelector(".tab table");
        if (table) {
            var rows = table.rows;

            document.getElementById("sort").addEventListener("change", function() {
                var selectedSort = this.value;
                var columnIndex;

                switch (selectedSort) {
                    case "date":
                        columnIndex = 2; // Index de la colonne "Date de départ"
                        break;
                    case "prix":
                        columnIndex = 3; // Index de la colonne "Prix par passagers"
                        break;
                    default:
                        return; // Sortie si aucune option valide n'est sélectionnée
                }

                var sortedRows = Array.from(rows).slice(1); // Exclut la première ligne d'en-tête
                sortedRows.sort(function(a, b) {
                    var aValue = a.cells[columnIndex].textContent.trim();
                    var bValue = b.cells[columnIndex].textContent.trim();

                    return selectedSort === "date" ? new Date(aValue) - new Date(bValue) : aValue - bValue;
                });

                // Supprime toutes les lignes existantes
                while (table.rows.length > 1) {
                    table.deleteRow(1);
                }

                // Ajoute les lignes triées
                sortedRows.forEach(function(row) {
                    table.appendChild(row);
                });
            });
        }
    });
</script>


<script>
    function reserverTrajet(idTrajet, portDepart, portArrivee, dateDepart, prix, duree, distance) {
        // Récupérer l'ID du client (id_client) depuis la session PHP
        var idClient = <?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 'null'; ?>;
        
        if (idClient === null) {
            alert('Veuillez vous connecter pour effectuer une réservation.');
            return;
        }

        // Envoyer les données au serveur avec AJAX
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "ajouter_reservation.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Afficher une alerte ou effectuer d'autres actions en fonction de la réponse du serveur
                alert(xhr.responseText);
            }
        };
        
        var params = "id_client=" + idClient +
                      "&port_depart=" + encodeURIComponent(portDepart) +
                      "&port_arrivee=" + encodeURIComponent(portArrivee) +
                      "&date_depart=" + encodeURIComponent(dateDepart) +
                      "&prix_par_passagers=" + prix +
                      "&duree_du_trajet=" + duree +
                      "&distance=" + distance;
        
        xhr.send(params);
    }
</script>



</head>

<body>

<header>

    <div>

        <a href="homelog.php"><img class="logo" src="BlaBlaBoat.png" alt="Logo"></a>

        <h1>BlaBlaBoat, le Co-baturage pour tous!</h1>

    </div>

    <nav>

        <a href="homelog.php">Acceuil</a>

        <a href="profil.php">Profil</a>

        <a href="bateau.php">Ajouter un bateau</a>

        <?php

        if (isset($_SESSION['login'])) {

            echo '<a href="deconnexion.php">Déconnexion</a>';

        }

        ?>

    </nav>

</header>





<div class="container">

    <h2>Achetez votre billet</h2>

    <p>Jusqu'à 15 min avant l'horaire de départ !</p>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">

        <div class="form-group">

            

            <select id="departure" name="departure">

                <?php

                $resultat->data_seek(0);

                while ($row = $resultat->fetch_assoc()) {

                    $nom_port = htmlspecialchars($row['nom_port'], ENT_QUOTES, 'UTF-8');

                    echo "<option value='$nom_port'>$nom_port</option>";

                }

                ?>

            </select>



        </div>

        <div class="form-group">

            

            <label for="arrival">Arrivée</label>

            <!-- Remplacez le champ texte par une liste déroulante -->

            <select id="arrival" name="arrival">

                <?php

                $resultat->data_seek(0);

                while ($row = $resultat->fetch_assoc()) {

                    $nom_port = htmlspecialchars($row['nom_port'], ENT_QUOTES, 'UTF-8');

                    echo "<option value='$nom_port'>$nom_port</option>";

                }

                ?>

            </select>



        </div>

        <div class="form-group">

            <label for="departure_date">Date de départ:</label>

            <input type="date" id="departure_date" name="departure_date">

        </div>

        <div class="form-group">

            <label for="return_date">Date de retour:</label>

            <input type="date" id="return_date" name="return_date">

        </div>

        <div class="form-group">

            <label for="passengers">Prix max par passagers($):</label>

            <input type="number" id="price_passengers" name="price_passengers" min="1" value="1">

        </div>

        <div class="form-group">

            <input type="submit" value="RECHERCHER" class="btn">

        </div>

    </form>

</div>

<select id="sort">
    <option value="date">Trier par date</option>
    <option value="prix">Trier par prix</option>
</select>

<div class="tab">

    <?php if ($formulaireSoumis && $resultat_trajet->num_rows > 0): ?>

        <h2>Trajets disponibles</h2>

        <table>

            <tr>

                <th>Port de départ</th>

                <th>Port d'arrivée</th>

                <th>Date de départ</th>

                <th>Prix par passagers</th>

                <th>Durée du trajet</th>

                <th>Distance</th>

            </tr>

            <?php

            

            // Fonction pour calculer la distance haversine entre deux points géographiques

            function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2) {

                $earthRadius = 6371; // Rayon de la Terre en kilomètres

            

                $lat1 = deg2rad($lat1);

                $lon1 = deg2rad($lon1);

                $lat2 = deg2rad($lat2);

                $lon2 = deg2rad($lon2);

            

                $dlat = $lat2 - $lat1;

                $dlon = $lon2 - $lon1;

            

                $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;

                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            

                $distance = $earthRadius * $c; // Distance en kilomètres

            

                return $distance;

            }

            

            function getCoordinatesByPortName($portName, $connexion) {

            $portName = $connexion->real_escape_string($portName); // Pour éviter les injections SQL

        

            $sql = "SELECT co_gps FROM port WHERE nom_port = '$portName'";

            $result = $connexion->query($sql);

        

            if ($result && $result->num_rows > 0) {

                $row = $result->fetch_assoc();

                return $row['co_gps'];

            } else {

                return null; // Retourne null si le port n'est pas trouvé

            }

        }



            // Calculer la durée en fonction de la vitesse et de la distance

            function calculateTravelTime($distance, $averageSpeed = 30) {

                // Vitesse moyenne en km/h (modifiable selon vos besoins)

                if ($averageSpeed <= 0) {

                    return "Vitesse non valide";

                }

            

                $timeInHours = $distance / $averageSpeed;

                $timeInMinutes = $timeInHours * 60;

            

                return round($timeInMinutes);

            }





            // Exemple de boucle pour afficher les données de la base de données dans un tableau

            

            while ($row_trajet = $resultat_trajet->fetch_assoc()) {

                echo "<tr>";

                echo "<td>" . $row_trajet['port_depart'] . "</td>";

                echo "<td>" . $row_trajet['port_arrivee'] . "</td>";

                echo "<td>" . $row_trajet['date_depart'] . "</td>";

                echo "<td>" . $row_trajet['prix_par_passagers'] . "$</td>";



               // Utiliser la fonction pour obtenir les coordonnées

                $departure_coordinates = getCoordinatesByPortName($row_trajet['port_depart'], $connexion);

                $arrival_coordinates = getCoordinatesByPortName($row_trajet['port_arrivee'], $connexion);



                if ($departure_coordinates && $arrival_coordinates) {

                    list($departure_lat, $departure_lon) = explode(', ', $departure_coordinates);

                    list($arrival_lat, $arrival_lon) = explode(', ', $arrival_coordinates);



                    // Calcul de la distance

                    $distance = calculateHaversineDistance(

                        $departure_lat, $departure_lon,

                        $arrival_lat, $arrival_lon

                    );

                    // Calcul de la durée estimée

                    $travelTime = calculateTravelTime($distance);

                    

                    echo "<td>" . $travelTime . " min</td>";

                    echo "<td>" . $distance . " km</td>";
                    
                    // Ajoutez la colonne "Réserver" avec le bouton
                    echo "<td><button onclick=\"reserverTrajet(" . $row_trajet['id_trajet'] . ", '" . $row_trajet['port_depart'] . "', '" . $row_trajet['port_arrivee'] . "', '" . $row_trajet['date_depart'] . "', " . $row_trajet['prix_par_passagers'] . ", " . $travelTime . ", " . $distance . ")\">Réserver</button></td>";

                } else {

                    echo "<td>Coordonnées indisponibles</td>";

                    echo "<td>N/A</td>";

                }


                echo "</tr>";

            }
            
            

            ?>

        </table>

    <?php elseif ($formulaireSoumis): ?>

        <h2>Aucun trajet disponible pour vos préférences.</h2>

    <?php endif; ?>
    

</div>



</body>

</html>

