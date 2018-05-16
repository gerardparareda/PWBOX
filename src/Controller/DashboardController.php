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

    public function __invoke(Request $request, Response $response, array $args){

        $repo = $this->container->get('user_repository');

        $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");

        if(count($result) == 0){
            $result[0] = "./uploads/default-avatar.jpg";
        }

        return $this->container->get('view')->render($response, 'dashboard.twig', ['user_avatar' => $result[0]]);
    }

    public function upload(Request $request, Response $response)
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

                    }
                }
            }

            if (count($errors) == 0) {
                if (isset($uploadedFiles['inputFiles'])) {
                    foreach ($uploadedFiles['inputFiles'] as $uploadedFile) {
                        $directory = __DIR__ . '/../../public/uploads/' . $_COOKIE['user_id'];

                        //Comprovar si la carpeta existeix i si no crear-la
                        if (!file_exists($directory)) {
                            mkdir($directory, 0777, true);
                        }

                        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $uploadedFile->getClientFilename());

                    }
                }
                return $response->withStatus(302)->withHeader("Location", "/dashboard");
            } else {
                return $this->container->get('view')->render($response, 'dashboard.twig', ['error_array' => $errors]);
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
}