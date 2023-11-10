<?php

declare(strict_types=1);
require_once 'ConnectionFactory.php';

class UtilisateurNarcissique
{
    private $pdo;

    public function __construct()
    {
        ConnectionFactory::setConfig('db.config.ini');
        $this->pdo = ConnectionFactory::makeConnection();
    }

    public function execute()
    {
        //Récupération des infos du compte connecté :
        if (isset($_SESSION['user_id'])){
            $id = $_SESSION['user_id'];
        }else{
            $id=null;
        }
        $query = "SELECT id_utilisateur, nom, prenom, email FROM utilisateur where id_utilisateur = :id ";
        $query = $this->pdo->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $result = $query->execute();
        if ($result) {
            if ($query->rowCount() > 0) {
                $stockage = $query->fetchAll(PDO::FETCH_ASSOC);

                foreach ($stockage as $row) {
                    $id = $row['id_utilisateur'];
                    $nom = $row['nom'];
                    $prenom = $row['prenom'];
                    $email = $row['email'];
                }
            }
        }else {
            echo "Erreur lors de l'exécution de la requête.";
        }
        //Récupération des abonnés
        $query = "SELECT id_utilisateur, nom, prenom FROM utilisateur INNER JOIN abonnementutil ON utilisateur.id_utilisateur = abonnementutil.utilisateurSuiveur WHERE abonnementutil.utilisateurSuivis = :id";
        $query = $this->pdo->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $tailleTableau = count($results);


        $htmlString = '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur - Accueil</title>
            <link rel="stylesheet" href="../CSS/profil.css">
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
            <div class="main-content">
                <div class="profil-main-container">
                    <div class="profil-titles"><h3>Liste de vos abonnés</h3></div>
                    <ul class="menu">';
        for ($i=0;$i<$tailleTableau;$i++){
            $htmlString .= "<li><a href='dispatcher.php?action=afficherTouitesUtilisateur&id_utilisateur={$results[$i]['id_utilisateur']}'>Nom : {$results[$i]['nom']} Prénom : {$results[$i]['prenom']}</a></li>";
    }
        $htmlString .= '
                    </ul>
                </div>
            </div>
            <aside class="sidebar">
                <nav>
                    <ul class="menu">';
        if (isset($_SESSION['user_id'])) {
            $htmlString .= '                    <li><a href="dispatcher.php?action=afficherMonMur"><img src="../images/mur_accueil.png" alt="" class="menu-icon">Mon Mur</a></li>
 <li><a href="dispatcher.php">
 <img src="../images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                    <li><a href="HTML/tendances.html"><img src="../images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                    <li><a href="dispatcher.php?action=afficherSonProfil"><img src="../images/profil.png" alt="" class="menu-icon">Profil</a></li>
                </ul>
                <div class="profile-module">
                <div class="profile-username">@' . $nom . ' ' . $prenom . '</div>
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
                </nav>
            </aside>
            <main class="content">
            </main>
        </div>
        </body>
        </html>';
        } else {
            $htmlString .= '<li><a href="dispatcher.php"><img src="../images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
                        <li><a href="dispatcher.php?action=afficherTendances"><img src="../images/icon_tendances.png" alt="" class="menu-icon">Tendances</a></li>
                        <li><a href="dispatcher.php?action=afficherSonProfil"><img src="../images/icon_profil.png" alt="" class="menu-icon">Profil</a></li>
                    </ul>
                    <div class="profile-module">
                        <div class="profile-username">@votreIdentifiant</div>
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
                    <form action="login.html" method="post">
                        <button type="submit" class="btn-connexion">Se connecter</button>
                    </form>
                    <form action="signup.html" method="post">
                        <button type="submit" class="btn-inscription">S\'inscrire</button>
                    </form>
                </nav>
            </aside>
            <main class="content">
            </main>
        </div>
        </body>
        </html>';
        }
        echo $htmlString;
    }
}
