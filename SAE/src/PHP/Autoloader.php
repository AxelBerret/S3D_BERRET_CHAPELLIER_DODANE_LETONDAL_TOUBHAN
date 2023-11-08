<?php

class Autoloader {
    public static function register() {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    public static function autoload($class) {


        // Comme vu en cours,on remplace les caractères \\ par '/'
        $classFile = __DIR__.'/'.str_replace('\\', '/', $class).'.php';

        // On vérifie ensuite si le fichier existe afin d'éviter les erreurs
        if (file_exists($classFile)) {
            include $classFile;
        }
    }
}