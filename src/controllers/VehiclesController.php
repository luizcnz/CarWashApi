<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 14:17
 */

namespace Api\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\controllers\BaseController;

class  VehiclesController extends BaseController {

    public function  getAllVehicles(Request $request, Response $response, $args){
        $sql = "SELECT * FROM DetallesFactura";
        $array=[];
        try
        {

            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                array_push($array, $resultado->fetchAll());
            }
            else
            {
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
        }
        return $response->withJson($array);


    }
//        $valores= $this->conteiner->get("db_settings");
//     echo var_dump($valores);
//    $response->getBody()->write("Hola");
//    return $response;
//}

}