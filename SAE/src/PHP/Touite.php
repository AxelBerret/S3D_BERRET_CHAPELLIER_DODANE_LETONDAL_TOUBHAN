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
                    <form action="poster.html" method="post">
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
                    <form action="poster.html" method="post">
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
                <form action="poster.html" method="post">
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

        //On cherche dans tous les touites le tag voulu
        $query = "SELECT * FROM TOUITE WHERE id_touite IN (SELECT id_touite FROM TOUITE WHERE text LIKE :tag)";

        $stmt = $this->db->prepare($query);

        $tag = '#'.$tag;
        $tag = '%'.$tag.'%';

        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Auteur: {$row['nom']} {$row['prenom']}<br>";
            echo "Touite: {$row['texte']}<br>";
            echo "Date de publication: {$row['datePub']}<br>";
            echo "<hr>";
        }

    }

    //DANS LE MAIN LORS DE L'IMPLEMENTATION NE PAS OUBLIER DE VERIFIER SI L'UTILISATEUR EST CONNECTÉ
    public function publierTouite(string $idutilisateur, string $texte) : bool{

        if(235 < strlen($texte)){
            throw new TouiteTropLong("Ce touite dépasse la limite de 235 charactères");
        }

        $query = "SELECT COUNT(id_touite) as nbtouite FROM TOUITE";

        $result = $this->db->query($query);

        $row = $result->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $idtouite = $row['nbtouite'];
        }

        // On insère un nouveau touite dans la table touite
        $query = "INSERT INTO TOUITE (id_touite, id_utilisateur, texte, datePub) VALUES ($idtouite, :id_utilisateur, :texte, NOW())";
        $jaime = 0;
        $dislike = 0;

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $idutilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':texte', $texte, PDO::PARAM_STR);
        $stmt->execute();

        $query = "INSERT INTO NOTE (id_touite, jaime, dislike) VALUES ($idtouite, 0, 0)";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        //On retourne true si la publication du touite à marcher
        return true;
    }

    public function evaluerTouite(string $idtouite, bool $eval) : bool{

        if($eval){
            $query = "UPDATE NOTE SET `jaime` = `jaime` + 1 WHERE id_touite = :idtouite";
        }
        else{
            $query = "UPDATE NOTE SET `dislike` = `dislike` + 1 WHERE id_touite = :idtouite";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmt->execute();

        //On retourne true si l'update du touite à marcher
        return true;
    }

    public function effacerTouite(string $idtouite) : bool{

        $query = "DELETE FROM Touite WHERE id_touite = :idtouite";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }


}



?>
