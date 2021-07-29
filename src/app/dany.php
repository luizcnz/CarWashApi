<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:54
 */
use Slim\Routing\RouteCollectorProxy;
use Api\controllers\UsuariosController;
use Api\controllers\VehiclesController;

//$app -> group("/v1/vehicles",function (RouteCollectorProxy $group){
//    $group->get("",VehiclesController::class.":getAllVehicles");
//});
$app -> group("/v1/users",function (RouteCollectorProxy $group){
    $group->get("",UsuariosController::class.":getAllUsers");
    
    $group->post("/verify",UsuariosController::class.":verifyNumberPhone");
    $group->post("/add",UsuariosController::class.":addUser");
});