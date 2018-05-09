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

class RegisterController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){
        return $this->container->get('view')->render($response, 'register.twig', []);
    }

    //Amb aquesta funcio obtenim les dades del request i despres de comprovar, guradem les dades.
    public function submit (Request $request, Response $response) {
        try{
            $data = $request->getParsedBody();
            var_dump($data);
            //Validate
            //if (isset($data['email'])) {}
            //$service = $this->container->get('post_user_use_case');
            //$service($data);

        }catch(\Exception $e){
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write('Something went wrong');
        }
        return $response;
    }

}