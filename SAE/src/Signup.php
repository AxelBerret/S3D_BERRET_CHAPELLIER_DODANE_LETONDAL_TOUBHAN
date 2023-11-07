<?php

declare(strict_types=1);

class DoublonEmailException extends Exception {}
class MailInvalideException extends Exception {}
class MDPNonRobusteException extends Exception {}

class Signup{

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function signup( string $email, string $password):bool {

        // On vérifie que ce mail n'a pas déjà un compte
        $query = "SELECT COUNT(*) FROM Utilisateur WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            // Si on trouve un compte, on lève une exception
            throw new DoublonEmailException("Cet utilisateur existe déjà.");
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Si l'email n'est pas valide, on lève une exception
            throw new MailInvalideException("L'adresse e-mail que vous avez fournie n'est pas valide. Veuillez vérifier et réessayer.");
        }

        if(!$this->checkPasswordStrength($password, 8)) {
            // Si le mdp n'est pas assez robuste, on lève une exception
            throw new MDPNonRobusteException("Le mot de passe n'est pas assez robuste, vérifiez les exigences.");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost'=>12]);
        "insert into Utilisateur (email, passwd ) values($email, $hash)";
        return true ;
    }


    //On contrôle la robustesse du mot de passe avec cette fonction
    public function checkPasswordStrength(string $pass, int $minimumLength = 8): bool{
        $length = (strlen($pass) < $minimumLength); // longueur minimale
        $digit = preg_match("#[\d]#", $pass); // au moins un digit
        $special = preg_match("#[\W]#", $pass); // au moins un car. spécial
        $lower = preg_match("#[a-z]#", $pass); // au moins une minuscule
        $upper = preg_match("#[A-Z]#", $pass); // au moins une majuscule
        if (!$length || !$digit || !$special || !$lower || !$upper) return false;
        return true;
    }


}




?>
