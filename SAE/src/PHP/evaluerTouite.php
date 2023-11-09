<?php

declare(strict_types=1);

class evaluerTouite{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function evaluerTouite(string $idtouite, int $eval) : bool{
        echo "ID Touite: " . $idtouite . "<br>";
        echo "Like: " . $eval . "<br>";
        if($eval == 1){
            $query = "UPDATE TOUITE SET `jaime` = `jaime` + 1 WHERE id_touite = :idtouite";
        }
        else{
            $query = "UPDATE TOUITE SET `dislike` = `dislike` + 1 WHERE id_touite = :idtouite";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idtouite', $idtouite, PDO::PARAM_STR);
        $stmt->execute();

        //On retourne true si l'update du touite à marcher
        return true;
    }
}