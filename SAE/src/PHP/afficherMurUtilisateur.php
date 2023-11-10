<?php

declare(strict_types=1);

class afficherMurUtilisateur{

private $db;

public function __construct(PDO $db) {
$this->db = $db;
}

    public function afficherMurUtilisateur(string $idUtilisateur) {
            // Récupérer les touites des tags suivis par l'utilisateur
            $queryTags = "
                SELECT t.id_touite, t.id_utilisateur, t.texte, t.datePub
                FROM Touite t
                INNER JOIN AbonnementTag at ON t.id_utilisateur = at.id_utilisateur
                WHERE at.utilisateurSuiveurT = :idUtilisateur
            ";
            $stmtTags = $this->db->prepare($queryTags);
            $stmtTags->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
            $stmtTags->execute();
            $touitesTags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

            // Récupérer les touites des utilisateurs suivis par l'utilisateur
            $queryUtilisateurs = "
                SELECT t.id_touite, t.id_utilisateur, t.texte, t.datePub
                FROM Touite t
                INNER JOIN AbonnementUtil au ON t.id_utilisateur = au.utilisateurSuivis
                WHERE au.utilisateurSuiveur = :idUtilisateur
            ";
            $stmtUtilisateurs = $this->db->prepare($queryUtilisateurs);
            $stmtUtilisateurs->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
            $stmtUtilisateurs->execute();
            $touitesUtilisateurs = $stmtUtilisateurs->fetchAll(PDO::FETCH_ASSOC);

            // Fusionner les résultats des deux requêtes
            $touites = array_merge($touitesTags, $touitesUtilisateurs);

            return $touites;
        }

}

?>