<?php

declare(strict_types=1);

class Touite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    function afficherListeTouites() {

        // Requête permettant de récupérer tous les touites et les trier par date récente
        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom FROM TOUITE 
                 JOIN UTILISATEUR ON TOUITE.Id_utilisateur = UTILISATEUR.id_utilisateur 
                 ORDER BY TOUITE.datePub DESC";

        $result = this->db->query($query);

        // On affiche ensuite tout les touites
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "Auteur: {$row['nom']} {$row['prenom']}<br>";
            $texteCourt = substr($row['texte'], 0, 80);

            if (strlen($texteCourt) > 80) {
                $texteCourt .= '...';
            }

            echo "Touite: {$texteCourt}<br>";
            echo "<hr>";
        }
    }

    function afficherTouiteDetail(string $idtouite) {

        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom FROM TOUITE 
                  JOIN UTILISATEUR ON TOUITE.Id_utilisateur = UTILISATEUR.id_utilisateur 
                  WHERE TOUITE.id_touite = :touite_id";

        $stmt = this->db->prepare($query);
        $stmt->bindParam(':touite_id', $idtouite, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo "Auteur: {$row['nom']} {$row['prenom']}<br>";
            echo "Touite: {$row['texte']}<br>";
            echo "Date de publication: {$row['datePub']}<br>";
            echo "<hr>";
        }
    }

    function afficherTouitesUtilisateur(string $idutilisateur) {

        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom FROM TOUITE 
                  JOIN UTILISATEUR ON t.Id_utilisateur = UTILISATEUR.id_utilisateur 
                  WHERE TOUITE.id_utilisateur = :utilisateur_id 
                  ORDER BY TOUITE.datePub DESC";

        $stmt = this->db->prepare($query);
        $stmt->bindParam(':utilisateur_id', $idutilisateur, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "Auteur: {$row['nom']} {$row['prenom']}<br>";
            echo "Touite: {$row['texte']}<br>";
            echo "Date de publication: {$row['datePub']}<br>";
            echo "<hr>";
        }
    }

    function afficherTouitesTag(string $tag){

        //On cherche dans tous les touites le tag voulu
        $query = "SELECT * FROM TOUITE WHERE id_touite IN (SELECT id_touite FROM TOUITE WHERE text LIKE :tag)";

        $stmt = this->db->prepare($query);

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


}



?>
