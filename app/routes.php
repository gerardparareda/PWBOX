<?php

//Aquí van els middlewares
$app->get('/hello/{name}', 'PwBox\Controller\HelloController')->add('PwBox\Controller\Middleware\TestMiddleware');
$app->post('/user', 'PwBox\Controller\PostUserController');

?>