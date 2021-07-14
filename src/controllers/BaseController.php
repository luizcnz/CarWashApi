<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 15:50
 */

namespace Api\controllers;

use Psr\Container\ContainerInterface;

class BaseController
{
    protected $conteiner;
    public function  __construct(ContainerInterface $container)
    {
        $this->conteiner=$container;
    }
}