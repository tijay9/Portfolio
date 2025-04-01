<?php
session_start();

if (isset($_SESSION['login'])) {
    echo '<p style="color: green;">Inscription réussie pour ' . $_SESSION['login'] . '!</p>';
} else {
    echo '<p style="color: red;">Erreur lors de l\'inscription.</p>';
}
?>
