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

class RegisterController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){
        return $this->container->get('view')->render($response, 'register.twig', []);
    }

    //Amb aquesta funcio obtenim les dades del request i despres de comprovar, guradem les dades.
    public function submit (Request $request, Response $response) {
        try{
            $data = $request->getParsedBody();
            //var_dump($data);

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

            return $this->container->get('view')->render($response, 'register.twig', ['error_array' => $errors, 'lastingData' => $data]);

        }catch(\Exception $e){
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write('Something went wrong');
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

}