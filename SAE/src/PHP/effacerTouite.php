<?php

declare(strict_types=1);

class effacerTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function effacerTouite(string $idtouite) : bool{

        $queryInteraction = "DELETE FROM InteractionTouite WHERE id_touite = :idtouite";
        $stmtInteraction = $this->db->prepare($queryInteraction);
        $stmtInteraction->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmtInteraction->execute();

        $queryTag = "DELETE FROM TAG WHERE id_touite = :idtouite";
        $stmtTag = $this->db->prepare($queryTag);
        $stmtTag->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmtTag->execute();

        $queryTouite = "DELETE FROM Touite WHERE id_touite = :idtouite";
        $stmtTouite = $this->db->prepare($queryTouite);
        $stmtTouite->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmtTouite->execute();

        return true;
    }

}