<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:53
 */

use Slim\Routing\RouteCollectorProxy;
$app -> group("/v1",function (RouteCollectorProxy $group){
    $group->get("/vehicles","Api\controllers\VehiclesController:getAllVehicles");
});

//$app -> group("/v1/users",function (RouteCollectorProxy $group2){
//    $group2->get("/","Api\controllers\UsuariosController:getAllUsers");
//});