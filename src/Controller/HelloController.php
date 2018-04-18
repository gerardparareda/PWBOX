<?php
namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class HelloController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){
        $name = $args['name'];
        return $this->container->get('view')->render($response, 'hello.twig', ['name' => $name]);
    }


    public function indexAction(Request $request, Response $response, array $args){
        $name = $args['name'];
        return $this->container->get('view')->render($response, 'hello.twig', ['name' => $name]);
    }
}