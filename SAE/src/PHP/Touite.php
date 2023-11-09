<?php

declare(strict_types=1);

class TouiteTropLong extends Exception {}

class Touite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function afficherListeTouites() {

        // Requête permettant de récupérer tous les touites et les trier par date récente
        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom, UTILISATEUR.id_utilisateur FROM TOUITE 
                 JOIN UTILISATEUR ON TOUITE.Id_utilisateur = UTILISATEUR.id_utilisateur 
                 ORDER BY TOUITE.datePub DESC";

        $result = $this->db->query($query);

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Touiteur - Accueil</title>
                <link rel="stylesheet" href="CSS/accueil.css">
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
                    <ul class="feed">
HTML;


        // On affiche ensuite tout les touites
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $texteCourt = substr($row['texte'], 0, 65);

            if (strlen($texteCourt) > 64) {
                $texteCourt = $texteCourt . '...';
            }

            $pattern = '/#(\w+)/';
            preg_match_all($pattern, $texteCourt, $matches);

            $tags = $matches[0];

            foreach ($tags as $tag) {

                $tagSansHash = substr($tag, 1);
                $texteCourt = str_replace($tag, "<a href='dispatcher.php?action=afficherTouitesTag&tag={$tagSansHash}'>$tag</a>", $texteCourt);
            }

            echo <<<HTML
    <li class="tweet">
        <div class="tweet-header">
            <div class="tweet-user-info">
                <a href='dispatcher.php?action=afficherTouitesUtilisateur&id_utilisateur={$row['id_utilisateur']}' style='text-decoration: none; color: white;'>
                <div class="tweet-username">@{$row['nom']} {$row['prenom']}</div>
                </a>
HTML;
            if (isset($_SESSION['user_id'])) {
                $id_utilisateur = $row['id_utilisateur'];
                $suiviButton = "<form action='dispatcher.php' method='post'>
                        <input type='hidden' name='action' value='suivreUtilisateur'>
                        <input type='hidden' name='id_utilisateur' value='$id_utilisateur'>
                        <button type='submit' class='btn-supprimer'>Suivre</button>
                    </form>";
                // Vérifie si l'utilisateur est déjà suivi
                if ($this->estDejaSuivi($id_utilisateur, $_SESSION['user_id'])) {
                    $suiviButton = "<button class='btn-supprimer' disabled>Suivi</button>";
                }

                echo $suiviButton;
             }else{
                 echo <<<HTML
                                        <form action="HTML/login.html" method="post">
                                            <button type="submit" class="btn-supprimer">Suivre</button>
                                        </form>
HTML;
             }
echo <<<HTML
            </div>
        </div>
        <hr class="tweet-divider">
        <div class="tweet-content">
                        <a href='dispatcher.php?action=afficherTouiteDetail&idtouite={$row['id_touite']}' style='text-decoration: none; color: white;'>
                        <p>$texteCourt</p>
                        </a><br>
                        <div class="like-dislike-buttons">
                            <div class="like-dislike-buttons">
                                <a href="dispatcher.php?action=evaluerTouite&idTouite={$row['id_touite']}&like=1">
                                    <img src="Images/like.png" alt="Like button" class="like-button">
                                </a>
                                <span class="like-counter">{$row['jaime']}</span>
                                
                                <a href="dispatcher.php?action=evaluerTouite&idTouite={$row['id_touite']}&like=0">
                                    <img src="Images/dislike.png" alt="Dislike button" class="dislike-button">
                                </a>
                                <span class="dislike-counter">{$row['dislike']}</span>
HTML;
                                if ($_SESSION['user_id'] == $row['id_utilisateur']) {
                                    echo <<<HTML
                                        <form action="dispatcher.php" method="post">
                                            <input type="hidden" name="action" value="effacerTouite">
                                            <input type="hidden" name="idtouite" value="{$row['id_touite']}">
                                            <button type="submit" class="btn-supprimer">Supprimer</button>
                                        </form>
HTML;
            }echo<<<HTML

                            </div>
                        </div>
                    </div>
    </li>
HTML;
        }
        if(isset($_SESSION['user_id'])){
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
        }
        else{
            $nom = "Déconnecté";
            $prenom = "";
        }
        echo <<<HTML
            </ul>
            </main>
            <aside class="sidebar">
            <nav>

                <ul class="menu">
