<?php

//Aquí van els middlewares
$app->get('/hello/{name}', 'PwBox\Controller\HelloController'); //->add('PwBox\Controller\Middleware\TestMiddleware');
$app->post('/user', 'PwBox\Controller\PostUserController');

$app->get('/', 'PwBox\Controller\LandingController')->add('PwBox\Controller\Middleware\UserNotLoggedMiddleware');

$app->get('/register', 'PwBox\Controller\RegisterController')->add('PwBox\Controller\Middleware\UserNotLoggedMiddleware');

$app->post('/register', 'PwBox\Controller\RegisterController:submit');
//$app->post('/register', 'PwBox\Controller\PostUserController');

$app->get('/login', 'PwBox\Controller\LoginController')->add('PwBox\Controller\Middleware\UserNotLoggedMiddleware');

$app->post('/login', 'PwBox\Controller\LoginController:submit');

$app->get('/dashboard', 'PwBox\Controller\DashboardController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');
$app->post('/upload_files', 'PwBox\Controller\DashboardController:upload')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');
//
$app->get('/logout', 'PwBox\Controller\LogoutController:logout');

$app->get('/profile', 'PwBox\Controller\ProfileController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

?>