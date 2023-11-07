<?php

require 'PHP/Autoloader.php';
require_once 'PHP/ConnectionFactory.php';
ConnectionFactory::setConfig('db.config.ini');
$db = ConnectionFactory::makeConnection();

//Main global

?>
