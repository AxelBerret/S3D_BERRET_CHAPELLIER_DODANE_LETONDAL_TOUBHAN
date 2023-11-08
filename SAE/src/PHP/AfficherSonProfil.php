<?php
declare(strict_types=1);
require_once 'ConnectionFactory.php';

class AfficherSonProfil{
    private $pdo;

    public function __construct() {
        ConnectionFactory::setConfig('db.config.ini');
        $this->pdo = ConnectionFactory::makeConnection();
    }

    public function execute() : string{
        $id = $_SESSION['user_id'];
        $query = "SELECT id_utilisateur, nom, prenom, email FROM utilisateur where id_utilisateur = :id ";
        $query->bindParam(':id', $id), PDO::PARAM_STR);
        $result = $this->pdo->query($query);
        if ($result) {
            $stockage = $result->fetchAll(PDO::FETCH_ASSOC);

            foreach ($stockage as $row) {
                $id = $row['id_utilisateur'];
                $nom = $row['nom'];
                $prenom = $row['prenom'];
                $email = $row['email'];
            }
        }else {
            echo "Erreur lors de l'exécution de la requête.";
        }
        $res = "<strong>@$id</strong><br><h3>$nom $prenom</h3><br><p>email : $email</p>";
        return $res;
    }
}
