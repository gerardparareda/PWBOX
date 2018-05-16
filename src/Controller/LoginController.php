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
use PwBox\Model\UserRepository;

class LoginController{
    protected $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args){

        if(isset($_COOKIE['inputRememberMe']) && isset($_COOKIE['inputEmail']) && isset($_COOKIE['inputPassword'])){
            return $this->container->get('view')->render($response, 'login.twig', ['inputRememberMe' => $_COOKIE['inputRememberMe'], 'inputEmail' => $_COOKIE['inputEmail'], 'inputPassword' => $_COOKIE['inputPassword']]);
        }
        return $this->container->get('view')->render($response, 'login.twig', []);
    }

    //Amb aquesta funcio sabem si les dades son correctes i si existeix l'usuari a la bbdd.

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
            $rejex = '/@/';
            preg_match($rejex, $data['inputEmail'], $matches, PREG_OFFSET_CAPTURE);

            //var_dump($matches);

            if (!empty($matches)) {

                //Si entra vol dir que no ha trobat una arroba, i per tant que es un email, fem comprovacions email.
                if (isset($data['inputEmail']) && filter_var($data['inputEmail'], FILTER_VALIDATE_EMAIL)) {

                } else {
                    $errors['errorEmail'] = 'El camp email no és correcte ';
                }

                $email = true;


            } else {

                //Si entra aqui vol dir que no ha trobat una arroba i per tant es un username, fem validacions.
                if (isset($data['inputEmail']) && strlen($data['inputEmail']) <= 20 && !preg_match("[^A-Za-z0-9]", $data['inputEmail'])) {

                } else {
                    $errors['errorUsername'] = 'The username is not valid' ;
                }

                $email = false;


            }


            if (isset($data['inputPassword'])) {

                if (strlen($data['inputPassword']) < 6 || strlen($data['inputPassword']) > 12){
                    $errors['errorPasswordLength'] = 'El password és incorrecte';
                }
                if (strtolower($data['inputPassword']) != $data['inputPassword'] && strtoupper($data['inputPassword']) != $data['inputPassword']){

                } else {
                    $errors['errorPasswordCase'] = 'El password és incorrecte';
                }

            } else {
                $errors['errorPassword'] = 'El camp password no pot estar buit';
            }

            if (empty($errors)) {
                //Comprovar
                /** @var UserRepository $repo */
                $repo = $this->container->get('user_repository');

                if ($email) {

                    $nouUser = new User(null, null, $data['inputEmail'], $data['inputPassword'], null, null, null, null, null);

                } else {

                    $nouUser = new User(null, $data['inputEmail'], null, $data['inputPassword'], null, null, null, null, null);
                }

                if ($repo->lookFor($nouUser)) {

                    //Començar cookie
                    session_start();

                    setcookie("user_id", $repo->getUserId($nouUser),time() + 15778463);

                    if(isset($data['inputRememberMe'])){
                        setcookie("inputEmail", $data['inputEmail'],time() + 15778463);
                        setcookie("inputPassword", $data['inputPassword'],time() + 15778463);
                        setcookie("inputRememberMe", 'checked',time() + 15778463);
                    } else {
                        if(isset($_COOKIE['inputEmail']) && isset($_COOKIE['inputPassword'])){
                            setcookie("inputEmail", 'nein',1);
                            setcookie("inputPassword", 'nein',1);
                        }
                    }

                    return $this->container->get('view')->render($response, 'dashboard.twig', []); //El render
                    //L'altre rediracció no funciona
                } else {
                    $errors['userNotFound'] = 'Usuari o contrasenya incorrectes.';
                }
            }

            return $this->container->get('view')->render($response, 'login.twig', ['error_array' => $errors]); //El render
            // llegeix el login.twig, interpreta el codi, variables, etc. substitueix i retorna la web ja feta,
            // posteriorment mostrem la web que ha retornat.

        }catch(\Exception $e){
            $response = $response->withStatus(500)->withHeader('Content-type', 'text/html')->write($e->getMessage());
        }
        return $response;
    }

}