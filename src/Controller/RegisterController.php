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
use PwBox\Model\User;
use Slim\Http\UploadedFile;

class RegisterController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){
        return $this->container->get('view')->render($response, 'register.twig', []);
    }

    //Amb aquesta funcio obtenim les dades del request i despres de comprovar, guradem les dades.

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function submit (Request $request, Response $response) {
        try{
            $data = $request->getParsedBody();

            $errors = [];

            //Validate
            if (isset($data['inputEmail']) && filter_var($data['inputEmail'], FILTER_VALIDATE_EMAIL)) {

            } else {
                $errors['errorEmail'] = 'The email is not valid';
            }

            if (isset($data['inputUsername']) && strlen($data['inputUsername']) <= 20 && !preg_match("[^A-Za-z0-9]", $data['inputUsername'])) {

            } else {
                $errors['errorUsername'] = 'The username is not valid' ;
            }

            if (isset($data['inputBirthDay']) && isset($data['inputMonthBirth']) && isset($data['inputBirthYear']) && $this->validateDay($data['inputBirthDay'], $data['inputMonthBirth'])) {

            } else {
                $errors['errorBirth'] = 'This birthdate is invalid';
            }

            if (isset($data['inputPassword'])) {

                if (strlen($data['inputPassword']) < 6 || strlen($data['inputPassword']) > 12){
                    $errors['errorPasswordLength'] = 'Password length must be between 6 and 12 characters';
                }
                if (strtolower($data['inputPassword']) != $data['inputPassword'] && strtoupper($data['inputPassword']) != $data['inputPassword']){

                } else {
                    $errors['errorPasswordCase'] = 'Password must have one lowercase and one uppercase';
                }

            } else {
                $errors['errorPassword'] = 'Password is missing';
            }

            if (isset($data['inputPasswordConf'])) {
                 if ($data['inputPasswordConf'] != $data['inputPassword']){
                     $errors['errorPasswordMismatch'] = 'The passwords don\'t match';
                 }
            } else {
                $errors['errorPasswordConf'] = 'Password confirmation is missing';
            }

            //Comprovar que la imatge no sigui més gran de 500kB
            $uploadedFiles = $request->getUploadedFiles();
            if(isset($uploadedFiles)){
                if (isset($uploadedFiles['inputProfileImage'])){

                    $uploadedFile = $uploadedFiles['inputProfileImage'];

                    if ($uploadedFile->getSize() >= 500000){
                        $errors['errorProfilePicture'] = "Profile picture size must be less than 500KB";
                    }

                }

            }

            //var_dump($data);

            if(sizeof($errors) == 0) {

                //Registrar l'usuari (ARREGLAR)
                $service = $this->container->get('user_repository');

                $now = new \DateTime('now');

                $id = $service->save(
                     new User(null,
                         $data['inputUsername'],
                         $data['inputEmail'],
                         $data['inputPassword'],
                         $data['inputBirthDay'],
                         $data['inputMonthBirth'],
                         $data['inputBirthYear'],
                         $now,
                         $now)
                 );

                //Moure la seva imatge de perfil al directori que toca
                if (isset($uploadedFile)) {
                    $directory = __DIR__ . '/../../public/uploads';
                    $this->moveUploadedImage($directory, $uploadedFile, $id);
                }

                //Iniciar una cookie

                //Portar l'usuari a la seva home
                return $this->container->get('view')->render($response, 'login.twig', []);
            } else {
                return $this->container->get('view')->render($response, 'register.twig', ['error_array' => $errors, 'lastingData' => $data]);
            }

        }catch(\Exception $e){
            //$response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write('Something went wrong');
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write($e->getMessage());

            //posar e.getMessage()
        }
        return $response;
    }

    private function validateDay($day, $month){

        if($month == "January" || $month == "March" || $month == "May" || $month == "July" || $month == "August" || $month == "October" || $month == "December"){
            if($day > 31 || $day < 1){
               return false;
            }
        } else if ($month =="February"){
            if ($day > 28 || $day < 1){
                return false;
            }
        } else {
            if ($day > 30 || $day < 1){
                return false;
            }
        }
        return true;
    }

    private function moveUploadedImage($directory, UploadedFile $uploadedFile, int $id)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $filename = sprintf('%s.%0.8s', $id, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

}