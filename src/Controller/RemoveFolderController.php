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

        $idFolderEsborrar[0] = $data['idCarpetaAEsborrar'];

        //$idFolderEsborrar = $args['id'];
        //var_dump($idFolderEsborrar);

        $this->deleteDirectory($idFolderEsborrar);

        die;

        $response_array = [];

        return $response->withJson($response_array, 200);
        //return $response;

        //return $this->container->get('view')->render($response, 'dashboard.twig', []);

    }

    public function deleteDirectory ($idCarpetesBorrar) {


        //var_dump($idCarpetesBorrar);
        //die;

        $repo = $this->container->get('user_repository');

        //foreach ($idCarpetesBorrar as $idCarpetaBorrar) {
        for ($i = 0; $i < sizeof($idCarpetesBorrar); $i++) {

            $idCarpetesChild = $repo->getCarpetesChildId($idCarpetesBorrar[$i]);

            if($i>0){
                var_dump('Pos actual' . $i);
                die;
            }

            //var_dump($idCarpetesBorrar[i]);
            //var_dump($idCarpetesChild);
            //die;


            if (sizeof($idCarpetesChild) > 0) {

                var_dump($idCarpetesChild);

                $info = $repo->getFileById($idCarpetesChild);
                var_dump($info);
                //var_dump(sizeof($idCarpetesChild));

                $this->deleteDirectory($idCarpetesChild);

            } else {

                //Remove current folder.

                //Borrar els fitxers fisics del sistema.

                //Primer recuperem si es un fitxer, i el nom del fitxer.

                $isFile = $repo->isFile($idCarpetesBorrar[$i]);

                if ($isFile) {

                    $file = $repo->getFileById($idCarpetesBorrar[$i]);

                    $userId = $_COOKIE['user_id']; //Es necessita per al path es /../userId/file['nomCarpeta']

                    //TODO: Borrar fitxer fisicament del sistema a partir del nom.

                }


                //Despres borrem la carpeta de la bbdd.

                $repo->removeFolder($idCarpetesBorrar[$i]);



            }

        }

    }
}