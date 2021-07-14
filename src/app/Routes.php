
<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 14:03
 */

use Slim\Routing\RouteCollectorProxy;

$app -> group("/v1",function (RouteCollectorProxy $group){
    $group->get("/vehicles","Api\controllers\VehiclesController:getAllVehicles");
});
