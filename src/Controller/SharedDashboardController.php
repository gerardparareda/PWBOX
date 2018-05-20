<?php
/**
 * Created by PhpStorm.
 * User: Gerard
 * Date: 02/05/2018
 * Time: 19:38
 */

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class SharedDashboardController{
    protected $container;

    public function __construct(ContainerInterface $container){
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
    public function __invoke(Request $request, Response $response, array $args){

        $repo = $this->container->get('user_repository');

        $result = glob ("/../uploads/" . $_COOKIE['user_id'] . ".*");

        if(count($result) == 0){
            $result[0] = "/../uploads/default-avatar.jpg";
        }

        //Comprovem si l'usuari ha entrat amb un path a una carpeta o no. Nomes entra aqui si te la cookie perque
        //hi ha el middleware d'abans que mira si hi ha cookie.

        if (empty($args)) {

            //L'usuari ha entrat a la dashboard general, carpeta root.

            //Busquem que hi ha a la carpeta.
            //Aqui he de cridar una funcio que buscara una carpeta root fent uc.id_usuari = uc.id_carpeta = idUsuari
            // i d.id = uc.id_carpeta i d.root = true;
            //$carpeta = $repo->getRootFolderId($_SESSION['user_id']);
            $carpetes = $repo->showSharedDirectory($_COOKIE['user_id']);


            return $this->container->get('view')->render($response, 'sharedDashboard.twig',
                ['user_avatar' => $result[0], 'carpetes' => $carpetes]);

        } else {


            //Carpeta concreta

            //Comprovem si la carpeta existeix a la bbdd.

            $carpeta = $repo->lookIfDirectoryExists($args['path']);

            if ($carpeta == null) {

                return $response->withStatus(302)->withHeader("Location", "/sharedDashboard");

            } else {

                //$idUsuari = $_SESSION['user_id'];
                $idUsuari = $_COOKIE['user_id'];

                $privileges = $repo->userPrivilegesShared($carpeta['id'], $idUsuari);

                if ($privileges['admin'] || $privileges['reader']) {

                    //Mostrem el contingut de la carpeta.
                    $fitxers = $repo->showFilesOfSharedDirectory($carpeta['id']);

                    if (!$repo->esCarpeta($args['path'])) {

                        $idCarpetaParent = $repo->getParentFolderId($args['path']);
                        //$carpetes = $repo->showDirectory($idCarpetaParent, $idUsuari);

                        return $this->container->get('view')->render($response, 'sharedDashboard.twig',
                            ['user_avatar' => $result[0], 'carpetes' => $fitxers]);
                    } else {

                        return $this->container->get('view')->render($response, 'sharedDashboard.twig',
                            ['user_avatar' => $result[0], 'carpetes' => $fitxers, 'carpetaParent' => $carpeta['id']]);

                    }
                }
            }
        }

        //return $this->container->get('view')->render($response, 'dashboard.twig', ['user_avatar' => $result[0]]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function downloadFile(Request $request, Response $response, array $args) {

        $fieldId = $args['id'];

        if (!empty($fieldId)) {

            //Busquem si tenim el fitxer a la bbdd.
            $repo = $this->container->get('user_repository');

            $dirInfo = $repo->getIdByUrlPath($fieldId);

            $idUsuari = $repo->getUserIdByDirectoryId($dirInfo);

            $fileName = __DIR__ . '/../../public/uploads/' . $idUsuari['id_usuari'] . '/'. $dirInfo['nomCarpeta'];
            //$fileName = __DIR__ . '/../../public/uploads/default-avatar.jpg';

            if (!file_exists($fileName)) {
                // Return error
                die('file not exist');
            }


            /*if (file_exists('./uploads/default-avatar.jpg')) {
            //if (file_exists($fileName)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                //header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
                header('Content-Disposition: attachment; filename="' . basename('./uploads/default-avatar.jpg') . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize('./uploads/default-avatar.jpg'));
                readfile('./uploads/default-avatar.jpg');
                exit;
            }*/

            //$file = __DIR__ . '/test.html';
            //$file = './uploads/default-avatar.jpg';
            $fh = fopen($fileName, 'r');

            $stream = new \Slim\Http\Stream($fh); // create a stream instance for the response body

            return $response->withHeader('Content-Type', 'application/force-download')
                ->withHeader('Content-Type', 'application/octet-stream')
                ->withHeader('Content-Type', 'application/download')
                ->withHeader('Content-Description', 'File Transfer')
                ->withHeader('Content-Transfer-Encoding', 'binary')
                ->withHeader('Content-Disposition', 'attachment; filename="' . basename($fileName) . '"')
                ->withHeader('Expires', '0')
                ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->withHeader('Pragma', 'public')
                ->withBody($stream); // all stream contents will be sent to the response

        }
    }
}