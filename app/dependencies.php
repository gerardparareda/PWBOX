<?php
$container = $app->getContainer();

$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('../src/view/templates', []);

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));

    return $view;
};

$container['doctrine'] = function ($container){
    $config = new \Doctrine\DBAL\Configuration();
    $conn = \Doctrine\DBAL\DriverManager::getConnection(
        $container->get('settings')['database'],
        $config
    );

    return $conn;
};

$container['user_repository'] = function ($container){
    $repository = new PwBox\Model\Implementation\DoctrineUserRepository(
        $container->get('doctrine')
    );
    return $repository;
};

$container['post_user_use_case'] = function ($container){
    $useCase = new PwBox\Model\UseCase\PostUserUseCase(
        $container->get('user_repository')
    );
    return $useCase;
};