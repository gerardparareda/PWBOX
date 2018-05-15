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
            if (isset($data['inputEmail']) && filter_var($data['inputEmail'], FILTER_VALIDATE_EMAIL)) {

            } else {
                $errors['errorEmail'] = 'El camp email no és correcte ';
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
                if ($repo->lookFor(new User(null, null, $data['inputEmail'], $data['inputPassword'], null, null, null, null, null))) {


                    return $this->container->get('view')->render($response, 'dashboard.twig', []);
                } else {
                    $errors['userNotFound'] = 'Usuari o contrasenya incorrectes. ';
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