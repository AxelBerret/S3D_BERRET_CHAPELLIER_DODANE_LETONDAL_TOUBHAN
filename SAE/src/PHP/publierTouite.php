<?php

declare(strict_types=1);

class publierTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function publierTouite(string $idutilisateur, string $texte, $image) : bool{

        if(235 < strlen($texte)){
            throw new TouiteTropLong("Ce touite dépasse la limite de 235 charactères");
        }
        $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'ImagesTouite' . DIRECTORY_SEPARATOR;  // Répertoire où les images seront stockées
        $filename = uniqid();  // nom de fichier unique
        $tmp = $image['image']['tmp_name'];
        $allowedFileType = ['image/jpeg', 'image/png', 'image/gif']; // Types de fichiers autorisés

        if (
            ($_FILES['image']['error'] === UPLOAD_ERR_OK) &&
            (in_array($_FILES['image']['type'], $allowedFileType))
        ) {
            // Vérification du type de fichier et déplacement vers le répertoire de destination
            $dest = $upload_dir . $filename . '.png';  // Nom de fichier
            if (move_uploaded_file($tmp, $dest)) {
                echo "Téléchargement terminé avec succès<br>";
            } else {
                echo "Hum, hum, téléchargement non valide<br>";
            }
        } else {
            echo "Échec du téléchargement ou type non autorisé<br>";
        }

        $dest = str_replace('\\', '/', $dest);// ON essaye de remplacer les backslash pour que ça soit bien lu par le html

        // On insère un nouveau touite dans la table touite
        $query = "INSERT INTO TOUITE (id_utilisateur, texte, image, jaime, dislike, datePub) VALUES ( :id_utilisateur, :texte, :image, :jaime, :dislike, NOW())";
        $jaime = 0;
        $dislike = 0;

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $idutilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':texte', $texte, PDO::PARAM_STR);
        $stmt->bindParam(':image', $dest, PDO::PARAM_STR);
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

        $query = "SELECT id_touite, datePub FROM touite WHERE id_utilisateur = :id_utilisateur AND datePub = NOW()";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $idutilisateur, PDO::PARAM_STR);
        $stmt->execute();

        $idtouite =  $stmt->fetch(PDO::FETCH_ASSOC);


        foreach ($tags as $tag) {
            // Insérer l'association dans la table des tags
            $query = "INSERT INTO TAG (libelletag, id_touite) VALUES (:libelletag, :id_touite)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':libelletag', $tag, PDO::PARAM_STR);
            $stmt->bindParam(':id_touite', $idtouite['id_touite'], PDO::PARAM_STR);
            $stmt->execute();
        }

        //On retourne true si la publication du touite à marcher
        return true;
    }

}