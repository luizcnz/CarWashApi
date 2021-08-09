<?php

namespace Api\controllers;

use Api\utils\status\Constants;
use Api\utils\ResponseServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\models\quotes\Quotes;
use Api\models\quotes\Oil;
use Api\utils\status\CodeStatus;


class  QuotesController extends BaseController
    {
        public function  addQuote(Request $request, Response $response, $args){

            $respuesta = new ResponseServer();
            $datos = $request->getParsedBody();
            $servicios=$datos["servicios"];

            $sql = "INSERT INTO  Cotizaciones(fecha_hora, estado, longitud, latitud, idEmpleado, idVehiculos ) 
            VALUES (:fecha_hora, :estado, :longitud, :latitud, :idEmpleado, :idVehiculos)";

            // $sql = "INSERT INTO  Cotizaciones(estado, idEmpleado, idVehiculos ) 
            // VALUES (:estado, :idEmpleado, :idVehiculos)";

            

            $codeStatus=0;

            
            
            //$id=9;
            //echo "New record created successfully. Last inserted ID is: " . $id;
            

            try
            {
                $db = $this->conteiner->get("db");
                $stament=$db->prepare($sql);
                $stament->bindParam(":fecha_hora",$datos["fecha_hora"]);
                // $stament->bindParam(":impuesto",$datos["impuesto"]);
                // $stament->bindParam(":subtotal",$datos["subtotal"]);
                // $stament->bindParam(":total",$datos["total"]);
                 $stament->bindParam(":estado",$datos["estado"]);
                // $stament->bindParam(":descuento",$datos["descuento"]);
                $stament->bindParam(":longitud",$datos["longitud"]);
                $stament->bindParam(":latitud",$datos["latitud"]);

                $stament->bindParam(":idEmpleado",$datos["idEmpleado"]);
                $stament->bindParam(":idVehiculos",$datos["idVehiculos"]);
                $res = $stament->execute();
                

                if($res)
                {
                    //$id=$db->insert_id();
                    $id=$db->lastInsertId();
                    //echo "New record created successfully. Last inserted ID is: " . $id;
                    foreach($servicios as $idServicio)
                    {
                        

                        $sql2="INSERT INTO  DetallesCotizacion(idCotizaciones,idServicios) 
                        VALUES(".$id.",".$idServicio.")";

                        try
                        {
                            $db = $this->conteiner->get("db");
                            $stament2=$db->prepare($sql2);
                            $res2 = $stament2->execute();

                            if($res2){
                                $codeStatus=Constants::CREATE;
                                $respuesta->status=Constants::Ok;
                                $respuesta->message ="Guardado con exito";
                                $respuesta->codeStatus=$codeStatus;
                                $respuesta->statusSession=true;
                                $respuesta->token=null;
                                
                            }
                        }
                        catch(\PDOException $e)
                        {
                            
                            $codeStatus=Constants::SERVER_ERROR;
                            $respuesta->status=Constants::ERROR;
                            $respuesta->message ="Error" .$e->getMessage();
                            $respuesta->codeStatus=$codeStatus;
                            $respuesta->statusSession=true;
                            $respuesta->token=null;
                        }
                    }
                }
                
            }
            catch(\PDOException $e)
            {
                $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
            }


            //$data_array;
            $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);

        }

        public function  getOil(Request $request, Response $response, $args){
        
            $datos = $request->getParsedBody();
            $respuesta = new ResponseServer();
    
            $sql = "SELECT 
            Vehiculos.idVehiculos,
            ModelosVehiculos.modelo,
            Servicios.nombre_servicio,
            DATE_FORMAT(Cotizaciones.fecha_hora, '%e %M %Y') as fecha,
            DATE_FORMAT(Cotizaciones.fecha_hora, '%k:%i') as hora
            from Cotizaciones 
            INNER JOIN DetallesCotizacion
            on Cotizaciones.idCotizaciones = DetallesCotizacion.idCotizaciones
            INNER JOIN Vehiculos
            on Vehiculos.idVehiculos = Cotizaciones.idVehiculos
            INNER JOIN ModelosVehiculos
            on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
            INNER JOIN Servicios
            on Servicios.idServicios = DetallesCotizacion.idServicios
            WHERE Vehiculos.idUsuario=".$datos["idUsuario"]." and Vehiculos.idVehiculos =".$datos["idVehiculos"]." and Servicios.idServicios=2";
            
            $array_data=[];
            $codeStatus=0;
    
    
            try
            {
                $db = $this->conteiner->get("db");
                $sql2 = "SET lc_time_names = 'es_ES'";
                $es=$db->query($sql2);
                $resultado = $db->query($sql);
    
                if ($resultado->rowCount() > 0)
                {
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message ="Consulta realizada con exito";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;
                    $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Oil::class);
                }
                else
                {
                    $codeStatus=Constants::CREATE;
                    $respuesta->status=Constants::NO_EXIST;
                    $respuesta->message ="No hay registros en la base de datos";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;
                    //json_encode("po existen registros en la BBDD.");
                }
            }
            catch(Exception $e)
            {
                $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
            }
            
            $array_response['historial'] = $array_data;

            $array_response['respuesta'] = $respuesta;

            //array_push($array_data, $respuesta);

            
            $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);
        }


        public function  getAllQuotes(Request $request, Response $response, $args){
        
            //$datos = $request->getParsedBody();
            $respuesta = new ResponseServer();

            $sql = "SELECT Cotizaciones.idCotizaciones,
            Cotizaciones.fecha_hora,
            Cotizaciones.impuesto,
            Cotizaciones.subtotal,
            Cotizaciones.total,
            Cotizaciones.estado,
            Cotizaciones.descuento,
            Cotizaciones.longitud,
            Cotizaciones.latitud,
            CONCAT(Empleado.nombre, ' ', Empleado.apellido)as Empleado,	
            CONCAT(MarcasVehiculos.marca, ' ', ModelosVehiculos.modelo) as Vehiculo
            from Cotizaciones
            INNER JOIN Vehiculos
            on Cotizaciones.idVehiculos = Vehiculos.idVehiculos
            INNER JOIN ModelosVehiculos
            on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
            INNER JOIN MarcasVehiculos
            on Vehiculos.idMarcaVehiculos = MarcasVehiculos.idMarcaVehiculos
			INNER JOIN Empleado
            on Cotizaciones.idEmpleado = Empleado.idEmpleado
            WHERE Vehiculos.idUsuario=".$args["idUsuario"];

            $array=[];
            $codeStatus=0;
    
    
            try
            {
                $db = $this->conteiner->get("db");
                $resultado = $db->query($sql);
    
                

                if ($resultado->rowCount() > 0)
                {

                    $json_response = array(); //Create an array
                    
                    while ($row = $resultado->fetch(\PDO::FETCH_BOTH))
                    {
                       //	$row_array = get_object_vars($resultado->fetch());
                        $row_array = array();
                        $row_array['idCotizaciones'] = $row['idCotizaciones'];        
                        $row_array['fecha_hora'] = $row['fecha_hora'];
                        $row_array['impuesto'] = $row['impuesto'];
                        $row_array['subtotal'] = $row['subtotal'];
                        $row_array['total'] = $row['total'];
                        $row_array['estado'] = $row['estado'];
                        $row_array['descuento'] = $row['descuento'];
                        $row_array['longitud'] = $row['longitud'];
                        $row_array['latitud'] = $row['latitud'];
                        $row_array['Empleado'] = $row['Empleado'];
                        $row_array['Vehiculo'] = $row['Vehiculo'];
                        
                        $row_array['DetallesCotizacion'] = array();
                        $idCotizaciones = $row['idCotizaciones'];  

                        $detalles_qry = "SELECT 
                        DetallesCotizacion.idDetalleCotizacion,
                        DetallesCotizacion.idCotizaciones, 
                        Servicios.nombre_servicio
                        FROM DetallesCotizacion 
                        INNER JOIN Servicios
                        on DetallesCotizacion.idServicios = Servicios.idServicios
                        WHERE DetallesCotizacion.idCotizaciones=".$idCotizaciones;
                        $detalles_resultado= $db->query($detalles_qry);

                        //$array_out=array();
                        while ($DetalleCotizacion =  $detalles_resultado->fetch(\PDO::FETCH_BOTH))
                        {
                            $row_array['DetallesCotizacion'][] = 
                            //array_push($array_out,
                            [
                                'idDetalleCotizacionions' => $DetalleCotizacion['idDetalleCotizacion'],
                                'idCotizaciones' => $DetalleCotizacion['idCotizaciones'],
                                'idServicios' => $DetalleCotizacion['nombre_servicio']
                            ];

                        }

                        array_push($json_response, $row_array); //push the values in the array
                        
                        
                    }

                    //----------------------------------------
                    

                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message ="Consulta realizada con exito";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;

                }
                else
                {
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::NO_EXIST;
                    $respuesta->message ="No hay registros en la base de datos ";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;
                    //json_encode("po existen registros en la BBDD.");
                }
            }
            catch(Exception $e)
            {
                $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error: ".$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
            }
             

            array_push($json_response, $respuesta);

            $response->getBody()->write(json_encode($json_response,JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);

        }

}
