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

        //$idUsuari = $_SESSION['user_id'];
        $idUsuari = $_COOKIE['user_id'];


        $repo = $this->container->get('user_repository');

        $permisos = $repo->userPrivileges($idFolder, $idUsuari);

        if ($permisos['admin']) {
            $repo->renameFolder($idFolder, $newNameCarpeta);

            $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");

            if(count($result) == 0){
                $result[0] = "./uploads/default-avatar.jpg";
            }

            $idCarpetaParent = $repo->getParentFolderId($args['path']);

            $urlPath = $repo->getFolderPath($idCarpetaParent);

            $carpetes = $repo->showDirectory($idCarpetaParent, $idUsuari);

            var_dump($data);
            var_dump($data);
            var_dump($data);

            $url = "/dashboard/" . $urlPath;

            //return $this->container->get('view')->render($response, 'dashboard.twig', ['user_avatar' => $result[0], 'carpetes' =>$carpetes]);

        } else {
            ?>
                <script>
                    alert("You don't have the rights to rename this folder");
                </script>
            <?php
        }


        return $this->container->get('view')->render($response, 'error.twig', ['errorCode' => 'Forbidden']);

    }
}