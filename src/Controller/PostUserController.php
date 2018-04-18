<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class PostUserController{

    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response){
        try{
            $data = $request->getParsedBody();
            //Validate
            //isset($data[email]){};
            $service = $this->container->get('post_user_use_case');
            $service($data);
        }catch(\Exception $e){
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write('Something went wrong');
        }
        return $response;
    }
}