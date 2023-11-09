<?php

declare(strict_types=1);

class effacerTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function effacerTouite(string $idtouite) : bool{

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