HTML;
        if(isset($_SESSION['user_id'])){
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="HTML/tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                    <li><a href="dispatcher.php?action=afficherSonProfil"><img src="images/profil.png" alt="" class="menu-icon">Profil</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>

                <form action="Dispatcher.php?action=deconnexion" method="post">
                    <button type="submit" class="btn-connexion">Se déconnecter</button>
                </form>
HTML;
        }
        else{
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>
                
            <form action="HTML/login.html" method="post">
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
                <form action="HTML/signup.html" method="post">
                    <button type="submit" class="btn-inscription">S'inscrire</button>
                </form>
            </nav>
            </aside>
            </ul>
            </main>
            </div>
            </body>
            </html>
HTML;
        }
    }

    function afficherTouiteDetail(string $idtouite){

        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom, UTILISATEUR.id_utilisateur FROM TOUITE 
                  JOIN UTILISATEUR ON TOUITE.Id_utilisateur = UTILISATEUR.id_utilisateur 
                  WHERE TOUITE.id_touite = :touite_id";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':touite_id', $idtouite, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Touiteur - Accueil</title>
                <link rel="stylesheet" href="CSS/accueil.css">
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
                    <ul class="feed">
HTML;

        if ($row) {
            echo <<<HTML
            <li class="tweet">
                <div class="tweet-header">
                    <div class="tweet-user-info">
                        <a href='dispatcher.php?action=afficherTouitesUtilisateur&id_utilisateur={$row['id_utilisateur']}' style='text-decoration: none; color: white;'>
                        <div class="tweet-username">@{$row['nom']} {$row['prenom']}</div>
                        </a><br>
                    </div>
                </div>
                <hr class="tweet-divider">
                <div class="tweet-content">
                                <p>{$row['texte']}</p>
                                <br><br><br>
                                <p>{$row['datePub']}</p>
                            </div>
            </li>
HTML;
        }
        if (isset($_SESSION['user_id'])) {
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
        } else {
            $nom = "Déconnecté";
            $prenom = "";
        }
        echo <<<HTML
            </ul>
            </main>
            <aside class="sidebar">
            <nav>

                <ul class="menu">
HTML;
        if (isset($_SESSION['user_id'])) {
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="HTML/tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                    <li><a href="dispatcher.php?action=afficherSonProfil"><img src="images/profil.png" alt="" class="menu-icon">Profil</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>

                <form action="Dispatcher.php?action=deconnexion" method="post">
                    <button type="submit" class="btn-connexion">Se déconnecter</button>
                </form>
HTML;
        } else {
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>
                
            <form action="HTML/login.html" method="post">
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
                <form action="HTML/signup.html" method="post">
                    <button type="submit" class="btn-inscription">S'inscrire</button>
                </form>
            </nav>
            </aside>
            </ul>
            </main>
            </div>
            </body>
            </html>
HTML;
        }
    }

    function afficherTouitesUtilisateur(string $idutilisateur) {

        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom, UTILISATEUR.id_utilisateur FROM TOUITE 
                  JOIN UTILISATEUR ON TOUITE.Id_utilisateur = UTILISATEUR.id_utilisateur 
                  WHERE TOUITE.id_utilisateur = :utilisateur_id 
                  ORDER BY TOUITE.datePub DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $idutilisateur, PDO::PARAM_STR);
        $stmt->execute();

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur - Accueil</title>
            <link rel="stylesheet" href="CSS/accueil.css">
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
                <ul class="feed">
    HTML;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $texteCourt = substr($row['texte'], 0, 65);

            if (strlen($texteCourt) > 64) {
                $texteCourt = $texteCourt . '...';
            }

            echo <<<HTML
    <li class="tweet">
        <div class="tweet-header">
            <div class="tweet-user-info">
                <a href='dispatcher.php?action=afficherTouitesUtilisateur&id_utilisateur={$row['id_utilisateur']}' style='text-decoration: none; color: white;'>
                <div class="tweet-username">@{$row['nom']} {$row['prenom']}</div>
                </a><br>
            </div>
        </div>
        <hr class="tweet-divider">
        <div class="tweet-content">
                        <a href='dispatcher.php?action=afficherTouiteDetail&idtouite={$row['id_touite']}' style='text-decoration: none; color: white;'>
                        <p>$texteCourt</p>
                        </a><br>
                    </div>
    </li>
    HTML;
        }

        if(isset($_SESSION['user_id'])){
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
        }
        else{
            $nom = "Déconnecté";
            $prenom = "";
        }

        echo <<<HTML
            </ul>
            </main>
            <aside class="sidebar">
            <nav>
                <ul class="menu">
    HTML;

        if(isset($_SESSION['user_id'])){
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="HTML/tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                    <li><a href="dispatcher.php?action=afficherMurUtilisateur&idtouite={$_SESSION['user_id']}"><img src="images/profil.png" alt="" class="menu-icon">Profil</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>

                <form action="Dispatcher.php?action=deconnexion" method="post">
                    <button type="submit" class="btn-connexion">Se déconnecter</button>
                </form>
    HTML;
        }
        else{
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>
                
            <form action="HTML/login.html" method="post">
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
                <form action="HTML/signup.html" method="post">
                    <button type="submit" class="btn-inscription">S'inscrire</button>
                </form>
            </nav>
            </aside>
            </ul>
            </main>
            </div>
            </body>
            </html>
    HTML;
        }
    }

    function afficherTouitesTag(string $tag){

        $tag = '#'.$tag;
        //On cherche dans tous les touites le tag voulu
        $query = "SELECT TOUITE.*, TAG.libelletag, UTILISATEUR.* FROM TOUITE
                  INNER JOIN TAG ON TOUITE.id_touite = TAG.id_touite
                  INNER JOIN UTILISATEUR ON TOUITE.id_utilisateur = UTILISATEUR.id_utilisateur
                  WHERE TAG.libelletag = :tag;";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Touiteur - Accueil</title>
                <link rel="stylesheet" href="CSS/accueil.css">
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
                    <ul class="feed">
HTML;

        foreach ($result as $row) {
            $texteCourt = substr($row['texte'], 0, 65);

            if (strlen($texteCourt) > 64) {
                $texteCourt = $texteCourt . '...';
            }

            echo <<<HTML
    <li class="tweet">
        <div class="tweet-header">
            <div class="tweet-user-info">
                <a href='dispatcher.php?action=afficherTouitesUtilisateur&id_utilisateur={$row['id_utilisateur']}' style='text-decoration: none; color: white;'>
                <div class="tweet-username">@{$row['nom']} {$row['prenom']}</div>
                </a><br>
            </div>
        </div>
        <hr class="tweet-divider">
        <div class="tweet-content">
                        <a href='dispatcher.php?action=afficherTouiteDetail&idtouite={$row['id_touite']}' style='text-decoration: none; color: white;'>
                        <p>$texteCourt</p>
                        </a><br>
                    </div>
    </li>
HTML;
        }
        if (isset($_SESSION['user_id'])) {
            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
        } else {
            $nom = "Déconnecté";
            $prenom = "";
        }
        echo <<<HTML
            </ul>
            </main>
            <aside class="sidebar">
            <nav>

                <ul class="menu">
HTML;
        if (isset($_SESSION['user_id'])) {
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="HTML/tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                    <li><a href="dispatcher.php?action=AfficherSonProfil"><img src="images/profil.png" alt="" class="menu-icon">Profil</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>

                <form action="Dispatcher.php?action=deconnexion" method="post">
                    <button type="submit" class="btn-connexion">Se déconnecter</button>
                </form>
HTML;
        } else {
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="tendances.html"><img src="images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="#tag1" class="tag">#Tag1</a>
                    <a href="#tag2" class="tag">#Tag2</a>
                    <a href="#tag3" class="tag">#Tag3</a>
                </div>
                
                <div class="recherche-tag">
                <form action="dispatcher.php" method="get">
                    <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                    <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                    <button type="submit" class="tag-search-button">Rechercher</button>
                </form>
                </div>
                
            <form action="HTML/login.html" method="post">
                    <button type="submit" class="btn-connexion">Se connecter</button>
                </form>
                <form action="HTML/signup.html" method="post">
                    <button type="submit" class="btn-inscription">S'inscrire</button>
                </form>
            </nav>
            </aside>
            </ul>
            </main>
            </div>
            </body>
            </html>
HTML;

        }
    }

    //DANS LE MAIN LORS DE L'IMPLEMENTATION NE PAS OUBLIER DE VERIFIER SI L'UTILISATEUR EST CONNECTÉ
    public function publierTouite(string $idutilisateur, string $texte) : bool{

        if(235 < strlen($texte)){
            throw new TouiteTropLong("Ce touite dépasse la limite de 235 charactères");
        }

        $query = "SELECT MAX(id_touite) as nbtouite FROM TOUITE";

        $result = $this->db->query($query);

        $row = $result->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $idtouite = $row['nbtouite']+1;
        }

        // On insère un nouveau touite dans la table touite
        $query = "INSERT INTO TOUITE (id_touite, id_utilisateur, texte, datePub) VALUES (:id_touite, :id_utilisateur, :texte, NOW())";
        $jaime = 0;
        $dislike = 0;

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_touite', $idtouite, PDO::PARAM_STR);
        $stmt->bindParam(':id_utilisateur', $idutilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':texte', $texte, PDO::PARAM_STR);
        $stmt->execute();

        $tags = [];
        // Recherche des occurrences de hashtags dans le texte
        preg_match_all('/#(\w+)/', $texte, $matches);

        // $matches[0] contient l'ensemble des hashtags trouvés
        if(isset($matches[0]) && is_array($matches[0])){
            $tags = $matches[0];
        }

        foreach ($tags as $tag) {
            // Insérer l'association dans la table des tags
            $query = "INSERT INTO TAG (libelletag, id_touite) VALUES (:libelletag, :id_touite)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':libelletag', $tag, PDO::PARAM_STR);
            $stmt->bindParam(':id_touite', $idtouite, PDO::PARAM_STR);
            $stmt->execute();
        }

        //On retourne true si la publication du touite à marcher
        return true;
    }

    public function evaluerTouite(string $idtouite, int $eval) : bool{
        echo "ID Touite: " . $idtouite . "<br>";
        echo "Like: " . $eval . "<br>";
        if($eval == 1){
            $query = "UPDATE TOUITE SET `jaime` = `jaime` + 1 WHERE id_touite = :idtouite";
        }
        else{
            $query = "UPDATE TOUITE SET `dislike` = `dislike` + 1 WHERE id_touite = :idtouite";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmt->execute();

        //On retourne true si l'update du touite à marcher
        return true;
    }

    public function effacerTouite(string $idtouite) : bool{

        $queryTag = "DELETE FROM TAG WHERE id_touite = :idtouite";
        $stmtTag = $this->db->prepare($queryTag);
        $stmtTag->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmtTag->execute();

        $queryTouite = "DELETE FROM Touite WHERE id_touite = :idtouite";
        $stmtTouite = $this->db->prepare($queryTouite);
        $stmtTouite->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmtTouite->execute();

        return true;
    }

    public function suivreUtilisateur(string $idUtilisateurSuivis) : bool {

        $query = "SELECT MAX(id_suivreU) as nbsuivre FROM AbonnementUtil";

        $result = $this->db->query($query);

        $row = $result->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $idSuivreU = $row['nbsuivre']+1;
        }

        $query = "INSERT INTO AbonnementUtil (id_suivreU, utilisateurSuivis, utilisateurSuiveurU) VALUES (:idSuivreU, :idUtilisateurSuivis, :idUtilisateurSuiveurU)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idSuivreU', $idSuivreU, PDO::PARAM_STR);
        $stmt->bindParam(':idUtilisateurSuivis', $idUtilisateurSuivis, PDO::PARAM_STR);
        $stmt->bindParam(':idUtilisateurSuiveurU', $_SESSION['user_id'], PDO::PARAM_STR);
        return $stmt->execute(); //Si la requête s'execute alors le suivi a bien eu lieu
    }

    public function estDejaSuivi($id_utilisateur, $id_suiveur) : bool{

        $query = "SELECT COUNT(*) FROM AbonnementUtil WHERE utilisateurSuivis = :id_utilisateur AND utilisateurSuiveurU = :id_suiveur";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':id_suiveur', $id_suiveur, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        return $result > 0;
    }


}



?>
