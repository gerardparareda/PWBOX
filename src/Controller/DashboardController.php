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

class DashboardController{
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

        $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");

        if(count($result) == 0){
            $result[0] = "/../uploads/default-avatar.jpg";
        }

        //Comprovem si l'usuari ha entrat amb un path a una carpeta o no. Nomes entra aqui si te la cookie perque
        //hi ha el middleware d'abans que mira si hi ha cookie.
        if (empty($args)) {
            //L'usuari ha entrat a la dashboard general, carpeta root.

            //Busquem que hi ha a la carpeta.
            $rootFolderId = $repo->getRootFolderId($_COOKIE['user_id']);

            //$carpetes = $repo->showDirectory($rootFolderId, $_SESSION['user_id']);
            $carpetes = $repo->showDirectory($rootFolderId, $_COOKIE['user_id']);
            return $this->container->get('view')->render($response, 'dashboard.twig', ['user_avatar' => $result[0], 'carpetes' =>$carpetes, 'carpetaParent' => $rootFolderId, 'usedSpace' => (int)($this->GetDirectorySize()/1000000000)]);

        } else {
            //Carpeta concreta

            //Comprovem si la carpeta existeix a la bbdd.

            $carpeta = $repo->lookIfDirectoryExists($args['path']);

            if ($carpeta == null) {
                return $response->withStatus(302)->withHeader("Location", "/dashboard");

            } else {

                $idUsuari = $_COOKIE['user_id'];

                $privileges = $repo->userPrivileges($carpeta['id'], $idUsuari);

                if ($privileges['admin'] || $privileges['reader']) {

                    //Mostrem el contingut de la carpeta.
                    $carpetes = $repo->showDirectory($carpeta['id'], $idUsuari);

                    if (!$repo->esCarpeta($args['path'])) {

                        $idCarpetaParent = $repo->getParentFolderId($args['path']);
                        $carpetes = $repo->showDirectory($idCarpetaParent, $idUsuari);

                        //downloadFileFromURL($args['path']);

                        return $this->container->get('view')->render($response, 'dashboard.twig',
                            ['user_avatar' => $result[0], 'carpetes' => $carpetes, 'carpetaParent' => $carpeta['id'], 'usedSpace' => (int)($this->GetDirectorySize()/1000000000)]);

                    } else {

                        return $this->container->get('view')->render($response, 'dashboard.twig',
                            ['user_avatar' => $result[0], 'carpetes' => $carpetes, 'carpetaParent' => $carpeta['id'], 'usedSpace' => (int)($this->GetDirectorySize()/1000000000)]);
                    }


                } else {
                    return $response->withStatus(302)->withHeader("Location", "/forbidden");
                }
            }
        }

        //return $this->container->get('view')->render($response, 'dashboard.twig', ['user_avatar' => $result[0]]);
    }

    public function upload(Request $request, Response $response, array $args)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $errors = [];

        try {
            if (isset($uploadedFiles['inputFiles'])) {

                foreach ($uploadedFiles['inputFiles'] as $uploadedFile) {


                    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);

                    if (!$this->validate_extension($extension)){
                        $errors['errorFileType'] = "Unknown file type! Only .pdf, .txt, .md, .jpg, .jpeg, .gif, .png accepted";
                        break;
                    }

                    if ($uploadedFile->getSize() >= 2000000) {
                        $errors['errorFileSize'] = "Each file size must be less than 2MB";
                        break;

                    } else if($uploadedFile->getSize() + $this->getDirectorySize() > 1000000000){
                        $errors['errorFilled'] = "You reached your drive unit size of 1GB";
                        break;
                    }
                }
            }

            if (count($errors) == 0) {
                if (isset($uploadedFiles['inputFiles'])) {
                    foreach ($uploadedFiles['inputFiles'] as $uploadedFile) {
                        $directory = __DIR__ . '/../../public/uploads/' . $_COOKIE['user_id'];

                        //Comprovar si la carpeta existeix i si no crear-la
                        /*if (!file_exists($directory)) {
                            mkdir($directory, 0777, true);
                        }*/

                        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename());

                        $repo = $this->container->get('user_repository');

                        $repo->createDirectory($uploadedFile->getClientFilename(), $args['parent'], $_COOKIE['user_id'], false);

                    }
                }
                return $response->withStatus(302)->withHeader("Location", "/dashboard");
            } else {

                $repo = $this->container->get('user_repository');

                $rootFolderId = $repo->getRootFolderId($_COOKIE['user_id']);

                //$carpetes = $repo->showDirectory($rootFolderId, $_SESSION['user_id']);
                $carpetes = $repo->showDirectory($rootFolderId, $_COOKIE['user_id']);

                return $this->container->get('view')->render($response, 'dashboard.twig', ['error_array' => $errors, 'carpetes' => $carpetes, 'carpetaParent' => null]);
            }

        } catch (\Exception $e) {
            //$response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write('Something went wrong');
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write($e->getMessage());
        }
        return $response;
    }

    private function GetDirectorySize(){
        $path = __DIR__ . '/../../public/uploads/' . $_COOKIE['user_id'];
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }


    private function validate_extension($fileExtension){
        $filetypes = ['png', 'jpg', 'jpeg', 'gif', 'pdf', 'txt', 'md'];
        foreach ($filetypes as $filetype) {
            if ($fileExtension == $filetype) {
                return true;
            }
        }
        return false;
    }

    /*public function hola (Request $request, Response $response) {
        echo 'holaaa';
    }*/

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

            $fileName = __DIR__ . '/../../public/uploads/' . $_COOKIE['user_id'] . '/' . $dirInfo['nomCarpeta'];
            //$fileName = __DIR__ . '/../../public/uploads/default-avatar.jpg';

            if (!file_exists($fileName)) {
                // Return error
                die('file not exist');
            }

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

    public function shareFolder(Request $request, Response $response, array $args){

        $data = $request->getParsedBody();

        $repo = $this->container->get('user_repository');


        $result_query = $repo->getIdByUsername($data['userShare']);

        if (count($result_query) > 0){

            $user_id = $result_query[0]['id'];

            if ($data['admin'] == 'false') {
                $admin = false;
            } else {
                $admin = true;
            }

            $repo->share($user_id, $data['idCarpeta'], $admin);

            $response_array['message'] = 'Carpeta compartida correctament';

            return $response->withJson($response_array, 200);

        } else {

            $response_array['message'] = "L'usuari no existeix!";

            return $response->withJson($response_array, 200);

        }
    }
}