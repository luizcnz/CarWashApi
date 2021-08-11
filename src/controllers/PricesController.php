<?php

namespace Api\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\models\prices\Prices;
use Api\utils\status\Constants;
use Api\utils\ResponseServer;

class  PricesController extends BaseController
    {
        public function  getAllPrices(Request $request, Response $response, $args){


            $respuesta = new ResponseServer();

            $datos = $request->getQueryParams();

            if($datos["disponible_domicilio"]==1)
            {
                $sql = "SELECT 
                Precios.idPrecios,
                Precios.precio,
                Servicios.nombre_servicio,
                TiposVehiculos.tipo_vehiculo,
                Servicios.disponible_domicilio
                from Precios 
                INNER JOIN Servicios
                on Precios.idServicios = Servicios.idServicios
                INNER JOIN TiposVehiculos
                on Precios.idTipoVehiculos = TiposVehiculos.idTipoVehiculos
                WHERE Precios.idTipoVehiculos=".$datos["idTipoVehiculos"]." and Servicios.disponible_domicilio = 1";
            }
            else
            {
                $sql = "SELECT 
                Precios.idPrecios,
                Precios.precio,
                Servicios.nombre_servicio,
                TiposVehiculos.tipo_vehiculo,
                Servicios.disponible_domicilio
                from Precios 
                INNER JOIN Servicios
                on Precios.idServicios = Servicios.idServicios
                INNER JOIN TiposVehiculos
                on Precios.idTipoVehiculos = TiposVehiculos.idTipoVehiculos
                WHERE Precios.idTipoVehiculos=".$datos["idTipoVehiculos"];
            }
            
            $array=[];
            $codeStatus=0;
        
            try
            {
                $db = $this->conteiner->get("db");
                $resultado = $db->query($sql);
        
                if ($resultado->rowCount() > 0)
                {
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message ="Consulta realizada con exito";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;
                    $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Prices::class);
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
            $array_response['servicios'] = $array_data;

            $array_response['respuesta'] = $respuesta;
    
            $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);
        }

        public function  getOnePrice(Request $request, Response $response, $args){

            $respuesta = new ResponseServer();
            $datos = $request->getQueryParams();

            $sql = "SELECT 
            Precios.idPrecios,
            ModelosVehiculos.modelo,
            Precios.precio,
            Servicios.nombre_servicio,
            TiposVehiculos.tipo_vehiculo
            from Precios 
            INNER JOIN Servicios
            on Precios.idServicios = Servicios.idServicios
            INNER JOIN TiposVehiculos
            on Precios.idTipoVehiculos = TiposVehiculos.idTipoVehiculos
            INNER JOIN ModelosVehiculos
            on TiposVehiculos.idTipoVehiculos = ModelosVehiculos.idTipoVehiculos
            INNER JOIN Vehiculos
            on ModelosVehiculos.idModeloVehiculos = Vehiculos.idModeloVehiculos
            WHERE Vehiculos.idUsuario=".$datos["idUsuario"] ." and Vehiculos.idVehiculos =".$datos["idVehiculos"];
            $array=[];
            $codeStatus=0;
        
            try
            {
                $db = $this->conteiner->get("db");
                $resultado = $db->query($sql);
        
                if ($resultado->rowCount() > 0)
                {
                    $codeStatus=Constants::CREATE;

                    $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Prices::class),JSON_NUMERIC_CHECK));
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
            }
             return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                            ->withStatus($codeStatus);
        }
    }
