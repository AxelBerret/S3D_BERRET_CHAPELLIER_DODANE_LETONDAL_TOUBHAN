<?php

declare(strict_types=1);
class afficherTouitesTag{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
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
                <link rel="stylesheet" href="../CSS/accueil.css">
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
        if ($this->estDejaAbonneAuTag($tag)) {
            echo<<<HTML
            <li><form action="dispatcher.php" method="get">
                        <input type="hidden" name="action" value="nePlusSuivreTag">
                        <input type="hidden" name="libelleTag" value="$tag">
                        <button type="submit" class="btn-suivre">Ne plus suivre ce tag</button>
                    </form></li>
HTML;
        }else{
            echo<<<HTML
            <li><form action="dispatcher.php" method="get">
                        <input type="hidden" name="action" value="suivreTag">
                        <input type="hidden" name="libelleTag" value="$tag">
                        <button type="submit" class="btn-suivre">Suivre ce tag</button>
                    </form></li>
HTML;
        }
        if (isset($_SESSION['user_id'])) {
            echo <<<HTML
                    <li><a href="dispatcher.php"><img src="../images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="HTML/tendances.html"><img src="../images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                    <li><a href="dispatcher.php?action=AfficherSonProfil"><img src="../images/profil.png" alt="" class="menu-icon">Profil</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="dispatcher.php?action=afficherTouitesTag&tag=Tag1" class="tag">#Tag1</a>
                    <a href="dispatcher.php?action=afficherTouitesTag&tag=Tag2" class="tag">#Tag2</a>
                    <a href="dispatcher.php?action=afficherTouitesTag&tag=Tag3" class="tag">#Tag3</a>
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
                    <div class="recherche-tag">
                    <form action="dispatcher.php" method="get">
                        <input type="text" name="action" value="afficherTouitesTag" style="display: none;">
                        <input type="text" name="tag" placeholder="Rechercher des tags..." class="tag-search-input">
                        <button type="submit" class="tag-search-button">Rechercher</button>
                    </form>
                    </div>
                    <li><a href="dispatcher.php"><img src="../images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="tendances.html"><img src="../images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@$nom $prenom</div>
                </div>

                <div class="tendances-container">
                    <div class="tendance-title">Tendances France</div>
                    <a href="dispatcher.php?action=afficherTouitesTag&tag=Tag1" class="tag">#Tag1</a>
                    <a href="dispatcher.php?action=afficherTouitesTag&tag=Tag2" class="tag">#Tag2</a>
                    <a href="dispatcher.php?action=afficherTouitesTag&tag=Tag3" class="tag">#Tag3</a>
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

    public function estDejaAbonneAuTag(string $tagSuivis): bool {
        $query = "SELECT * FROM AbonnementTag WHERE id_utilisateur = :id_utilisateur AND tagSuivis = :tagSuivis";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->bindParam(':tagSuivis', $tagSuivis, PDO::PARAM_STR);
        $stmt->execute();

        // Si une ligne est trouvée, l'utilisateur suit déjà ce tag
        $result = $stmt->fetch() !== false;

        return $result;
    }

}