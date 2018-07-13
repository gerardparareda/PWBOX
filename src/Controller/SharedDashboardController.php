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

                        //$idCarpetaParent = $repo->getParentFolderId($args['path']);

                        //$isAdmin = $repo->isAdmin($idCarpetaParent, $_COOKIE['user_id']);
                        //$carpetes = $repo->showDirectory($idCarpetaParent, $idUsuari);


                        return $this->container->get('view')->render($response, 'sharedDashboard.twig',
                            ['user_avatar' => $result[0], 'carpetes' => $fitxers, 'isAdmin' => $privileges['admin']]);
                    } else {

                        return $this->container->get('view')->render($response, 'sharedDashboard.twig',
                            ['user_avatar' => $result[0], 'carpetes' => $fitxers, 'carpetaParent' => $carpeta['id'], 'isAdmin' => $privileges['admin']]);

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

    public function upload(Request $request, Response $response, array $args)
    {
        $uploadedFiles = $request->getUploadedFiles();
        $errors = [];

        $repo = $this->container->get('user_repository');
        $id_parent = $args['parent'];
        $proprietari_id = $repo->getUserIdByDirectoryId($id_parent);
        $proprietari_id = $proprietari_id['id_usuari'];

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

                    } else if($uploadedFile->getSize() + $this->getDirectorySize($proprietari_id) > 1000000000){
                        $errors['errorFilled'] = "You reached your drive unit size of 1GB";
                        break;
                    }
                }
            }

            if (count($errors) == 0) {
                if (isset($uploadedFiles['inputFiles'])) {

                    foreach ($uploadedFiles['inputFiles'] as $uploadedFile) {

                        $directory = __DIR__ . '/../../public/uploads/' . $proprietari_id;
                        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename());
                        $repo->createDirectory($uploadedFile->getClientFilename(), $args['parent'], $proprietari_id, false);

                    }
                }
                return $response->withStatus(302)->withHeader("Location", "/dashboard");
            } else {

                $repo = $this->container->get('user_repository');

                $rootFolderId = $repo->getRootFolderId($proprietari_id);

                //$carpetes = $repo->showDirectory($rootFolderId, $_SESSION['user_id']);
                $carpetes = $repo->showDirectory($rootFolderId, $proprietari_id);

                return $this->container->get('view')->render($response, 'sharedDashboard.twig', ['error_array' => $errors, 'carpetes' => $carpetes, 'carpetaParent' => null]);
            }

        } catch (\Exception $e) {
            //$response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write('Something went wrong');
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write($e->getMessage());
        }
        return $response;
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

    private function GetDirectorySize($id_propietari){
        $path = __DIR__ . '/../../public/uploads/' . $id_propietari;
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

}