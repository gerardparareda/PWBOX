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

        $this->deleteDirectory($idFolderEsborrar, $_COOKIE['user_id']);

        $response_array = ['elementBorrat' => $idFolderEsborrar];

        return $response->withJson($response_array, 200);

    }

    //Rep un id de la carpeta que s'ha d'esborrar.
    public function deleteDirectory ($idCarpetesBorrar, $idUsuari) { //3

        $repo = $this->container->get('user_repository');

        $idsCarpetesChildDeBorrar = $repo->getCarpetesChildId($idCarpetesBorrar);

        for ($i = 0; $i < count($idsCarpetesChildDeBorrar); $i++) {
            $this->deleteDirectory($idsCarpetesChildDeBorrar[$i]['id'], $idUsuari);
        }

        $nom = $repo->getFitxerPerId($idCarpetesBorrar);
        if ($nom) {
            unlink(dirname(__FILE__).'/../../public/uploads/' . $idUsuari . '/' . $nom);
        }
        $repo->removeFolder($idCarpetesBorrar);

    }

    public function removeShared(Request $request, Response $response, array $args)
    {

        $data = $request->getParsedBody();

        $idFolderEsborrar = $data['idCarpetaAEsborrar'];
        $nomFolderEsborrar = $data['nomCarpetaAEsborrar'];
        $urlPath = $data['path'];

        $repo = $this->container->get('user_repository');

        $file = $repo->getFileById($idFolderEsborrar);

        if ($file['esCarpeta'] == '1') {

            $idUsuari = $repo->getUserIdByDirectoryId($idFolderEsborrar);

            $this->deleteDirectory($idFolderEsborrar, $idUsuari['id_usuari']);

            $response_array = ['elementBorrat' => $idFolderEsborrar];

        } else {

            $response_array = ['elementBorrat' => $idFolderEsborrar];

            $idParentFolder = $repo->getParentFolderId($urlPath);

            $idPropietari = $repo->getUserIdByDirectoryId($idParentFolder);

            $path = './uploads/' .  $idPropietari['id_usuari'] . '/' . $nomFolderEsborrar;

            unlink($path);

            $repo->removeFolder($idFolderEsborrar);

        }

        return $response->withJson($response_array, 200);

    }

}