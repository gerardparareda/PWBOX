<?php

//S'ha de posar middleware als posts?

$app->post('/user', 'PwBox\Controller\PostUserController');

$app->get('/', 'PwBox\Controller\LandingController')->add('PwBox\Controller\Middleware\UserNotLoggedMiddleware');

$app->get('/register', 'PwBox\Controller\RegisterController')->add('PwBox\Controller\Middleware\UserNotLoggedMiddleware');

$app->post('/register', 'PwBox\Controller\RegisterController:submit');

$app->get('/login', 'PwBox\Controller\LoginController')->add('PwBox\Controller\Middleware\UserNotLoggedMiddleware');

$app->post('/login', 'PwBox\Controller\LoginController:submit');

//$app->get('/dashboard', 'PwBox\Controller\DashboardController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');
$app->get('/dashboard[/{path}]', 'PwBox\Controller\DashboardController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->get('/file/{id}', 'PwBox\Controller\DashboardController:downloadFile'); //->add('PwBox\Controller\Middleware\UserLoggedMiddleware');
//$app->get('/aaa', 'PwBox\Controller\DashboardController:downloadFile')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/upload_files', 'PwBox\Controller\DashboardController:upload')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');
//
$app->get('/logout', 'PwBox\Controller\LogoutController:logout');

$app->get('/profile', 'PwBox\Controller\ProfileController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/profile', 'PwBox\Controller\ProfileController:editProfile');

$app->get('/forbidden', 'PwBox\Controller\ForbiddenController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/renameFolder', 'PwBox\Controller\RenameFolderController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');



?>