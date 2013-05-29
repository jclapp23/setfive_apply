<?php

require_once "DB.class.php";
require_once "User.class.php";
require_once "UserTable.class.php";
require_once "config.class.php";



$db = new DB(array('dsn'=>Config::$dsn,'username'=>Config::$username,'password'=>Config::$password));

$db->getConnection();



$user = new User($db);



$user->setFirstName("Jared");
$user->setLastName("Clapp");
$user->setEmail("jared@jmcdesignstudios.com");
$user->setId("200");
$user->save();

var_dump($user);

echo $user->getFirstName();
echo $user->getLastName();
echo $user->getEmail();
echo $user->getId();


