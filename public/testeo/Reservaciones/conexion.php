<?php

$servidor="173.249.21.6";
$usuario="movil2";
$clave="carwash2021";
$bd="carwashcatrachodb";


$db = new mysqli();

$db->connect($servidor,$usuario,$clave,$bd);

if ($db->connect_errno!=null) 
{
	echo "Error Numero".$db->connect_errno;
}
else
{
	//echo "Conexion Exitosa ";
}

?>