<?php
declare(strict_types=1);
require_once 'ConnectionFactory.php';

class AfficherSonProfil{
    private $pdo;

    public function __construct() {
        ConnectionFactory::setConfig('db.config.ini');
        $this->pdo = ConnectionFactory::makeConnection();
    }

    public function execute(){
        $id = $_SESSION['user_id'];
        $query = "SELECT id_utilisateur, nom, prenom, email FROM utilisateur where id_utilisateur = :id ";
        $query = $this->pdo->prepare($query);
        $query->bindParam(':id', $id, PDO::PARAM_STR);
        $result = $query->execute();
        if ($result) {
            $stockage = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($stockage as $row) {
                $id = $row['id_utilisateur'];
                $nom = $row['nom'];
                $prenom = $row['prenom'];
                $email = $row['email'];
            }
        }else {
            echo "Erreur lors de l'exécution de la requête.";
        }
        echo '$htmlString = <!DOCTYPE html>
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
                <form action="poster.html" method="post">
                    <button type="submit" class="btn-poster">Poster</button>
                </form>
            </div>
            <header>
                <img src="../images/logo.jpeg" alt="Logo Touiteur" class="logo">
            </header>
            <div class="main-content">
                <div class="profil-main-container">
                    <div class="profil-titles"><h3>Profil de username</h3></div>
                    <ul class="menu">
                        <li><a>'. $id . '</a></li>
                        <li><a>' . $nom . '</a></li>
                        <li><a>' . $prenom . '</a></li>
                        <li><a>' . $email . '</a></li>
                    </ul>
                </div>
            </div>
            <aside class="sidebar">
                <nav>
                    <ul class="menu">
                        <li><a href="dispatcher.php"><img src="../images/icon_accueil.png" alt="" class="menu-icon">Accueil</a></li>
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
}
