
<?php
	include("conexion.php");
?>

<html>
<head>
  <meta charset="UTF-8">
  <title>Cotizaciones</title>
  
  
  
      <link rel="stylesheet" href="css/style.css">

  
</head>

<body bgcolor="linear-gradient(45deg, #49a09d, #5f2c82)">


<div style="position: relative;left:0px; padding: 3%;background: linear-gradient(45deg, #49a09d, #5f2c82);">
	<center>
	<TABLE >
		<tr>
			<td`>
				<font size ="4"color="#ffffff">  

					<form>
						<table >
							<thead>	
								<tr> 
									<font size ="4"color="#ffffff">  
										<th>Codigo</th>
										<th>Usuario</th>
										<th>Vehiculo</th>
										<th>Fecha y Hora</th>
										<th>Estado Actual </th>
										<th>Revisar</th>
									</font>
								</tr>
							</thead>
						<tbody>
							<?php 
								
								$resultado = $db->query("
									SELECT Cotizaciones.idCotizaciones,
									CONCAT(Usuarios.nombre, ' ', Usuarios.apellido) as Usuario,
									CONCAT(MarcasVehiculos.marca, ' ', ModelosVehiculos.modelo) as Vehiculo,
									Cotizaciones.fecha_hora,
									Cotizaciones.estado
									from Cotizaciones
									INNER JOIN Vehiculos
									on Cotizaciones.idVehiculos = Vehiculos.idVehiculos
									INNER JOIN ModelosVehiculos
									on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
									INNER JOIN MarcasVehiculos
									on Vehiculos.idMarcaVehiculos = MarcasVehiculos.idMarcaVehiculos
									INNER JOIN Usuarios
									on Vehiculos.idUsuario = Usuarios.idUsuario
									where Cotizaciones.estado = 'pendiente'");

								$fila = $resultado->fetch_array(MYSQLI_ASSOC); 
								while ($fila != null)
								{ 
									echo "
										<tr>
											<td>".$fila['idCotizaciones']."</td>
											<td>".$fila['Usuario']."</td>
											<td>".$fila['Vehiculo']."</td>
											<td>".$fila['fecha_hora']."</td>
											<td>".$fila['estado']."</td>
											<td align='center'>
											<form method='post' action='Datos/Datos.php'>
												<input type='hidden' name='id' value='" . json_encode($fila['idCotizaciones']) . "' />
							                	<button type='submit'><img src='check.png'></button>
							                </form>
										</td>


										</tr>";
										//<td align='center'><img src='check.png' onClick='return revisar(".$fila['idCotizaciones'].")'></td>

										$fila = $resultado->fetch_array(MYSQLI_ASSOC);
								}
								$resultado->free(); 
						
								?>
							</tbody>
						</table>
					</form>
				</font>
			</td>
		</tr>
	</TABLE>
	</center>
	<div class="form-group last col-sm-12">
		<a href="http://api.com/testeo/index.html"><font size="5" color="white">Regresar</a>
	</div>
</div>
  
</body>


</html>
