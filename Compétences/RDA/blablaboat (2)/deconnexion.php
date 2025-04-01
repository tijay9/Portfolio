<?php
session_start(); // Démarre la session

// Détruit toutes les variables de session
$_SESSION = array();

// Détruit la session elle-même
session_destroy();

// Redirige l'utilisateur vers la page d'accueil
header('Location: login.html');
exit();
?>
