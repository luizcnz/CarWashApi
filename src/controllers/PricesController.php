<?php

namespace Api\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\models\prices\Prices;
use Api\utils\status\Constants;

class  PricesController extends BaseController
    {
        public function  getPricesByVehicleType(Request $request, Response $response, $args){

            $datos = $request->getQueryParams();

            $sql = "SELECT 
            Precios.idPrecios,
            Precios.precio,
            Servicios.nombre_servicio,
            TiposVehiculos.tipo_vehiculo
            from Precios 
            INNER JOIN Servicios
            on Precios.idServicios = Servicios.idServicios
            INNER JOIN TiposVehiculos
            on Precios.idTipoVehiculos = TiposVehiculos.idTipoVehiculos
            WHERE Precios.idTipoVehiculos=".$datos["idtipovehiculo"];
            $array=[];
            $codeStatus=0;
        
            try
            {
                $db = $this->conteiner->get("db");
                $resultado = $db->query($sql);
        
                if ($resultado->rowCount() > 0)
                {
                    $codeStatus=Constants::CREATE;
                    array_push($array, $resultado->fetchAll(\PDO::FETCH_CLASS,Prices::class));
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
                ->withJson($array)
                            ->withStatus($codeStatus);
        }

        public function  getPricesByUserAndVehicle(Request $request, Response $response, $args){

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
            WHERE Vehiculos.idUsuario=".$datos["idusuario"] ." and Vehiculos.idVehiculos =".$datos["idvehiculo"];
            $array=[];
            $codeStatus=0;
        
            try
            {
                $db = $this->conteiner->get("db");
                $resultado = $db->query($sql);
        
                if ($resultado->rowCount() > 0)
                {
                    $codeStatus=Constants::CREATE;
                    array_push($array, $resultado->fetchAll(\PDO::FETCH_CLASS,Prices::class));
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
                ->withJson($array)
                            ->withStatus($codeStatus);
        }
    }
