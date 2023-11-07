<?php
class ConnectionFactory{
    private static $config;
    private static $pdo;
    public static function setConfig($configName){
        self::$config = parse_ini_file($configName);
    }

    public static function makeConnection(){
        if (!self::$config) {
            throw new Exception('La configuration n\'a pas été définie. Utilisez setConfig($configFileName) pour la définir.');
        }
        if (!self::$pdo) {
            $dsn = self::$config['driver'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['database'] . ';charset=utf8';
            $username = self::$config['username'];
            $password = self::$config['password'];

            try {
                self::$pdo = new PDO($dsn, $username, $password);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                throw new Exception('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
