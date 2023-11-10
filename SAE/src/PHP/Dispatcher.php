<?php
// dispatcher.php

require 'Autoloader.php';

Autoloader::register();

session_start();

$bdd = new PDO('mysql:host=localhost; dbname=touiteur; charset=utf8', 'root', '');

// Instances de nos classes
$afficherListeTouites = new afficherListeTouite($bdd);
$afficherTouiteDetail = new afficherTouiteDetail($bdd);
$afficherTouitesUtilisateur = new afficherTouitesUtilisateur($bdd);
$afficherTouitesTag = new afficherTouitesTag($bdd);
$afficherMurUtilisateur = new afficherMurUtilisateur($bdd);
$administrateur = new Administrateur($bdd);
$publierTouite = new publierTouite($bdd);
$evaluerTouite = new evaluerTouite($bdd);
$effacerTouite = new effacerTouite($bdd);
$suivreUtilisateur = new suivreUtilisateur($bdd);
$suivreTag = new suivreTag($bdd);
$signup = new Signup($bdd);
$connexion = new Connexion($bdd);

// On récupère l'action nécessaire
$action = $_REQUEST['action'] ?? 'default'; // Vous devez définir une valeur par défaut, par exemple 'default' ici

// Switch de l'action
switch ($action) {

    case 'afficherTouiteDetail':
        $idtouite = $_GET['idtouite'] ?? null;
        if ($idtouite !== null) {
            $afficherTouiteDetail->afficherTouiteDetail($idtouite);
        }
        break;

    case 'afficherTouitesUtilisateur':
        $idutilisateur = $_GET['id_utilisateur'] ?? null;
        if ($idutilisateur !== null) {
            $afficherTouitesUtilisateur->afficherTouitesUtilisateur($idutilisateur);
        }
        break;

    case 'afficherTouitesTag':
        $tag = $_GET['tag'] ?? null;
        if ($tag !== null) {
            $afficherTouitesTag->afficherTouitesTag($tag);
        }
        break;

    case 'afficherInfluenceurs':
        $administrateur->afficherInfluenceurs();
        break;

    case 'afficherTagsTendances':
        $administrateur->afficherTagsTendances();
        break;

    case 'publierTouite':
        if (isset($_SESSION['user_id'])) {

            $idutilisateur = $_SESSION['user_id'];
            $texte = $_POST['texte'] ?? '';
            $image = $_FILES ?? null;
            if (!empty($texte)) {
                $publierTouite->publierTouite($idutilisateur, $texte, $image);
                if($publierTouite){
                    header('Location: dispatcher.php');
                    exit;
                }else{
                  $erreur = "Problème dans la publication du touite";
                }
            }
        }else{
            header('Location: ../HTML/login.html');
        }
        break;

    case 'evaluerTouite':
        if (isset($_SESSION['user_id'])) {
            $idtouite = $_GET['idTouite'] ?? null;
            $like = ($_GET['like']);
            if ($idtouite !== null) {
                $evaluerTouite->evaluerTouite($idtouite, $like);
                if($evaluerTouite){
                    header("Refresh:0");
                    exit;
                }else{
                    //Déjà évalué
                    header("Refresh:0");
                }
            }
        }else{
            header('Location: ../HTML/login.html');
        }
        break;

    case 'effacerTouite':
        if (isset($_SESSION['user_id'])) {
            $idtouite = $_POST['idtouite'] ?? null;
            if ($idtouite !== null) {
                $effacerTouite->effacerTouite($idtouite);
                    header("Refresh:0");
                    exit;
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
                header('Location: ../index.php');
                exit();
            }else {
                // Afficher un message d'erreur en cas d'échec de connexion
                header("Refresh:0");
            }
        }else{
            header("Refresh:0");
        }
        break;

    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
            if ($connexion->login($email, $password)) {
                // Redirection après une connexion réussie
                header('Location: dispatcher.php');
                exit();
            } else {
                header("Refresh:0");
                exit();
            }
        break;

    case 'afficherSonProfil':
        $AfSProfil = new AfficherSonProfil();
        $AfSProfil->execute();

    case 'suivreUtilisateur':
        if (isset($_SESSION['user_id'])) {
            $idutil = $_POST['id_utilisateur'] ?? null;
            if ($idutil !== null) {
                $idutil = $suivreUtilisateur->suivreUtilisateur($idutil);
                if($idutil){
                    header("Refresh:0");
                    exit;
                }else{
                    $erreur = "Le suivi n'a pas fonctionné";
                }
            }
        }
        break;

    case 'nePlusSuivreUtilisateur':
        if (isset($_SESSION['user_id'])) {
            $idutil = $_POST['id_utilisateur'] ?? null;
            if ($idutil !== null) {
                $idutil = $suivreUtilisateur->nePlusSuivreUtilisateur($idutil);
                if($idutil){
                    header("Refresh:0");
                    exit;
                }else{
                    $erreur = "L'unfollow n'a pas fonctionné";
                }
            }
        }
        break;


    case 'deconnexion':
        session_unset();
        header('Location: dispatcher.php');
        break;

    case 'poster':
    echo <<<HTML
            <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur - Accueil</title>
            <link rel="stylesheet" href="../CSS/poster.css">
            <link rel="icon" type="image/jpeg" href="../images/icon.png">
        </head>
    
        <body>
    
    
        <div class="container">
            <div class="header-containerA">
                <form action="dispatcher.php?action=poster" method="post">
                    <button type="submit" class="btn-poster">Poster</button>
                </form>
            </div>
            <header>
                <img src="../images/logo.jpeg" alt="Logo Touiteur" class="logo">
    
            </header>
    
            <main class="content">
    
                <div class="tweet-form-container">
                    <form class="tweet-form" action="dispatcher.php?action=publierTouite" method="post" enctype="multipart/form-data">
                        <label for="tweetContent">@Déconnecté</label>
                        <textarea id="texte" name="texte" placeholder="Veuillez écrire ici." required></textarea>
                        <input type="file" name="image" id="image" accept="image/*">
                        <input type="hidden" name="action" value="publierTouite">
                        <button type="submit" name="submit">Touiter</button>
                    </form>
                </div>
            </main>
            <aside class="sidebar">
                <nav>
    
                    <ul class="menu">
                        <li><a href="dispatcher.php?action=afficherMonMur"><img src="../images/mur_accueil.png" alt="" class="menu-icon">Mon Mur</a></li>
                        <li><a href="dispatcher.php"><img src="../images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                        <li><a href="tendances.html"><img src="../images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                        <li><a href="dispatcher.php?action=afficherSonProfil"><img src="../images/icon_profil.png" alt="" class="menu-icon">Profil</a></li>
    
                    </ul>
                    <div class="profile-module">
                        <div class="profile-username">@Déconnecté</div>
                    </div>
                    <div class="tendances-container">
                        <div class="tendance-title">Tendances France</div>
                        <a href="#tag1" class="tag">#Tag1</a>
                        <a href="#tag2" class="tag">#Tag2</a>
                        <a href="#tag3" class="tag">#Tag3</a>
                    </div>
                <form action="../HTML/login.html" method="post">
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
                <form action="../HTML/signup.html" method="post">
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

    case 'suivreTag':
        if (isset($_SESSION['user_id'])) {
            $libelleTag = $_GET['libelleTag'] ?? null;
            if ($libelleTag !== null) {
                $suivreTag->suivreTag($libelleTag);
                $tagSansHashtag = substr($libelleTag, 1);
                header("Location: dispatcher.php?action=afficherTouitesTag&tag=$tagSansHashtag");
                exit;
            }
        } else {
            header('Location: ../HTML/login.html');
        }
        break;

    case 'nePlusSuivreTag':
        if (isset($_SESSION['user_id'])) {
            $libelleTag = $_GET['libelleTag'] ?? null;
            if ($libelleTag !== null) {
                $suivreTag->nePlusSuivreTag($libelleTag);
                $tagSansHashtag = substr($libelleTag, 1);
                header("Location: dispatcher.php?action=afficherTouitesTag&tag=$tagSansHashtag");
                exit;
            }
        } else {
            header('Location: ../HTML/login.html');
        }
        break;

    case 'afficherMonMur':
        if (isset($_SESSION['user_id'])) {
            $iduser = $_SESSION['user_id'] ?? null;
            if ($iduser !== null) {
                $afficherMurUtilisateur->afficherMurUtilisateur($iduser);
            }
        } else {
            header('Location: ../HTML/login.html');
        }
        break;

    case 'afficherInfluenceurs':
        $influenceurs = $connexion->afficherInfluenceurs();
        echo <<<HTML
            <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur - Influenceurs</title>
            <link rel="stylesheet" href="../CSS/influenceurs.css"> <!-- Assurez-vous d'avoir un fichier CSS approprié -->
            <link rel="icon" type="image/jpeg" href="../images/icon.png">
        </head>
        <body>
        <div class="container">
            <header>
                <img src="../images/logo.jpeg" alt="Logo Touiteur" class="logo">
            </header>
            <div class="main-content">
                <div class="influenceurs-container">
                    <h2>Les Influenceurs</h2>
                    <ul></ul>
                </div>
            </div>
        </div>
        </body>
        </html>
        HTML;

        foreach ($influenceurs as $influenceur) {
            echo '<li>' . $influenceur['nom'] . ' ' . $influenceur['prenom'] . ' - ' . $influenceur['nbSuiveurs'] . ' suiveurs</li>';
        }
        break;

    case 'utilisateurNarcissique':
        $un = new UtilisateurNarcissique();
        $un->execute();
        break;

    default:
            $afficherListeTouites->afficherListeTouites();
        break;
}