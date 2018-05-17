<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PwBox\Model\User;
use PwBox\Model\UserRepository;

class NewFolderController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Request $request, Response $response, array $args)
    {

        $data = $request->getParsedBody();


        $idFolder = $data['pathCarpetaRoot'];
        $nameCarpeta = $data['nameCarpeta'];
        $esCarpeta = $data['esCarpeta'];

        $idUsuari = $_COOKIE['user_id'];

        /** @var UserRepository $repo */
        $repo = $this->container->get('user_repository');

        $repo->createDirectory($nameCarpeta, $idFolder, $idUsuari, $esCarpeta);

        $path = $repo->getFolderPath($idFolder);

        $fullPath = '/dashboard' . $path;

        return $response->withStatus(302)->withHeader("Location", $fullPath);
        //return $this->container->get('view')->render($response, 'dashboard.twig', []);

    }
}