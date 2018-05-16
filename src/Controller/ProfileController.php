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

    public function editProfile(Request $request, Response $response){

        //Moure la seva imatge de perfil al directori que toca
        if (isset($uploadedFile)) {
            $directory = __DIR__ . '/../../public/uploads';
            $this->moveUploadedImage($directory, $uploadedFile, $_COOKIE['user_id']);
        }

    }

    private function moveUploadedImage($directory, UploadedFile $uploadedFile, int $id)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%0.8s', $id, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }
}