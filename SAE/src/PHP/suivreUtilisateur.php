<?php

declare(strict_types=1);

class suivreUtilisateur{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }


    public function suivreUtilisateur(string $idUtilisateurSuivis) : bool {

        $query = "INSERT INTO AbonnementUtil (utilisateurSuivis, utilisateurSuiveur) VALUES (:idUtilisateurSuivis, :idUtilisateurSuiveur)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idUtilisateurSuivis', $idUtilisateurSuivis, PDO::PARAM_STR);
        $stmt->bindParam(':idUtilisateurSuiveur', $_SESSION['user_id'], PDO::PARAM_STR);
        return $stmt->execute(); //Si la requête s'execute alors le suivi a bien eu lieu
    }

    public function nePlusSuivreUtilisateur(string $idutilisateur): bool{
        // Vérifier si l'utilisateur suit déjà ce tag
        $query = "SELECT COUNT(*) FROM AbonnementUtil WHERE utilisateurSuiveur = :utilisateurSuiveur AND utilisateurSuivis = :utilisateurSuivis";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':utilisateurSuiveur', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':utilisateurSuivis', $idutilisateur, PDO::PARAM_STR);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // L'utilisateur suit déjà ce tag, procéder à la suppression de l'abonnement
            $queryDelete = "DELETE FROM AbonnementUtil WHERE utilisateurSuiveur = :utilisateurSuiveur AND utilisateurSuivis = :utilisateurSuivis";
            $stmtDelete = $this->db->prepare($queryDelete);
            $stmtDelete->bindParam(':utilisateurSuiveur', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmtDelete->bindParam(':utilisateurSuivis', $idutilisateur, PDO::PARAM_STR);
            $stmtDelete->execute();
        }
        //Le delete a bien marché
        return true;
    }


}