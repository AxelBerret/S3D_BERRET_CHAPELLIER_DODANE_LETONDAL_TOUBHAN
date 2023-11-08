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

        // On affiche ensuite tout les touites
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            //A FAIRE DEMAIN DANS LA CLASSE USER ETC
            echo "<a href='dispatcher.php?action=afficherMurUtilisateur&idtouite={$row['id_utilisateur']}' style='text-decoration: none; color: black;'>";
            echo "Auteur: {$row['nom']} {$row['prenom']}<br>";
            echo "</a><br>";
            // Créez un lien autour du touite en utilisant une balise <a> et l'ID du touite
            echo "<a href='dispatcher.php?action=afficherTouiteDetail&idtouite={$row['id_touite']}' style='text-decoration: none; color: black;'>";

            $texteCourt = substr($row['texte'], 0, 80);

            if (strlen($texteCourt) > 80) {
                $texteCourt .= '...';
            }

            // Affichez le texte du touite
            echo "Touite: {$texteCourt}";

            // Fermez la balise <a> pour créer le lien
            echo "</a><br>";
            echo "<hr>";
        }
    }

    function afficherTouiteDetail(string $idtouite) {

        $query = "SELECT TOUITE.*, UTILISATEUR.nom, UTILISATEUR.prenom FROM TOUITE 
                  JOIN UTILISATEUR ON TOUITE.Id_utilisateur = UTILISATEUR.id_utilisateur 
                  WHERE TOUITE.id_touite = :touite_id";

        $stmt = $this->db->prepare($query);
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

        $stmt = $this->db->prepare($query);
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
