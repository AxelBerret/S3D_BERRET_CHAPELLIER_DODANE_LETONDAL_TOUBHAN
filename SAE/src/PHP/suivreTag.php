<?php

declare(strict_types=1);

class suivreTag
{

    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function suivreTag(string $tagSuivis): bool
    {

        // Insérer un nouvel abonnement au tag dans la base de données
        $query = "INSERT INTO AbonnementTag (tagSuivis, id_utilisateur) VALUES (:tagSuivis, :id_utilisateur)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':tagSuivis', $tagSuivis, PDO::PARAM_STR);
        $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            // Le suivi est réussi
            return true;
        } else {
            // Erreur lors du suivi
            return false;
        }
    }

    public function nePlusSuivreTag(string $tag): bool{
        // Vérifier si l'utilisateur suit déjà ce tag
        $query = "SELECT COUNT(*) FROM AbonnementTag WHERE id_utilisateur = :id_utilisateur AND tagSuivis = :tag";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // L'utilisateur suit déjà ce tag, procéder à la suppression de l'abonnement
            $queryDelete = "DELETE FROM AbonnementTag WHERE id_utilisateur = :id_utilisateur AND tagSuivis = :tag";
            $stmtDelete = $this->db->prepare($queryDelete);
            $stmtDelete->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmtDelete->bindParam(':tag', $tag, PDO::PARAM_STR);
            $stmtDelete->execute();
        }
        //Le delete a bien marché
        return true;
    }
}