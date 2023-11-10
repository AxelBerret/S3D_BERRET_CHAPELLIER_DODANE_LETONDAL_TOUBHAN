<?php

declare(strict_types=1);

class evaluerTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function evaluerTouite(string $idtouite, int $eval): bool {
        $id_utilisateur = $_SESSION['user_id'];

        // Vérifier si l'utilisateur a déjà évalué ce touite
        $query = "SELECT * FROM InteractionTouite WHERE id_utilisateur = :id_utilisateur AND id_touite = :id_touite";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $stmt->bindParam(':id_touite', $idtouite, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // L'utilisateur n'a pas encore évalué ce touite
            if ($eval == 1) {
                // L'utilisateur aime le touite (like)
                $query = "UPDATE TOUITE SET `jaime` = `jaime` + 1 WHERE id_touite = :idtouite";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
                $stmt->execute();

                $type_interaction = "jaime";
            } else {
                // L'utilisateur n'aime pas le touite (dislike)
                $query = "UPDATE TOUITE SET `dislike` = `dislike` + 1 WHERE id_touite = :idtouite";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
                $stmt->execute();

                $type_interaction = "dislike";
            }

            // Insérer dans la table interactiontouite
            $query = "INSERT INTO INTERACTIONTOUITE(id_touite, id_utilisateur, type_interaction) VALUES (:id_touite, :id_utilisateur, :type_interaction)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_touite', $idtouite, PDO::PARAM_STR);
            $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_STR);
            $stmt->bindParam(':type_interaction', $type_interaction, PDO::PARAM_STR);
            $stmt->execute();

            // Retourner true si l'insertion a réussi
            return true;
        }

        // L'utilisateur a déjà évalué ce touite
        return false;
    }
}