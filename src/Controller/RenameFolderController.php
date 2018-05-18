<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PwBox\Model\User;
use PwBox\Model\UserRepository;

class RenameFolderController
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


        $idFolder = $data['idCarpeta'];
        $newNameCarpeta = $data['newNameCarpeta'];
        $oldNameCarpeta = $data['nomCarpeta'];
        $idUsuari = $_COOKIE['user_id'];

        /** @var UserRepository $repo */
        $repo = $this->container->get('user_repository');

        $permisos = $repo->userPrivileges($idFolder, $idUsuari);


        if ($permisos['admin']) {

            $folderPath = $repo->getFolderPath($idFolder);

            $repo->renameFolder($idFolder, $newNameCarpeta);
            //$result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");

            /*if(count($result) == 0){
                $result[0] = "./uploads/default-avatar.jpg";
            }*/

            $oldPath = '/../uploads/' . $_COOKIE['user_id'] . '/' . $oldNameCarpeta;

            // Define the new directory
            $newPath = '/../uploads/' . $_COOKIE['user_id'] . '/' . $newNameCarpeta;

            // Renames the directory
            rename($oldPath, $newPath);

            $idCarpetaParent = $repo->getParentFolderId($folderPath['urlPath']);

            $urlPath = $repo->getFolderPath($idCarpetaParent);

            /*$carpetes = $repo->showDirectory($idCarpetaParent, $idUsuari);

            $url = "/dashboard/" . $urlPath;*/

            $response_array['id'] = $idFolder;
            $response_array['newName1'] = $newNameCarpeta;

            return $response->withJson($response_array, 200);

        } else {

            $response_array['status'] = 'Error';

            header('Content-type: application/json');
            echo json_encode($response_array);
        }


        //return $this->container->get('view')->render($response, 'error.twig', ['errorCode' => 'Forbidden']);

    }
}