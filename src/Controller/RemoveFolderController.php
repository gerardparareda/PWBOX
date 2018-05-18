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

        //$idFolderEsborrar = $args['id'];
        //var_dump($idFolderEsborrar);

        $repo = $this->container->get('user_repository');

        $this->deleteDirectory($idFolderEsborrar);


        /** @var UserRepository $repo */
        $repo = $this->container->get('user_repository');

        $path = $repo->getFolderPath($idFolderEsborrar);

        $fullPath = '/dashboard' . $path;

        return $response->withStatus(200)->withHeader("Location", $fullPath);

        //return $response;

        //return $this->container->get('view')->render($response, 'dashboard.twig', []);

    }

    public function deleteDirectory ($idCarpetesBorrar) {

        $repo = $this->container->get('user_repository');

        foreach ($idCarpetesBorrar as $idCarpetaBorrar) {

            $idCarpetesChild = $repo->getCarpetesChildId();

            if (!empty($idCarpetesChild)) {

                $this->deleteDirectory($idCarpetesChild);

            } else {

                //Remove current folder.

                //Borrar els fitxers fisics del sistema.

                //Primer recuperem si es un fitxer, i el nom del fitxer.

                $isFile = $repo->isFile($idCarpetaBorrar);

                if ($isFile) {

                    $file = $repo->getFileById($idCarpetaBorrar);

                    $userId = $_COOKIE['user_id']; //Es necessita per al path es /../userId/file['nomCarpeta']

                    //TODO: Borrar fitxer fisicament del sistema a partir del nom.

                }


                //Despres borrem la carpeta de la bbdd.

                $repo->removeFolder($idCarpetaBorrar);



            }

        }

    }
}