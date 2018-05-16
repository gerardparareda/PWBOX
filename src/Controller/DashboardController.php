<?php
/**
 * Created by PhpStorm.
 * User: Gerard
 * Date: 02/05/2018
 * Time: 19:38
 */

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class DashboardController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){

        $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");

        if(!isset($result)){
            $result = "./uploads/default-avatar.jpg";
        }


        return $this->container->get('view')->render($response, 'dashboard.twig', ['user_avatar' => $result[0]]);
    }

}