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
        $oldNameCarpeta = $data['oldNameCarpeta'];
        $idUsuari = $_COOKIE['user_id'];

        /** @var UserRepository $repo */
        $repo = $this->container->get('user_repository');

        $permisos = $repo->userPrivileges($idFolder, $idUsuari);

        if ($permisos['admin']) {

            $folderPath = $repo->getFolderPath($idFolder);

            $file = $repo->getFileById($idFolder);



            $repo->renameFolder($idFolder, $newNameCarpeta);

            if (!$file['esCarpeta']){
                $oldPath = './uploads/' . $_COOKIE['user_id'] . '/' . $oldNameCarpeta;

                // Define the new path
                $newPath = './uploads/' . $_COOKIE['user_id'] . '/' . $newNameCarpeta;

                // Renames the file
                rename($oldPath, $newPath);
            }



            $idCarpetaParent = $repo->getParentFolderId($folderPath['urlPath']);

            $urlPath = $repo->getFolderPath($idCarpetaParent);

            /*$carpetes = $repo->showDirectory($idCarpetaParent, $idUsuari);

            $url = "/dashboard/" . $urlPath;*/

            $response_array['id'] = $idFolder;
            $response_array['newName'] = $newNameCarpeta;
            $response_array['permision'] = true;
            return $response->withJson($response_array, 200);

        } else {

            $response_array['permision'] = false;
            return $response->withJson($response_array, 200);
        }
        //return $this->container->get('view')->render($response, 'error.twig', ['errorCode' => 'Forbidden']);
    }

    public function renameShared(Request $request, Response $response, array $args)
    {

        $data = $request->getParsedBody();

        $idFolder = $data['idCarpeta'];
        $idFitxer = $idFolder;
        $newNameCarpeta = $data['newNameCarpeta'];
        $oldNameCarpeta = $data['oldNameCarpeta'];
        $urlPath = $data['path'];
        $idUsuari = $_COOKIE['user_id'];

        /** @var UserRepository $repo */
        $repo = $this->container->get('user_repository');

        $permisos = $repo->userPrivilegesShared($idFolder, $idUsuari);

        $file = $repo->getFileById($idFolder);


        if ($file['esCarpeta'] == '0') {

            $idFolder = $repo->getParentFolderId($urlPath);

            $permisos = $repo->userPrivilegesShared($idFolder['carpetaParent'], $idUsuari);


        }

        if ($permisos['admin'] == '1') {

            //Element is a folder
            if ($file['esCarpeta']) {
                $folderPath = $repo->getFolderPath($idFolder);

                $file = $repo->getFileById($idFolder);
                $repo->renameFolder($idFolder, $newNameCarpeta);
            }


            //Element is a file
            if ($file['esCarpeta'] == '0'){
                $idFolder = $repo->getParentFolderId($urlPath);
                $idPropietari = $repo->getUserIdByDirectoryId($idFolder);


                $oldPath = './uploads/' .  $idPropietari['id_usuari'] . '/' . $oldNameCarpeta;

                // Define the new path
                $newPath = './uploads/' .  $idPropietari['id_usuari'] . '/' . $newNameCarpeta;

                // Renames the file
                rename($oldPath, $newPath);
                $repo->renameFolder($idFitxer, $newNameCarpeta);

            }

            $response_array['id'] = $idFolder;
            $response_array['newName'] = $newNameCarpeta;
            $response_array['permision'] = true;
            return $response->withJson($response_array, 200);

        } else {

            $response_array['permision'] = false;
            return $response->withJson($response_array, 200);
        }
        //return $this->container->get('view')->render($response, 'error.twig', ['errorCode' => 'Forbidden']);
    }
}