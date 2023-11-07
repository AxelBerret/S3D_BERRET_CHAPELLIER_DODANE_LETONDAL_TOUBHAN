<?php

require 'Signup.php';

//Si le formulaire de connexion ou signup est fourni
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $db = new PDO('mysql:host=localhost;dbname=SAE_touiteur;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $signup = new Signup($db);

        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($signup->signup($email, $password)) {
            // Si l'inscription on redirige vers confirmation.php (à voir plus tard si la méthode header marche bien on l'a pas vu en cours
            header('Location: confirmation.php');
            exit;
        }
        //Ici les eventuelles erreur liées au signup de l'utilisateur
    } catch (DoublonEmailException $e) {
        $errorMessage = $e->getMessage();
    } catch (MailInvalideException $e) {
        $errorMessage = $e->getMessage();
    } catch (MDPNonRobusteException $e) {
        $errorMessage = $e->getMessage();
    }
}

?>