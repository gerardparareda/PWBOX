<?php

//Aquí van els middlewares
$app->get('/hello/{name}', 'PwBox\Controller\HelloController'); //->add('PwBox\Controller\Middleware\TestMiddleware');
$app->post('/user', 'PwBox\Controller\PostUserController');

$app->get('/', 'PwBox\Controller\LandingController');

$app->get('/register', 'PwBox\Controller\RegisterController');

$app->post('/register', 'PwBox\Controller\RegisterController:subtmit');

$app->get('/login', 'PwBox\Controller\LoginController');

$app->post('/login', 'PwBox\Controller\LoginController:subtmit');

?>