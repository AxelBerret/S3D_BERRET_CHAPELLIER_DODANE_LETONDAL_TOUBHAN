<?php
// dispatcher.php

require 'PHP/Autoloader.php';

Autoloader::register();

// Démarrez la session
session_start();

// Configurez votre connexion à la base de données
$bdd = new PDO('mysql:host=localhost; dbname=phpbd; charset=utf8', 'root', '');

// Créez des instances de vos classes
$touite = new Touite($bdd);
$signup = new Signup($bdd);
$connexion = new Connexion($bdd);

// Vérifiez quelle action doit être effectuée (par exemple, en fonction de l'URL ou des paramètres GET/POST)
$action = $_GET['action'] ?? 'default'; // Vous devez définir une valeur par défaut, par exemple 'default' ici

// En fonction de l'action, appelez la méthode appropriée de vos classes
switch ($action) {
    case 'afficherListeTouites':
        $touite->afficherListeTouites();
        break;

    case 'afficherTouiteDetail':
        $idtouite = $_GET['idtouite'] ?? null;
        if ($idtouite !== null) {
            $touite->afficherTouiteDetail($idtouite);
        }
        break;

    case 'afficherTouitesUtilisateur':
        $idutilisateur = $_GET['idutilisateur'] ?? null;
        if ($idutilisateur !== null) {
            $touite->afficherTouitesUtilisateur($idutilisateur);
        }
        break;

    case 'afficherTouitesTag':
        $tag = $_GET['tag'] ?? null;
        if ($tag !== null) {
            $touite->afficherTouitesTag($tag);
        }
        break;

    case 'publierTouite':
        if (isset($_SESSION['user_id'])) {
            $idutilisateur = $_SESSION['user_id'];
            $texte = $_POST['texte'] ?? '';
            if (!empty($texte)) {
                $touite->publierTouite($idutilisateur, $texte);
            }
        }
        break;

    case 'evaluerTouite':
        if (isset($_SESSION['user_id'])) {
            $idtouite = $_GET['idtouite'] ?? null;
            $eval = ($_GET['eval'] ?? '') === 'like' ? true : false;
            if ($idtouite !== null) {
                $touite->evaluerTouite($idtouite, $eval);
            }
        }
        break;

    case 'effacerTouite':
        if (isset($_SESSION['user_id'])) {
            $idtouite = $_GET['idtouite'] ?? null;
            if ($idtouite !== null) {
                $touite->effacerTouite($idtouite);
            }
        }
        break;

    case 'signup':
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($password)) {
            $signup->signup($nom, $prenom, $email, $password);
        }
        break;

    case 'connexion':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!empty($email) && !empty($password)) {
            $connexion->login($email, $password);
        }
        break;

    case 'deconnexion':
        session_unset();
        break;

    default:
        // Action par défaut
        $touite->afficherListeTouites();
        break;
}