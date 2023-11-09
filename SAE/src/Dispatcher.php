<?php
// dispatcher.php

require 'PHP/Autoloader.php';

Autoloader::register();

// Démarrez la session
session_start();

// Configurez votre connexion à la base de données
$bdd = new PDO('mysql:host=localhost; dbname=touiteur; charset=utf8', 'root', '');

// Créez des instances de vos classes
$touite = new Touite($bdd);
$signup = new Signup($bdd);
$connexion = new Connexion($bdd);

// Vérifiez quelle action doit être effectuée (par exemple, en fonction de l'URL ou des paramètres GET/POST)
$action = $_REQUEST['action'] ?? 'default'; // Vous devez définir une valeur par défaut, par exemple 'default' ici

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
        $idutilisateur = $_GET['id_utilisateur'] ?? null;
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
                if($touite){
                    header('Location: dispatcher.php');
                    exit;
                }else{
                  $erreur = "Problème dans la publication du touite";
                }
            }
        }else{
            header('Location: HTML/login.html');
        }
        break;

    case 'evaluerTouite':
        if (isset($_SESSION['user_id'])) {
            $idtouite = $_GET['idTouite'] ?? null;
            $like = ($_GET['like']);
            if ($idtouite !== null) {
                $touite->evaluerTouite($idtouite, $like);
                if($touite){
                    header('Location: dispatcher.php');
                    exit;
                }else{
                    $erreur = "Problème dans l'évaluation du touite";
                }
            }
        }else{
            header('Location: HTML/login.html');
        }
        break;

    case 'effacerTouite':
        if (isset($_SESSION['user_id'])) {
            $idtouite = $_POST['idtouite'] ?? null;
            if ($idtouite !== null) {
                $touite->effacerTouite($idtouite);
                if($touite){
                    header('Location: dispatcher.php');
                    exit;
                }else{
                    $erreur = "La suppression n'a pas fonctionnée";
                }
            }
        }
        break;

    case 'signup':
        $nom = $_POST['nom'] ?? '';
        $prenom = $_POST['prenom'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($password)) {
            if($signup->signup($nom, $prenom, $email, $password)){
                header('Location: index.php');
                exit();
            }else {
                // Affichez un message d'erreur en cas d'échec de connexion
                $erreur = "Problèmes coté serveur.";
            }
        }else{
            $erreur = "Erreur dans vos données, veuillez vérifier.";
        }
        break;

    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
            if ($connexion->login($email, $password)) {
                // Redirection après une connexion réussie
                header('Location: index.php');
                exit();
            } else {
                // Affichez un message d'erreur en cas d'échec de connexion
                $erreur = "Identifiants incorrects.";
            }
        break;


    case 'AfficherSonProfil':
        $AfSProfil = new AfficherSonProfil();
        $AfSProfil->execute();


    case 'deconnexion':
        session_unset();
        header('Location: dispatcher.php');
        break;

    case 'poster':
    echo<<<HTML
            <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur - Accueil</title>
            <link rel="stylesheet" href="CSS/poster.css">
            <link rel="icon" type="image/jpeg" href="images/icon.png">
        </head>
    
        <body>
    
    
        <div class="container">
            <div class="header-containerA">
                <form action="dispatcher.php?action=poster" method="post">
                    <button type="submit" class="btn-poster">Poster</button>
                </form>
            </div>
            <header>
                <img src="images/logo.jpeg" alt="Logo Touiteur" class="logo">
    
            </header>
    
            <main class="content">
    
                <div class="tweet-form-container">
                    <form class="tweet-form" action="dispatcher.php?action=publierTouite" method="post">
                        <label for="tweetContent">@{$_SESSION['nom']} {$_SESSION['prenom']}</label>
                        <textarea id="texte" name="texte" placeholder="Veuillez écrire ici." required></textarea>
                        <input type="hidden" name="action" value="publierTouite">
                        <button type="submit" name="submit">Touiter</button>
                    </form>
                </div>
            </main>
            <aside class="sidebar">
                <nav>
    
                    <ul class="menu">
                        <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                        <li><a href="tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                        <li><a href="ACONFIGURER"><img src="images/icon_profil.png" alt="" class="menu-icon">Profil</a></li>
    
                    </ul>
                    <div class="profile-module">
                        <div class="profile-username">@{$_SESSION['nom']} {$_SESSION['prenom']}</div>
                    </div>
                    <div class="tendances-container">
                        <div class="tendance-title">Tendances France</div>
                        <a href="#tag1" class="tag">#Tag1</a>
                        <a href="#tag2" class="tag">#Tag2</a>
                        <a href="#tag3" class="tag">#Tag3</a>
                    </div>
                    <form action="HTML/login.html" method="post">
                        <button type="submit" class="btn-connexion">Se connecter</button>
                    </form>
                    <form action="HTML/signup.html" method="post">
                        <button type="submit" class="btn-inscription">S'inscrire</button>
                    </form>
    
                </nav>
    
            </aside>
            <main class="content">
            </main>
    
    
        </div>
        </body>
        </html>
    HTML;
    break;

    default:
        // Action par défaut

        $touite->afficherListeTouites();
        break;
}