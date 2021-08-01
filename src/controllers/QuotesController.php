<?php

namespace Api\controllers;

use Api\utils\status\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\models\quotes\Quotes;
use Api\models\quotes\Oil;
use Api\utils\status\CodeStatus;

class  QuotesController extends BaseController
    {
        public function  addQuote(Request $request, Response $response, $args){

            $datos = $request->getParsedBody();
            $servicios=$datos["servicios"];

            $sql = "INSERT INTO  Cotizaciones(fecha_hora, estado, longitud, latitud, idEmpleado, idVehiculos ) 
            VALUES (:fecha_hora, :estado, :longitud, :latitud, :idEmpleado, :idVehiculos)";

            // $sql = "INSERT INTO  Cotizaciones(estado, idEmpleado, idVehiculos ) 
            // VALUES (:estado, :idEmpleado, :idVehiculos)";

            $respuesta=[];

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
                    echo "New record created successfully. Last inserted ID is: " . $id;
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
                                $respuesta=["status" => "ok","msg"=>"Guardado con exito"];
                            }
                        }
                        catch(\PDOException $e)
                        {
                            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
                            $codeStatus=Constants::SERVER_ERROR;
                        }
                    }
                }
                
            }
            catch(\PDOException $e)
            {
                $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
                $codeStatus=Constants::SERVER_ERROR;
            }
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($respuesta)
                        ->withStatus($codeStatus);
        }

        public function  getOil(Request $request, Response $response, $args){
        
            $datos = $request->getParsedBody();
    
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
            
            $array=[];
            $codeStatus=0;
    
    
            try
            {
                $db = $this->conteiner->get("db");
                $sql2 = "SET lc_time_names = 'es_ES'";
                $es=$db->query($sql2);
                $resultado = $db->query($sql);
    
                if ($resultado->rowCount() > 0)
                {
                    $codeStatus=Constants::CREATE;
                    $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Oil::class),JSON_NUMERIC_CHECK));
                }
                else
                {
                    $codeStatus=Constants::NO_CONTENT;
                    array_push($array,["msg" =>"No hay registros en la base de datos"]);
                    //json_encode("po existen registros en la BBDD.");
                }
            }
            catch(Exception $e)
            {
                array_push($array,["error" => $e->getMessage()]);
                $codeStatus=Constants::SERVER_ERROR;
            }
             return $response->withHeader('Content-type', 'application/json;charset=utf-8')
    
                            ->withStatus($codeStatus);
        }

}
