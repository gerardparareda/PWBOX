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

$app->get('/sharedFile/{id}', 'PwBox\Controller\SharedDashboardController:downloadFile'); //->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/upload_files[/{parent}]', 'PwBox\Controller\DashboardController:upload')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');
//
$app->get('/logout', 'PwBox\Controller\LogoutController:logout');

$app->get('/profile', 'PwBox\Controller\ProfileController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->get('/deleteprofile', 'PwBox\Controller\ProfileController:deleteProfile')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/profile', 'PwBox\Controller\ProfileController:editProfile');

$app->get('/forbidden', 'PwBox\Controller\ForbiddenController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/renameFolder', 'PwBox\Controller\RenameFolderController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/renameSharedFolder', 'PwBox\Controller\RenameFolderController:renameShared')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->get('/sendEmail', 'PwBox\Controller\EmailSenderController:sendEmail');

$app->get('/sharedDashboard[/{path}]', 'PwBox\Controller\SharedDashboardController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/newItem', 'PwBox\Controller\NewFolderController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/removeFolder', 'PwBox\Controller\RemoveFolderController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->post('/removeSharedFolder', 'PwBox\Controller\RemoveFolderController:removeShared')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

//$app->get('/removeFolder/{id}', 'PwBox\Controller\RemoveFolderController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->get('/notifications', 'PwBox\Controller\NotificationController')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

$app->get('/emailActivate/{activatorId}', 'PwBox\Controller\EmailActivatorController');

$app->post('/shareFolder', 'PwBox\Controller\DashboardController:shareFolder')->add('PwBox\Controller\Middleware\UserLoggedMiddleware');

?>