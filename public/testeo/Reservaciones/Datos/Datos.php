<?php

	include("conexion.php");
	$IdCotizacion=$_POST["id"];
	
	$resultado = $db->query("Select 
		Cotizaciones.idCotizaciones,
		Cotizaciones.fecha_hora,
		Cotizaciones.impuesto,
		Cotizaciones.subtotal,
		Cotizaciones.total,
		Cotizaciones.estado,
		Cotizaciones.descuento,
		Cotizaciones.longitud,
		Cotizaciones.latitud,
		CONCAT(Usuarios.nombre, ' ', Usuarios.apellido) as Usuario,
		CONCAT(MarcasVehiculos.marca, ' ', ModelosVehiculos.modelo) as Vehiculo,
		ModelosVehiculos.idTipoVehiculos as tipo_vehiculo,
		Cotizaciones.domicilio
		from Cotizaciones
		INNER JOIN Vehiculos
		on Cotizaciones.idVehiculos = Vehiculos.idVehiculos
		INNER JOIN ModelosVehiculos
		on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
		INNER JOIN MarcasVehiculos
		on Vehiculos.idMarcaVehiculos = MarcasVehiculos.idMarcaVehiculos
		INNER JOIN Usuarios
		on Vehiculos.idUsuario = Usuarios.idUsuario
		WHERE Cotizaciones.idCotizaciones=".$IdCotizacion);

		$datos=[];
		$datos = $resultado->fetch_array(MYSQLI_ASSOC);

		echo $datos['tipo_vehiculo'];

		if($datos['domicilio']==0)
		{
			$tipo="En Establecimiento";
			$extra=0;
		}
		else
		{
			$tipo="A Domicilio";
			$extra=0.15;
		}

		//echo $IdCotizacion;
		
?>

<script>

	function aceptar()
	{
		document.getElementById("accion").value=1;
		document.getElementbyId("form").submit();
		return false;
	}


	function rechazar()
	{

		document.getElementById("accion").value=2;
		document.getElementbyId("form").submit();
		return false;
	}

</script>

<html lang="en-US">
<head>

	
	<meta charset="utf-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1"/>
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	
	<title>Detalles de la Cotizacion</title>
	<!-- set your website meta description and keywords -->
	
	<!-- Bootstrap Stylesheets -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Font Awesome Stylesheets -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Template Main Stylesheets -->
	<link rel="stylesheet" href="css/contact-form.css" type="text/css">	

	<link rel="stylesheet" href="css/style.css">
	
</head>



<body >
	
<center>
	<section id="contact-form-section" class="form-content-wrap">
		<div style="position: relative;left:0px; padding: 3%; width: 50%;">
			<div class="row">
				<div class="tab-content">
					<div class="col-sm-12">
						<div class="item-wrap">
							<div>
								
								<div class="col-sm-12">
									<div class="item-content colBottomMargin">
										<div class="item-info">
											<h2 class="item-title text-center">Detalles de la Reservacion</h2>
											
										</div><!--End item-info -->
										
								   </div><!--End item-content -->
								</div><!--End col -->
								<div class="col-md-12">
									<form name="form" id="form" method="POST" action="process.php">
												<div class="row">
													
													<div class="form-group col-sm-6">
														<label ><font color="white">Id de la Cotizacion</font></label>
														<input type="text" name="id" id="id" readonly="true" class="form-control" value="<?php echo $datos['idCotizaciones'];?>"></input>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">Vehiculo</font></label>
														<label  class="form-control"><?php echo $datos['Vehiculo'];?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">Usuario</font></label>
														<label class="form-control"><?php echo $datos['Usuario'];?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">Fecha y Hora</font></label>
														<label class="form-control"><?php echo $datos['fecha_hora'];?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">Tipo de Servicio</font></label>
														<label class="form-control"><?php echo $tipo;?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">SubTotal</font></label>
														<label class="form-control"><?php echo $datos['subtotal'];?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">ISV</font></label>
														<label class="form-control"><?php echo $datos['impuesto'];?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">Descuento</font></label>
														<label class="form-control"><?php echo $datos['descuento'];?></label>
													</div>
													
													<div class="form-group col-sm-6">
														<label ><font color="white">Total</font></label>
														<label class="form-control"><?php echo $datos['total'];?></label>
													</div>

													<br>
													<div class="form-group col-sm-6">
														<label ><font color="white">Latitud</font></label>
														<label class="form-control"><?php echo $datos['latitud'];?></label>
													</div>
													
													<div class="form-group col-sm-6">
														<label ><font color="white">Longitud</font></label>
														<label class="form-control"><?php echo $datos['longitud'];?></label>
													</div>

													<div class="form-group col-sm-6">
														<label ><font color="white">Estado</font></label>
														<label class="form-control"><?php echo $datos['estado'];?></label>
													</div>

													<center>
													<div class="form-group col-sm-6">
														<label ><font color="white">Observacion</font></label>
													<textarea rows="3" cols="40" name="obersvaciones" id="obersvaciones" maxlength="150"  value="" ></textarea>
													</div></center>

													<input type='hidden' name='accion' id='accion' value=''>

													<div class="form-group last col-sm-12">
														<input type="submit" name="accion" value="aceptar" id="accion" class="btn btn-custom"></input>
														<input type="submit" name="accion" value="rechazar" id="accion" class="btn btn-custom"></input>
													</div>
													<center>
														<table>
															<thead>	
																<tr> 
																	<th>Id</th>
																	<th>Id de Cotizacion</th>
																	<th>Servicio</th>
																	<th>Precio</th>
																</tr>
															</thead>
														<tbody>
															<?php 
																$total = 0;
																$resultado = $db->query("
																	SELECT 
																DetallesCotizacion.idDetalleCotizacion,
																DetallesCotizacion.idCotizaciones, 
																Servicios.nombre_servicio,
																Precios.precio
																FROM DetallesCotizacion 
																INNER JOIN Servicios
																on DetallesCotizacion.idServicios = Servicios.idServicios
																INNER JOIN Precios
																on Servicios.idServicios = Precios.idServicios
																INNER JOIN TiposVehiculos
																on Precios.idTipoVehiculos = TiposVehiculos.idTipoVehiculos
																WHERE DetallesCotizacion.idCotizaciones = ".$datos['idCotizaciones']." and TiposVehiculos.idTipoVehiculos = ".$datos['tipo_vehiculo']);
																

																$fila = $resultado->fetch_array(MYSQLI_ASSOC); 
																while ($fila != null)
																{ 
																	$total = $total+$fila['precio'];
																	echo "
																		<tr>
																			<td>".$fila['idDetalleCotizacion']."</td>
																			<td>".$fila['idCotizaciones']."</td>
																			<td>".$fila['nombre_servicio']."</td>
																			<td>".$fila['precio']."</td>
																		</tr>";
																		$fila = $resultado->fetch_array(MYSQLI_ASSOC);
																}
																$resultado->free(); 
																echo "
																<tr>
																	<td></td>
																	<td></td>
																	<td>Extra por Serv. a domicilio: </td>
																	<td>".$total*$extra."</td>
																</tr>";

																$total = $total+($total*$extra);
																echo "
																<tr>
																	<td></td>
																	<td></td>
																	<td>Total a Pagar: </td>
																	<td>".$total."</td>
																</tr>";

																?>
															</tbody>
														</table>
													</center>
												<br>
											</div><!-- end row -->
									</form><!-- end form -->
									
								</div>
							</div><!--End row -->
						</div><!-- end item-wrap -->
					</div><!--End col -->
				</div><!--End tab-content -->
			</div><!--End row -->

		</div><!--End container -->
	</section>
</center>
<div class="form-group last col-sm-12">
	<button name="regresar" class="btn btn-custom" onclick="window.location='http://api.com/testeo/reservaciones/reservaciones.php'">Regresar</button>
</div>
</body>

</html>
