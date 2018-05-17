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

        $data = $request->getParsedBody();

        if(isset($data['inputNewEmail'])) {
            if($data['inputNewEmail'] != '') {
                if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {

                } else {
                    $errors['errorEmail'] = 'Invalid email';
                }
            }
        }

        if(isset($data['inputNewPassword'])){
            if($data['inputNewPassword'] == '') {

            }else {
                if (strlen($data['inputNewPassword']) < 6 || strlen($data['newPassword']) > 12) {
                    $errors['inputNewPassword'] = 'Password length must be between 6 and 12 characters';
                }
                if (strtolower($data['inputNewPassword']) != $data['inputNewPassword'] && strtoupper($data['inputNewPassword']) != $data['inputNewPassword']) {

                } else {
                    $errors['errorNewPassword'] = 'Password must have one lowercase and one uppercase';
                }
            }
        }

        if($data['inputOldPassword'] != '') {

            //TODO: Fer una crida a la base de dades

        }else{
            $errors['errorOldPassword'] = 'Introduce your old password';
        }

        $uploadedImage = $request->getUploadedFiles()['inputNewProfileImage'];

        if(isset($uploadedImage)){
            if ($uploadedImage->getSize() >= 500000){
                $errors['errorProfilePicture'] = "Profile picture size must be less than 500KB";
            }
        }

        if($errors['errorOldPassword'] == ''){
            $directory = './uploads';
            $imageName = $this->moveUploadedImage($directory, $uploadedImage, $_COOKIE['user_id']);
        }

        if($errors['errorEmail'] == '' && $errors['errorNewPassword'] == '' && $errors['errorOldPassword'] == '' && $errors['errorNewProfileImage'] == '') {
            $response_array['errors'] = $errors;
            $response_array['image'] = $imageName;
            return $response->withJson($response_array, 200);
        }else{
            $response_array['errors'] = $errors;
            return $response->withJson($response_array, 500);

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