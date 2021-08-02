<?php

/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 13/7/2021
 * Time: 00:38
 */
//Container que retorna una configuración de base de datos
$mainContainer->set("db", function (){

    $user="movil2";
    $pass= "carwash2021";
    $host= "173.249.21.6";
    $dbName="carwashcatrachodb";
    $charSet="utf8";

    $options=[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ];
    $dsn = "mysql:host=".$host.";dbname=".$dbName.";charset=".$charSet;
    return new PDO($dsn,$user,$pass,$options);
});
