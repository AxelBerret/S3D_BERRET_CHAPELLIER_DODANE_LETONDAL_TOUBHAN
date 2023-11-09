<?php

declare(strict_types=1);

class suivreUtilisateur{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }


    public function suivreUtilisateur(string $idUtilisateurSuivis) : bool {

        $query = "SELECT MAX(id_suivreU) as nbsuivre FROM AbonnementUtil";

        $result = $this->db->query($query);

        $row = $result->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $idSuivreU = $row['nbsuivre']+1;
        }

        $query = "INSERT INTO AbonnementUtil (id_suivreU, utilisateurSuivis, utilisateurSuiveurU) VALUES (:idSuivreU, :idUtilisateurSuivis, :idUtilisateurSuiveurU)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idSuivreU', $idSuivreU, PDO::PARAM_STR);
        $stmt->bindParam(':idUtilisateurSuivis', $idUtilisateurSuivis, PDO::PARAM_STR);
        $stmt->bindParam(':idUtilisateurSuiveurU', $_SESSION['user_id'], PDO::PARAM_STR);
        return $stmt->execute(); //Si la requête s'execute alors le suivi a bien eu lieu
    }


}