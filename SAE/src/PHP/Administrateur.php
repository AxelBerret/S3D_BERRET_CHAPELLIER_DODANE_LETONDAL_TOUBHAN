<?php

declare(strict_types=1);

class Administrateur{

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function afficherInfluenceurs(){

        $query = "SELECT id_utilisateur, nom, prenom, COUNT(utilisateurSuiveur) AS nbSuiveurs FROM Utilisateur
                  INNER JOIN AbonnementUtil ON Utilisateur.id_utilisateur = AbonnementUtil.utilisateurSuivis
                  GROUP BY id_utilisateur, nom, prenom
                  ORDER BY nbSuiveurs DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Touiteur - Influenceurs</title>
                <link rel="stylesheet" href="../CSS/influenceurs.css">
                <link rel="icon" type="image/jpeg" href="../Images/icon.png">
            </head>
        
            <body>
        
            <div class="container">
                <header>
                    <img src="../Images/logo.jpeg" alt="Logo Touiteur" class="logo">
                </header>
        
                <main class="content">
                    <h2>Liste des Influenceurs</h2>
                    <ul class="influenceurs-list">
HTML;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo <<<HTML
                    <li>
                        <span class="influenceur-username">@$row[nom] $row[prenom]</span>
                        <span class="nb-suiveurs">($row[nbSuiveurs] suiveurs)</span>
                    </li>
HTML;
        }

        echo <<<HTML
                    </ul>
                </main>
            </div>
            </body>
            </html>
HTML;
    }

    public function afficherTagsTendances()
    {
        $query = "SELECT Tag.libelleTag, COUNT(Tag.libelleTag) AS nombre_mentions FROM Tag
                    JOIN Touite ON Tag.id_touite = Touite.id_touite
                    GROUP BY Tag.libelleTag
                    ORDER BY nombre_mentions DESC";

        $stmt = $this->db->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Touiteur - Tags tendances</title>
                <link rel="stylesheet" href="../CSS/influenceurs.css">
                <link rel="icon" type="image/jpeg" href="../Images/icon.png">
            </head>
        
            <body>
        
            <div class="container">
                <header>
                    <img src="../Images/logo.jpeg" alt="Logo Touiteur" class="logo">
                </header>
        
                <main class="content">
                    <h2>Liste des Tags tendances</h2>
                    <ul class="influenceurs-list">
HTML;
        foreach ($results as $row) {
            echo<<<HTML
            <a href='dispatcher.php?action=afficherTouitesTag&tag={$row['libelleTag']}' class='tag'>{$row['libelleTag']} ({$row['nombre_mentions']} mentions)</a>
            <br>
            HTML;
        }
        echo <<<HTML
                        </div>
                    </main>
                </div>
            </body>
            </html>
HTML;
    }
}
?>