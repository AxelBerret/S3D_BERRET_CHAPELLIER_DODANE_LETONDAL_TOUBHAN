<?php
declare(strict_types=1);
require_once 'ConnectionFactory.php';
require_once 'Action.php';

class AfficherSonProfil extends Action{
    private $pdo;

    public function __construct($http_methode, $script_name) {
        parent::construct($http_methode, $script_name);
        ConnectionFactory::setConfig('db.config.ini');
        $this->pdo = ConnectionFactory::makeConnection();
    }

    public function execute() : string{
        $query = "SELECT id_utilisateur, nom, prenom, email FROM utilisateur";

        $result = $this->db->query($query);
        if ($result){
            $stockage = $result->fetchAll(PDO::FETCH_ASSOC);
        }
        foreach ($stockage as $row){
            $id = $row['id_utilisateur'];
            $nom = $row['nom'];
            $prenom = $row['prenom'];
            $email = $row['email'];
        } else {
            echo "Erreur lors de l'exécution de la requête.";
        }
        $res = '';
        return $res;
    }
}
