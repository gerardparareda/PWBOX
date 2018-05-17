<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ProfileController{

    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(Request $request, Response $response){

        $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");

        $repo = $this->container->get('user_repository');

        if(count($result) == 0){
            $result[0] = "./uploads/default-avatar.jpg";
        }

        $username = $repo->getUsernameById($_COOKIE['user_id']);
        $email = $repo->getEmailById($_COOKIE['user_id']);

        return $this->container->get('view')->render($response, 'profile.twig', ['user_avatar' => $result[0], 'username' => $username, 'email' => $email]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function editProfile(Request $request, Response $response){

        $errors = [];
        $errors['errorEmail'] = '';
        $errors['errorNewPassword'] = '';
        $errors['errorOldPassword'] = '';
        $errors['errorNewProfileImage'] = '';

        if(isset($_POST['email'])) {
            if($_POST['email'] != '') {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {

                } else {
                    $errors['errorEmail'] = 'Invalid email';
                }
            }
        }

        if(isset($_POST['newPassword'])){
            if($_POST['newPassword'] == '') {

            }else {
                if (strlen($_POST['newPassword']) < 6 || strlen($_POST['newPassword']) > 12) {
                    $errors['errorNewPassword'] = 'Password length must be between 6 and 12 characters';
                }
                if (strtolower($_POST['newPassword']) != $_POST['newPassword'] && strtoupper($_POST['newPassword']) != $_POST['newPassword']) {

                } else {
                    $errors['errorNewPassword'] = 'Password must have one lowercase and one uppercase';
                }
            }
        }

        if(isset($_POST['oldPassword'])){
            if($_POST['oldPassword'] != '') {

                //TODO: Fer una crida a la base de dades

            }else{
                $errors['errorOldPassword'] = 'Introduce your old password';
            }
        } else {
            $errors['errorOldPassword'] = 'Introduce your old password';
        }


        /*if(isset($_POST['newProfileImage'])){

            if($_POST['newProfileImage'] != ''){


            }

        }*/

        if($errors['errorOldPassword'] != '') {
            $response_array['errors'] = $errors;
            $response_array['status'] = 'failure';
        }else{
            $response_array['errors'] = $errors;
            $response_array['status'] = 'success';
        }

        header('Content-type: application/json');
        echo json_encode($response_array);

    }

    private function moveUploadedImage($directory, UploadedFile $uploadedFile, int $id)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%0.8s', $id, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}