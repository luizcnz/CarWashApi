<?php


namespace Api\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


class  ModelsController extends BaseController {

    public function  getAllModels(Request $request, Response $response, $args){
        $sql = "SELECT * FROM ModelosVehiculos";
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


}