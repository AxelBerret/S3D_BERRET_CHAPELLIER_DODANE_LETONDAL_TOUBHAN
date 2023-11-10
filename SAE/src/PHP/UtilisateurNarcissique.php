<?php

declare(strict_types=1);
require_once 'ConnectionFactory.php';

class AfficherSonProfil
{
    private $pdo;

    public function __construct()
    {
        ConnectionFactory::setConfig('db.config.ini');
        $this->pdo = ConnectionFactory::makeConnection();
    }

    public function execute(){
        Select nom, prenom from utilisateur inner join abonnementutil on utilisateur.id_utilisateur = abonnementutil.utilisateurSuiveur where abonnementutil.utilisateurSuivis = 0
        echo $htmlString;
    }
}
