<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PwBox\Model\User;
use PwBox\Model\UserRepository;

class RemoveFolderController
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


        $idFolderEsborrar = $data['idCarpetaAEsborrar'];

        $this->deleteDirectory($idFolderEsborrar);

        $response_array = ['elementBorrat' => $idFolderEsborrar];

        return $response->withJson($response_array, 200);

    }

    //Rep un id de la carpeta que s'ha d'esborrar.
    public function deleteDirectory ($idCarpetesBorrar) { //3

        $repo = $this->container->get('user_repository');

        $idsCarpetesChildDeBorrar = $repo->getCarpetesChildId($idCarpetesBorrar); // 0 => 4

        //var_dump($idsCarpetesChildDeBorrar[0]); //4

        for ($i = 0; $i < count($idsCarpetesChildDeBorrar); $i++) { //Sizeof = 1; i = 0
            $this->deleteDirectory($idsCarpetesChildDeBorrar[$i]['id']);
        }

        $nom = $repo->getFitxerPerId($idCarpetesBorrar);
        if ($nom) {
            unlink(dirname(__FILE__).'/../../public/uploads/' . $_COOKIE['user_id'] . '/' . $nom);
        }
        $repo->removeFolder($idCarpetesBorrar);

    }

    public function removeShared(Request $request, Response $response, array $args)
    {

        $data = $request->getParsedBody();


        $idFolderEsborrar = $data['idCarpetaAEsborrar'];

        $this->deleteDirectory($idFolderEsborrar);

        $response_array = ['elementBorrat' => $idFolderEsborrar];

        return $response->withJson($response_array, 200);

    }

}