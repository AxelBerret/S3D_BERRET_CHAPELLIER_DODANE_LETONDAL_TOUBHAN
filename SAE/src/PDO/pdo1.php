<?php
// Connexion à la base de données
try {
    $bdd = new PDO('mysql:host=localhost;dbname=SAE_touiteur;charset=utf8', 'root', '');
} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
