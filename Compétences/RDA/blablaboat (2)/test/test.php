<?php
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

// Récupérer la liste des ports depuis la base de données
$sql = "SELECT nom_port, co_gps FROM port";
$resultat = $connexion->query($sql);

// Fermer la connexion
$connexion->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calcul de distance entre deux ports</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .distance-calculator {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 5px;
      padding: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    form {
      display: flex;
      flex-direction: column;
      max-width: 300px;
    }

    label {
      margin-bottom: 5px;
    }

    button {
      padding: 10px;
      cursor: pointer;
    }

    #distance-result {
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div class="distance-calculator">
    <form id="distance-form">
      <label for="port1">Choisir le premier port:</label>
      <select id="port1">
        <?php
          // Afficher les options basées sur les données de la base de données
          while ($row = $resultat->fetch_assoc()) {
              echo "<option value='{$row['co_gps']}'>{$row['nom_port']}</option>";
          }
        ?>
      </select>
      
      <label for="port2">Choisir le deuxième port:</label>
      <select id="port2">
        <?php
          // Réinitialiser le pointeur de résultat pour réutilisation
          $resultat->data_seek(0);

          // Afficher les options basées sur les données de la base de données
          while ($row = $resultat->fetch_assoc()) {
              echo "<option value='{$row['co_gps']}'>{$row['nom_port']}</option>";
          }
        ?>
      </select>

      <button type="button" onclick="calculateDistance()">Calculer la distance</button>
    </form>

    <div id="distance-result"></div>
  </div>

  <script>
    function calculateDistance() {
      const port1Input = document.getElementById('port1');
      const port2Input = document.getElementById('port2');
      const resultDiv = document.getElementById('distance-result');

      const coordinates1 = port1Input.value.split(',').map(coord => parseFloat(coord));
      const coordinates2 = port2Input.value.split(',').map(coord => parseFloat(coord));

      if (coordinates1.length !== 2 || coordinates2.length !== 2 || isNaN(coordinates1[0]) || isNaN(coordinates1[1]) || isNaN(coordinates2[0]) || isNaN(coordinates2[1])) {
        resultDiv.innerHTML = 'Veuillez saisir des coordonnées GPS valides.';
        return;
      }

      const distance = haversineDistance(coordinates1[0], coordinates1[1], coordinates2[0], coordinates2[1]);

      resultDiv.innerHTML = `La distance entre les deux ports est d'environ ${distance.toFixed(2)} kilomètres.`;
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
      const R = 6371; // Rayon de la Terre en kilomètres
      const dLat = (lat2 - lat1) * Math.PI / 180;
      const dLon = (lon2 - lon1) * Math.PI / 180;
      const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      const distance = R * c;

      return distance;
    }
  </script>

</body>
</html>
