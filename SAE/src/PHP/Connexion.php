<?php

declare(strict_types = 1);

class Connexion{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }


        public function login($email, $password){
        // On utilise des requêtes préparées afin de prévenir les injections SQL
        $query = "SELECT * FROM Utilisateur  WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Ici, on utilise la fonction php password_verify pour vérifier le mot de passe hashé
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            return true;
        }
        return false;
    }

    public function getInfluenceurs(int $limit = 10) {
        $query = "select u.id_utilisateur, u.nom, u.prenom, count(AU.id_suivreU) as nbSuiveurs
                    from Utilisateur u left join AbonnementUtil au 
                        on U.id_utilisateur = au.utilisateurSuivis
                          group by u.id_utilisateur, u.nom, u.prenom
                            order by nbSuiveurs desc
                                limit :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>