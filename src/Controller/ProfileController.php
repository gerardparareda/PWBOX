<?php

namespace PwBox\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

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
        $activated = $repo->getActivationById($_COOKIE['user_id']);

        return $this->container->get('view')->render($response, 'profile.twig', ['activated' => $activated, 'user_avatar' => $result[0], 'username' => $username, 'email' => $email]);
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

        $data = $request->getParsedBody();
        $repo = $this->container->get('user_repository');


        if($data['inputNewEmail'] != '') {
            if (filter_var($data['inputNewEmail'], FILTER_VALIDATE_EMAIL)) {

            } else {
                $errors['errorEmail'] = 'Invalid email';
            }
        }


        if($data['inputNewPassword'] != '') {
            if (strlen($data['inputNewPassword']) < 6 || strlen($data['inputNewPassword']) > 12) {
                $errors['errorNewPassword'] = 'Password length must be between 6 and 12 characters';
            }
            if (strtolower($data['inputNewPassword']) != $data['inputNewPassword'] && strtoupper($data['inputNewPassword']) != $data['inputNewPassword']) {

            } else {
                $errors['errorNewPassword'] = 'Password must have one lowercase and one uppercase';
            }
            if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $data['inputNewPassword'])) {

            } else {
                $errors['errorNewPassword'] = 'Password must contain at least one number and one letter';
            }

        }

        if($data['inputOldPassword'] != '') {
            $old_password = $repo->getPasswordByID($_COOKIE['user_id']);
            if(md5($data['inputOldPassword']) != $old_password){
                $errors['errorOldPassword'] = 'Incorrect password';
            }
        }else{
            $errors['errorOldPassword'] = 'Introduce your old password!';
        }

        $uploadedFiles = $request->getUploadedFiles();

        if (sizeof($uploadedFiles) > 0){
            $uploadedImage = $uploadedFiles['inputNewProfileImage'];
        }

        if(isset($uploadedImage)){
            if ($uploadedImage->getSize() >= 500000){
                $errors['errorProfilePicture'] = "Profile picture size must be less than 500KB";
            }
        }

        if($errors['errorEmail'] == '' && $errors['errorNewPassword'] == '' && $errors['errorOldPassword'] == '' && $errors['errorNewProfileImage'] == '') {

            //Canviar el correu
            if ($data['inputNewEmail'] != ''){
                $repo->updateEmailById($data['inputNewEmail'], $_COOKIE['user_id']);

            }

            //Canviar la contrasenya
            if ($data['inputNewPassword'] != ''){
                $repo->updatePasswordById($data['inputNewPassword'], $_COOKIE['user_id']);
            }

            //Canviar la foto de perfil
            if(isset($uploadedImage)) {
                $directory = './uploads';
                $imageName = $this->moveUploadedImage($directory, $uploadedImage, $_COOKIE['user_id']);
            } else {
                $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");
                if(count($result) == 1){
                    $imageName = basename($result[0]);
                } else {
                    $imageName = 'default-avatar.jpg';
                }
            }

            $response_array['image'] = $imageName;
            $response_array['errors'] = $errors;
            $response_array['newEmail'] = $data['inputNewEmail'];
            return $response->withJson($response_array, 200);
        }else{

            $response_array['errors'] = $errors;
            return $response->withJson($response_array, 200);

        }

    }

    private function moveUploadedImage($directory, $uploadedImage, int $id)
    {

        $result = glob ("./uploads/" . $_COOKIE['user_id'] . ".*");
        if(count($result) == 1){
            unlink($result[0]);
        }

        $extension = pathinfo($uploadedImage->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%0.8s', $id, $extension);
        $uploadedImage->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}