<?php

declare(strict_types=1);

class publierTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

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

}