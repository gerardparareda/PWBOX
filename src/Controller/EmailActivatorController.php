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
use PwBox\Model\User;
use PwBox\Model\UserRepository;

    class EmailActivatorController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){

        $repo = $this->container->get('user_repository');

        $userId = $repo->getIdByHash($args['activatorId']);

        var_dump($userId);

        $repo->activateUserById($userId);

        $email = $repo->getEmailById($userId);
        $password = $repo->getPasswordById($userId);

        session_start();

        setcookie("user_id", (string)$userId,time() + 15778463, '/');
        setcookie("inputEmail", $email,time() + 15778463, '/');
        setcookie("inputPassword", $password,time() + 15778463, '/');
        setcookie("inputRememberMe", 'checked',time() + 15778463, '/');

        return $response->withStatus(302)->withHeader("Location", "/login");
    }

}