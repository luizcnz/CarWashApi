<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:53
 */

use Slim\Routing\RouteCollectorProxy;
use API\controllers;
$app -> group("/v1/vehicles",function (RouteCollectorProxy $group){
    //$group->get("","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/{idusuario}","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/{idusuario}/{idvehiculo}","Api\controllers\VehiclesController:getVehicleByUserId");
    $group->post("","Api\controllers\VehiclesController:addVehicle");
    $group->put("","Api\controllers\VehiclesController:updateVehicle");
});



//$app -> group("/v1/users",function (RouteCollectorProxy $group2){
//    $group2->get("/","Api\controllers\UsuariosController:getAllUsers");
//});

// $app -> group("/v1/models",function (RouteCollectorProxy $group){
//     $group->get("","Api\controllers\ModelsController:getAllModels");
// });