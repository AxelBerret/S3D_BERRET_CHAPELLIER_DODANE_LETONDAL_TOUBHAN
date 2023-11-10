<?php

declare(strict_types=1);

class publierTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function publierTouite(string $idutilisateur, string $texte, string $image) : bool{

        if(235 < strlen($texte)){
            throw new TouiteTropLong("Ce touite dépasse la limite de 235 charactères");
        }

        if ($image !== null && !empty($image['tmp_name'])) {
            $imagePath = '../images/';
            $imageName = uniqid() . '_' . $image['name'];
            $imageFullPath = $imagePath . $imageName;

            move_uploaded_file($image['tmp_name'], $imageFullPath);
        } else {
            $imageName = null;
        }


        // On insère un nouveau touite dans la table touite
        $query = "INSERT INTO TOUITE (id_utilisateur, texte, image, jaime, dislike, datePub) VALUES ( :id_utilisateur, :texte, :image, :jaime, :dislike, NOW())";
        $jaime = 0;
        $dislike = 0;

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $idutilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':texte', $texte, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imageName, PDO::PARAM_STR);
        $stmt->bindParam(':jaime', $jaime, PDO::PARAM_STR);
        $stmt->bindParam(':dislike', $dislike, PDO::PARAM_STR);
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