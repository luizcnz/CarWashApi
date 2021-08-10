<?php
include("conexion.php");

$idcotizacion=$_POST["id"];
$acepta = null;
$rechazar = null;

$aceptar = $_POST["accion"];
$rechazar = $_POST["accion"];

echo "var aceptar: ".$aceptar;
echo "var rechazar: ".$rechazar;

echo json_encode($_POST);

if($aceptar=="aceptar")
        {
        	try
        	{
        		$resultado = $db->query("UPDATE Cotizaciones
        		set estado = 'aceptado'
        		where idCotizaciones=".$idcotizacion);

        	echo "<script>alert('Se ha aceptado la reservacion'.$idcotizacion); window.location='http://api.com/testeo/reservaciones/reservaciones.php';</script>";
        	}
        	catch(Exception $e)
        	{
        		echo $e;
        	}
        	
       	}
else if($rechazar=="rechazar")
	{
		try
		{

		$resultado = $db->query("UPDATE Cotizaciones
        		set estado = 'rechazado'
        		where idCotizaciones=".$idcotizacion);
		echo "<script>alert('Se ha aceptado la reservacion'.$idcotizacion); window.location='http://api.com/testeo/reservaciones/reservaciones.php';</script>";
		}
    	catch(Exception $e)
    	{
    		echo $e;
    	}
	}

?>

