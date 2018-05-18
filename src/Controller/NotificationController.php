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

class NotificationController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){

        $repo = $this->container->get('user_repository');

        $notifications = $repo->showNotifications($_COOKIE['user_id']);

        return $this->container->get('view')->render($response, 'notifications.twig', ['notifications' => $notifications]);
    }

}